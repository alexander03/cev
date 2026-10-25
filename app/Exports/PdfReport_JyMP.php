<?php

namespace App\Exports;

use DateTime;
use DB;
use App\Tipohora;
use App\Equipo;
use App\Controldiario;
use App\Piepagina;

class PdfReport_JyMP
{
    private $start_date, $end_date, $equipo_ids, $subtotal, $periodo, $fechas;

    public function __construct(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo, array $fechas) 
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->equipo_ids = $equipo_ids;
        $this->periodo = $periodo;
        $this->fechas = $fechas;
    }

    private function getSummaryEquipos(): array
    {
        // Traigo las fechas formateadas para uso rapido
        $startDate = $this->start_date->format('Y-m-d');
        $endDate = $this->end_date->format('Y-m-d');
        $listPeriodo = $this->fechas;
        $periodo = $this->periodo;
        
        // Consigo todos los ids de tipos de hora mantenimiento
        $thMantenimiento_ids = Tipohora::select('id')->where('tipo', 'like', '%Horas de Mantenimiento%')->get();
        
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
            //$dias_permanencia = date_diff($this->end_date,$this->start_date)->format('%a')+1;//cambiado por la fecha de adelante
            
            $equipo = Equipo::find($equipo_id);

            if($equipo['unidad_id']=="6"){//HORAS
                $dias_permanencia = $listPeriodo[$equipo_id];
                // Horas trabajadas
                $a = Controldiario::select(DB::raw('SUM(hora_total) AS a'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)->first()['a'];
                $a = is_null($a) ? 0 : floatval($a);
    
                // Horas de mantenimiento
                $horas_min = round($equipo->horas_min/$periodo,2);
                $mantenimiento = Controldiario::join('tipohora','tipohora.id','=','controldiario.tipohora_id')
                                ->select(DB::raw('(case when tipohora.tipo="Horas de Mantenimiento" and SUM(hora_parada)>'.$horas_min.' then '.$horas_min.' else sum(hora_parada) end) AS b'))
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
                $e = round($d > $a ? $d - $a : 0,2);
    
                // Horas total a pagar
                $f = $a + $e;
            }else{//DIAS
                $dias_permanencia = $listPeriodo[$equipo_id];
                
                /*$a = count(Controldiario::select(DB::raw('count(fecha) AS a'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where('fecha', '>=', $startDate)
                                ->where('fecha', '<=', $endDate)
                                ->where('hora_total','>',0)
                                ->groupBy('fecha')
                                ->get());
                $a = is_null($a) ? 0 : floatval($a);*/
                $a = $dias_permanencia;
    
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
                
                //$a = $dias_permanencia - $b;    
                $a = $a - $b;
                
                // Horas minimas establecidas
                //$c = round(($equipo->horas_min/$periodo) * intval($dias_permanencia), 2);
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
                
                $a = $a - $g;
                
                //$g = $dias_permanencia - $a;
                //$g = date_diff($this->end_date,$this->start_date)->format('%a')+1 - $a;
    
                // Horas total a pagar
                $f = round(($a+$g)/$periodo,2);
                //$f = round(($a+$g - $b)/(date_diff($this->end_date,$this->start_date)->format('%a')+1),2);
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

            $table[] = [
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'cantidad' => number_format($f, 2),
                'valorUnitario' => number_format($valorUnitario, 2),
                'subtotal' => number_format($subtotalEquipo, 2),
                'responsable' => $responsable,
                'moneda' => $equipo->moneda,
            ];
        }
        
        $this->subtotal = number_format($subtotal, 2,".",',');

        return $table;
    }

    public function getData(): array
    {
        $data = [];


        $data['periodo'] = strtoupper("(" . $this->start_date->format('d-m-Y') . " AL " . $this->end_date->format('d-m-Y') . ")");
        $data['listSummaryEquipos'] = $this->getSummaryEquipos();

        $data['subtotal'] = $this->subtotal;
        $data['listapiepagina'] = Piepagina::where('formato','like','Medicion Equipo Consolidado')->orderBy('orden','asc')->get();

        return $data;
    }
}
