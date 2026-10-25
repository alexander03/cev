<?php

namespace App\Exports;

use App\Contratista;
use App\Controldiario;
use App\Checklistvehicular;
use App\Vehiculo;
use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RecorridoVehiculoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct($fechainicial,$fechafinal)
    {
        $this->fechainicial = $fechainicial;
        $this->fechafinal = $fechafinal;
    }
    
    public function collection(){
        $data = array();
        $list = Vehiculo::where('concesionaria_id','=',$this->getConsecionariaActual())
                    ->orderBy('modelo','asc')
                    ->get();
        $placa = array();
        $placa[] = "PLACA";
        $empresa = array();
        $empresa[] = "EMPRESA";
        $ua = array();
        $ua[]="UA";
        $nombres = array();
        $nombres[]="NOMBRES Y APELLIDOS";
        $inicial=array();
        $inicial[]="ULTIMO KM INICIAL";
        $recorrido=array();
        $recorrido[]="KM TOTAL";
        $x=$this->fechainicial;
        while(strtotime($x)<=strtotime($this->fechafinal)){
            eval("\$f".str_replace("-","",$x)."=array();");
            eval("\$f".str_replace("-","",$x)."[]='".date("d/m/Y",strtotime($x))."';");
            $x=date("Y-m-d",strtotime($x." +1 day"));
        }
        foreach($list as $k=>$v){
            $placa[] = $v->placa;
            $empresa[] = $v->contratista->razonsocial;
            $ua[] = $v->ua->codigo;
            
            $x=$this->fechainicial;
            $rec=0;$band=false;
            while(strtotime($x)<=strtotime($this->fechafinal)){
                $check = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                ->where('fecha_registro','=',$x)
                                ->orderBy('hora_fin','desc')
                                ->first();
                if(!is_null($check)){
                    eval("\$f".str_replace("-","",$x)."[]='".$check->k_final."';");
                    $check2 = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                ->where('fecha_registro','=',$x)
                                ->orderBy('hora_inicio','asc')
                                ->first();
                    $rec=$rec + $check->k_final - $check2->k_inicial;
                    if(!$band){
                        $inicial[]=$check2->k_inicial;
                        $band=true;
                    }
                }else{
                    eval("\$f".str_replace("-","",$x)."[]='X';");
                }
                $x=date("Y-m-d",strtotime($x." +1 day"));
            }
            if(!$band){
                $inicial[]="";
            }
            $recorrido[]=$rec;
        }
        $data[]=$placa;
        $data[]=$empresa;
        $data[]=$ua;
        $data[]=$nombres;
        $data[]=$inicial;
        $x=$this->fechainicial;
        while(strtotime($x)<=strtotime($this->fechafinal)){
            eval("\$data[]=\$f".str_replace("-","",$x).";");
            $x=date("Y-m-d",strtotime($x." +1 day"));
        }
        $data[]=$recorrido;
        return collect($data);
    }

    public function headings(): array{
        $lista = array();
        $con = Concesionaria::find($this->getConsecionariaActual());
        $lista[] = $con->razonsocial;
        $list = Vehiculo::where('concesionaria_id','=',$this->getConsecionariaActual())
                    ->orderBy('modelo','asc')
                    ->get();
        foreach($list as $k=>$v){
            $lista[] = $v->modelo." ".$v->marca->descripcion;
        }
        return $lista;
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z','AA','AB'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }
                $cellRange = 'A1:A1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
            }
        ];
    }  
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
