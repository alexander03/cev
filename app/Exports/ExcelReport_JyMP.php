<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Concesionaria;
use App\Equipo;
use App\Tipohora;
use App\Piepagina;
use App\Controldiario;
use DateTime;
use Exception;
use DB;

class ExcelReport_JyMP extends ExcelHelper implements FromView 
{
    use Exportable;


    private $start_date, $end_date, $equipo_ids, $periodo, $fechas;

    public function __construct(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo, array $fechas)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->equipo_ids = $equipo_ids;
        $this->periodo = $periodo;
        $this->fechas = $fechas;
    }


    private function loadData()
    {
        $concesionaria = Concesionaria::getConcesionariaActual();

        // Parte 1
        $this->nextCell()->add('MEDICIÓN DE ALQUILER DE EQUIPOS', ['colspan' => 12]);
        
        // Parte 2
        $equipo = Equipo::whereIn('id',$this->equipo_ids)->first();
        $this->nextCell(2)->add([['CONCESIONARIA:', $concesionaria->razonsocial]]);
        $this->nextCell()->add([['SUBCONTRATISTA:', $equipo->contratista->razonsocial]]);
        $this->nextCell()->add([['PERIODO:', '(' . $this->start_date->format('d/m/Y') . ' AL ' . $this->end_date->format('d/m/Y') . ')']]);

        // Parte 3
        $this->nextCell(2);
        $this->add('CODIGO', ['rowspan' => 3]);
        $this->add('EQUIPO', ['rowspan' => 3]);
        $this->add('CANTIDAD (HORA)', ['rowspan' => 3]);
        $this->add('VALOR UNITARIO (US$)', ['rowspan' => 3]);
        $this->add('SUBTOTAL (US$)', ['rowspan' => 3]);
        $this->nextCol()->add('RESPONSABLE POR EQUIPO', ['rowspan' => 3]);

        $summaryEquipos = $this->getSummaryEquipos();
        $this->nextCell(4)->add($summaryEquipos);
        $letRows = count($summaryEquipos);

        // Parte 4
        $this->nextRow($letRows + 10);
        $listapiepagina = Piepagina::where('formato','like','Medicion Equipo Consolidado')->orderBy('orden','asc')->get();
        foreach($listapiepagina as $k=>$v){
            $this->nextCol()->add('_________________________________', ['colspan' => 4]);
        }
        $this->nextRow();
        foreach($listapiepagina as $k=>$v){
            $this->nextCol()->add($v->nombre, ['colspan' => 4]);
        }
        $this->nextRow();
        foreach($listapiepagina as $k=>$v){
            $this->nextCol()->add($v->cargo, ['colspan' => 4]);
        }
    }

    private function getMonths()
    {
        setlocale(LC_TIME, 'es_ES');

        $months = [];
        $month = new DateTime($this->start_date->format('Y-m-d'));

        while ( $month <= $this->end_date ) {
            $new_month['name'] = substr(strtolower(strftime('%b', $month->getTimestamp())), 0, 3);
            
            $days = [];
            $day = new DateTime($month->format('Y-m-d'));
            while ( $day->format('m') == $month->format('m') && $day <= $this->end_date ) {
                $days[] = $day->format('d');
                $day->modify('+1 day');
            }
            
            $new_month['days'] = $days;
            $month = new DateTime($day->format('Y-m-d'));
            // $date = new DateTime(date('Y-m-d', strtotime($date->format('Y-m-d') . ' + 1 months')));

            $months[] = $new_month;
        }

        return $months;
    }

    private function getSummaryEquipos(): array
    {
        // Traigo las fechas formateadas para uso rapido
        $startDate = $this->start_date->format('Y-m-d');
        $endDate = $this->end_date->format('Y-m-d');
        $listPeriodo = $this->fechas;
        $periodo = $this->periodo;
        
        // Consigo todos los ids de tipos de hora mantenimiento
        $thMantenimiento_ids = Tipohora::select('id')->where('descripcion', 'like', '%mantenimiento%')->get();
        
        // variable de respuesta
        $table = []; $subtotal = 0;

        foreach ($this->equipo_ids as $equipo_id) {
            $equipo = Equipo::join('ua', 'ua.id', '=', 'equipo.ua_id')
                            ->join('controldiario', 'controldiario.equipo_id', '=', 'equipo.id')
                            ->select('ua.codigo', 'equipo.descripcion', DB::raw('COUNT(controldiario.id) AS dias_permanencia'))
                            ->where('equipo.id', '=', $equipo_id)
                            ->where('controldiario.fecha', '>=', $startDate)
                            ->where('controldiario.fecha', '<=', $endDate)
                            ->groupBy('ua.codigo', 'equipo.descripcion')->first();

            $codigo = $equipo['codigo'];
            $descripcion = $equipo['descripcion'];
            //$dias_permanencia = $equipo['dias_permanencia'];
            $dias_permanencia = date_diff($this->end_date,$this->start_date)->format('%a')+1;
            $equipo = Equipo::find($equipo_id);

             if($equipo['unidad_id']=="6"){//HORAS
                // Horas trabajadas
                $a = Controldiario::select(DB::raw('SUM(hora_total) AS a'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)->first()['a'];
                $a = is_null($a) ? 0 : floatval($a);
    
                // Horas de mantenimiento
                $mantenimiento = Controldiario::join('tipohora','tipohora.id','=','controldiario.tipohora_id')
                                ->select(DB::raw('(case when tipohora.tipo="Horas de Mantenimiento" and SUM(hora_parada)>4 then 4 else sum(hora_parada) end) AS b'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where(function ($query) use ($thMantenimiento_ids) {
                                    foreach ($thMantenimiento_ids as $th) $query->orWhere('tipohora_id', '=', $th['id']);
                                })
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)
                                ->groupBy('fecha')->get();
                $b=0;
                foreach($mantenimiento as $k=>$v){
                    $b=$b+$v->b;
                }
                
                $b = is_null($b) ? 0 : floatval($b);
                // Horas minimas establecidas
                $c = round(($equipo->horas_min/$periodo) * intval($dias_permanencia), 2);
                //$c = round($equipo->horas_min,2);
    
                // Horas minimas por derecho
                $d = $c > $b ? $c - $b : 0;
    
                // Horas minimas a pagar
                $e = $d > $a ? $d - $a : 0;
    
                // Horas total a pagar
                $f = $a + $e;
            }else{//DIAS
                $dias_permanencia = $listPeriodo[$equipo_id];
                
                $a = count(Controldiario::select(DB::raw('count(fecha) AS a'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)
                                ->where('hora_total','>',0)
                                ->groupBy('fecha')
                                ->get());
                $a = is_null($a) ? 0 : floatval($a);
    
                // Horas de mantenimiento
                $mant = Controldiario::select(DB::raw('round((case when sum(hora_parada)>4 then 4 else sum(hora_parada) end)/( (case when sum(hora_parada)>4 then 4 else sum(hora_parada) end) + (select sum(x.hora_total) from controldiario as x where x.equipo_id='.$equipo_id.' and x.fecha=controldiario.fecha and x.deleted_at is null group by fecha)),2) AS b'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where(function ($query) use ($thMantenimiento_ids) {
                                    foreach ($thMantenimiento_ids as $th) $query->orWhere('tipohora_id', '=', $th['id']);
                                })
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)
                                ->groupBy('fecha')
                                ->get();
                $b=0;
                foreach($mant as $k=>$v){
                    $b=$b+$v->b;
                }         
                //calculo segun dias trabajados
                $a = $a - $b;
                // Horas minimas establecidas
                //$c = round(($equipo->horas_min/31) * intval($dias_permanencia), 2);
                $c = round($equipo->horas_min,2);
    
                // Horas minimas por derecho
                $d = $c > $b ? $c - $b : 0;
    
                // Horas minimas a pagar
                $e = $d > $a ? $d - $a : 0;
                
                // Dias Parados a Pagar
                $thMantenimiento_ids2 = Tipohora::select('id')->where('tipo', 'like', '%Horas Paradas%')->get();
                $g = count(Controldiario::select(DB::raw('count(fecha) AS b'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where(function ($query) use ($thMantenimiento_ids2) {
                                    foreach ($thMantenimiento_ids2 as $th) $query->orWhere('tipohora_id', '=', $th['id']);
                                })
                                ->whereNotIn('fecha',function ($query) use($equipo_id,$startDate,$endDate){
                                    $query->select('fecha')->from('controldiario')->where('equipo_id','=',$equipo_id)->where('hora_total','>',0)->where('fecha','>=',$startDate)->where('fecha','<=',$endDate);
                                })
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)
                                ->where('hora_total','=',0)
                                ->groupBy('fecha')
                                ->get());
                $g = is_null($g) ? 0 : floatval($g);
                
                $g = $dias_permanencia - $a;
    
                // Horas total a pagar
                $f = round(($a+$g)/$dias_permanencia,2);
            }

            // Valor unitario
            $valorUnitario = $equipo->precio;
            
            // Subtotal por equipo
            $subtotalEquipo = round($f * $valorUnitario, 2);

            // Responsable de equipo
            if(!is_null($equipo->ua->responsable)){
                $responsable = $equipo->ua->responsable->nombre;
            }else{
                $responsable = "";
            }

            // Calculo el subtotal general
            $subtotal += $subtotalEquipo;

            // Agrego las filas de la tabla de resumen de equipos
            $array = $this->formatTable([
                [$codigo, $descripcion, $f, $valorUnitario, $subtotalEquipo, '', $responsable]
            ], [ // le defino offset rows en 2 para que tenga una fila libre despues y trasposed en verdadero para que el array no salga vertical sino horizontal
                'oRows' => 2
            ]);

            // fusionamos el valor de $table con el de $array
            $table = array_merge($table, $array);
        }
        
        // Agrego las filas de la tabla de resumen de equipos
        $array = $this->formatTable([
            ['', '', '', 'SUBTOTAL', $subtotal, '', $responsable]
        ], [ // le defino offset rows en 2 para que tenga una fila libre despues y trasposed en verdadero para que el array no salga vertical sino horizontal
            'oRows' => 2
        ]);
        
        // fusionamos el valor de $table con el de $array
        $table = array_merge($table, $array);

        return $table;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view():View
    {
        $this->loadData();

        return view('app.controldiario.exports.excelReports.MedicionEquipos', [
            'data' => $this->data,
            'max_row' => $this->max_row,
            'max_col' => $this->max_col,
            'span_value' => $this->span_value,
            'free_value' => $this->free_value
        ]);
    }
}