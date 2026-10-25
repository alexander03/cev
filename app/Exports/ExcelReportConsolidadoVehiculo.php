<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Concesionaria;
use App\Vehiculo;
use App\Tipohora;
use App\Controldiariovehiculo;
use DateTime;
use Exception;
use DB;

class ExcelReportConsolidadoVehiculo extends ExcelHelper implements FromView 
{
    use Exportable;


    private $start_date, $end_date, $equipo_ids, $periodo;

    public function __construct(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->equipo_ids = $equipo_ids;
        $this->periodo = $periodo;
    }


    private function loadData()
    {
        $concesionaria = Concesionaria::getConcesionariaActual();

        // Parte 1
        $this->nextCell()->add('MEDICIÓN DE ALQUILER DE EQUIPOS', ['colspan' => 12]);
        
        // Parte 2
        $this->nextCell(2)->add([['CONCESIONARIA:', $concesionaria->razonsocial]]);
        $this->nextCell()->add([['SUBCONTRATISTA:', '']]);
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
        $this->nextCol()->add('_________________________________', ['colspan' => 4]);
        $this->nextCol()->add('_________________________________', ['colspan' => 4]);
        $this->nextCol()->add('_________________________________', ['colspan' => 4]);
        $this->nextRow();
        $this->nextCol()->add('ING. JOSE SALINAS', ['colspan' => 4]);
        $this->nextCol()->add('SAYRA MONJA', ['colspan' => 4]);
        $this->nextCol()->add('REPRESENTANTE SUBCONTRATISTA', ['rowspan' => 2, 'colspan' => 4]);
        $this->nextRow();
        $this->nextCol()->add('GERENTE DE OPERACIONES Y MANTENIMIENTO', ['colspan' => 4]);
        $this->nextCol()->add('COSTOS', ['colspan' => 4]);
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
        
        // Consigo todos los ids de tipos de hora mantenimiento
        $thMantenimiento_ids = Tipohora::select('id')->where('tipo', 'like', '%Horas de Mantenimiento%')->get();
        
        // variable de respuesta
        $table = []; $subtotal = 0;

        foreach ($this->equipo_ids as $equipo_id) {
            $equipo = Vehiculo::join('ua', 'ua.id', '=', 'vehiculo.ua_id')
                            ->join('controldiariovehiculo', 'controldiariovehiculo.vehiculo_id', '=', 'vehiculo.id')
                            ->join('ua as ua2','ua2.id','=','controldiariovehiculo.ua_id')
                            ->select('ua.codigo', 'vehiculo.modelo as descripcion', DB::raw('COUNT(controldiariovehiculo.id) AS dias_permanencia'),'vehiculo.placa','vehiculo.precio','ua2.codigo as codigo2')
                            ->where('vehiculo.id', '=', $equipo_id)
                            ->where('controldiariovehiculo.fecha', '>=', $startDate)
                            ->where('controldiariovehiculo.fecha', '<=', $endDate)
                            ->groupBy('ua.codigo', 'vehiculo.modelo','vehiculo.placa','vehiculo.precio')->first();

            $codigo = $equipo['codigo'];
            $descripcion = $equipo['descripcion'];
            $dias_permanencia = $this->periodo;
            $ua = $equipo['codigo2'];
            $placa = $equipo['placa'];
            
            $equipo = Vehiculo::find($equipo_id);

            // Horas trabajadas
            $a = Controldiariovehiculo::select(DB::raw('count(hora_total) AS a'))
                            ->where('vehiculo_id', '=', $equipo_id)
                            ->where('fecha', '>=', $startDate)
                            ->where('fecha', '<=', $endDate)
                            ->where('hora_total','>',0)
                            ->groupBy('vehiculo_id')
                            ->first()['a'];
            $a = is_null($a) ? 0 : floatval($a);

            // Horas de mantenimiento
            $b = Controldiariovehiculo::select(DB::raw('count(hora_parada) AS b'))
                            ->where('vehiculo_id', '=', $equipo_id)
                            ->where(function ($query) use ($thMantenimiento_ids) {
                                foreach ($thMantenimiento_ids as $th) $query->orWhere('tipohora_id', '=', $th['id']);
                            })
                            ->where('fecha', '>=', $startDate)
                            ->where('fecha', '<=', $endDate)
                            ->where('hora_parada','>',0)
                            ->groupBy('vehiculo_id')
                            ->first()["b"];
            $b = is_null($b) ? 0 : floatval($b);
            
            // Horas minimas establecidas
            $c = round(31 * intval($dias_permanencia), 2);

            // Horas minimas por derecho
            $d = $c > $b ? $c - $b : 0;

            // Horas minimas a pagar
            $e = $d > $a ? $d - $a : 0;

            // Horas total a pagar
            $f = round(($a - $b)/31,2);
            
            // Responsable de equipo
            if(!is_null($equipo->ua->responsable)){
                $responsable = $equipo->ua->responsable->nombre;
            }else{
                $responsable = "";
            }

            // Valor unitario
            $valorUnitario = $equipo->precio;
            
            // Subtotal por equipo
            $subtotalEquipo = round($f * $valorUnitario, 2);

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