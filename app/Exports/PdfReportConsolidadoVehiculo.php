<?php

namespace App\Exports;

use DateTime;
use DB;
use App\Tipohora;
use App\Vehiculo;
use App\Controldiariovehiculo;
use App\Piepagina;

class PdfReportConsolidadoVehiculo
{
    private $start_date, $end_date, $equipo_ids, $subtotal, $periodo;

    public function __construct(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo) 
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->equipo_ids = $equipo_ids;
        $this->periodo = $periodo;
    }

    private function getSummaryEquipos(): array
    {
        // Traigo las fechas formateadas para uso rapido
        $startDate = $this->start_date->format('Y-m-d');
        $endDate = $this->end_date->format('Y-m-d');
        
        // Consigo todos los ids de tipos de hora mantenimiento
        $thMantenimiento_ids = Tipohora::select('id')->get();//->where('tipo', 'like', '%Horas de Mantenimiento%')
        
        // variable de respuesta
        $table = []; $subtotal = 0;

        foreach ($this->equipo_ids as $equipo_id) {
            $equipo = Vehiculo::join('ua', 'ua.id', '=', 'vehiculo.ua_id')
                            ->join('controldiariovehiculo', 'controldiariovehiculo.vehiculo_id', '=', 'vehiculo.id')
                            //->join('ua as ua2','ua2.id','=','controldiariovehiculo.ua_id')
                            ->join('ua as ua2','ua2.id','=','vehiculo.uatrabajo_id')
                            ->select('ua.codigo', 'vehiculo.modelo as descripcion', DB::raw('COUNT(controldiariovehiculo.id) AS dias_permanencia'),'vehiculo.placa','vehiculo.precio','ua2.codigo as codigo2')
                            ->where('vehiculo.id', '=', $equipo_id)
                            ->where('controldiariovehiculo.fecha', '>=', $startDate)
                            ->where('controldiariovehiculo.fecha', '<=', $endDate)
                            ->groupBy('ua.codigo', 'vehiculo.modelo','vehiculo.placa','vehiculo.precio')->first();

            $codigo = $equipo['codigo'];
            $descripcion = $equipo['descripcion'];
            //$dias_permanencia = date_diff($this->end_date,$this->start_date)->format('%a')+1;
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
                            ->first();
            if(!is_null($b)){
                $b = $b["b"];
            }
            $b = is_null($b) ? 0 : floatval($b);
            
            // Horas minimas establecidas
            $c = round(31 * intval($dias_permanencia), 2);

            // Horas minimas por derecho
            $d = $c > $b ? $c - $b : 0;

            // Horas minimas a pagar
            $e = $d > $a ? $d - $a : 0;

            // Horas total a pagar
            $f = round(($a - $b)/$dias_permanencia,2);
            
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

            $table[] = [
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'ua' => $ua,
                'placa' => $placa,
                'unidad' => 'MES',
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
        $data['listapiepagina'] = Piepagina::where('formato','like','Medicion Vehiculo')->orderBy('orden','asc')->get();

        return $data;
    }
}
