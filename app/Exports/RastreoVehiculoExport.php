<?php

namespace App\Exports;

use App\Concesionaria;
use App\Vehiculo;
use App\Checklistvehicular;
use App\Control;
use App\RegManVeh;
use App\RegRepVeh;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RastreoVehiculoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fechainicio;
    private $fechafin;
    private $placa;

    public function __construct($fechainicio, $fechafin, $placa)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->placa = $placa;
    }

    public function collection(){
        $fechainicio = $this->fechainicio;
        $fechafin = $this->fechafin;
        $placa = $this->placa;
        $fecha = $fechainicio;

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $resultado        = Vehiculo::where('placa','like','%'.$placa.'%')
                            ->where('concesionaria_id','=', $idConcAct)
                            ->whereIn('contratista_id',[12,27])
                            ->whereIn('estado',['Activo','Temporal'])
                            ->orderBy('placa','asc');
        $lista            = $resultado->get();

        $data = array();
        foreach ($lista as $key => $value){
            $fechainicio = $fecha;
            while(strtotime($fechainicio)<=strtotime($fechafin)){
                $regmanveh = RegManVeh::where('ua_id','=',$value->id)->where('fechamantenimiento','=',$fechainicio)->first();
                if(!is_null($regmanveh)){
                    $kmman = $regmanveh->kmman;
                }else{
                    $kmman = '-';
                }
                $regrepveh = RegRepVeh::where('ua_id','=',$value->id)->where('fechamantenimiento','=',$fechainicio)->first();
                if(!is_null($regrepveh)){
                    $kmrep = $regrepveh->kmman;
                }else{
                    $kmrep = '-';
                }
                $control = Control::join('detallecontrol','detallecontrol.control_id','=','control.id')
                                ->join('garita','garita.id','=','control.garita_id')
                                ->where(DB::raw('cast(control.fecha as date)'),'=',$fechainicio)
                                ->where('detallecontrol.vehiculo_id','=',$value->id)
                                ->where('control.tipo','like','Entrada')
                                ->whereNull('detallecontrol.deleted_at')
                                ->select(DB::raw('distinct(detallecontrol.km) as km'),'control.fecha','garita.nombre as garita')
                                ->orderBy('fecha','asc')
                                ->get();
                $entrada = "";
                if(count($control)>0){
                    foreach ($control as $k => $v) {
                        $entrada .= "[".substr($v->fecha,11,5)."]: ".$v->km." km (".$v->garita.")\n";
                    }
                    //$entrada = substr($entrada,0,strlen($entrada)-2);
                }else{
                    $entrada = '-';
                }
                $control = Control::join('detallecontrol','detallecontrol.control_id','=','control.id')
                                ->join('garita','garita.id','=','control.garita_id')
                                ->where(DB::raw('cast(control.fecha as date)'),'=',$fechainicio)
                                ->where('detallecontrol.vehiculo_id','=',$value->id)
                                ->where('control.tipo','like','Salida')
                                ->whereNull('detallecontrol.deleted_at')
                                ->select(DB::raw('distinct(detallecontrol.km) as km'),'control.fecha','garita.nombre as garita')
                                ->orderBy('fecha','asc')
                                ->get();
                $salida = "";
                if(count($control)>0){
                    foreach ($control as $k => $v) {
                        $salida .= "[".substr($v->fecha,11,5)."]: ".$v->km." km (".$v->garita.")\n";
                    }
                    //$salida = substr($salida,0,strlen($salida)-2);
                }else{
                    $salida = '-';
                }
                $checklist = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                ->where('fecha_registro','like',$fechainicio)
                                ->orderBy('hora_inicio','asc')
                                ->get();
                $check = '';
                if(count($checklist)>0){
                    foreach ($checklist as $k => $v) {
                        $check = "[".substr($v->hora_inicio,0,5)." - ".substr($v->hora_fin,0,5)."] ".$v->k_inicial." - ".$v->k_final." km (".$v->conductor->nombres." ".$v->conductor->apellidos.")\n";
                    }
                    $check = substr($check,0,strlen($check)-2);
                }else{
                    $check = '-';
                }

                $array = array();
                $array[] = date("d/m/Y",strtotime($fechainicio));
                $array[] = $value->placa;
                //$array[] = $kmman;
                //$array[] = $kmrep;
                $array[] = $entrada;
                $array[] = $salida;
                $array[] = $check;
                $data[] = $array;

                $fechainicio = date("Y-m-d",strtotime("+1 day",strtotime($fechainicio)));
            }
        }

        return collect($data);
    }

    public function headings(): array{
        return [
            'Fecha',
            'Placa',
            //'KM Mant.',
            //'KM Mant. Repuesto',
            'Entrada',
            'Salida',
            'Checklist Vehicular'
        ];
    }
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }

                $cellRange = 'A1:Z1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
            }
        ];
    }  
}
