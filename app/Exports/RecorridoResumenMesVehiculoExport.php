<?php

namespace App\Exports;

use App\Contratista;
use App\Controldiario;
use App\Checklistvehicular;
use App\Vehiculo;
use App\Equipo;
use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RecorridoResumenMesVehiculoExport implements FromCollection, WithHeadings, WithEvents
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
        $list = Vehiculo:://where('concesionaria_id','=',$this->getConsecionariaActual())
                    orderBy('modelo','asc')
                    ->get();
        $meses = $this->obtenerMes($this->fechainicial,$this->fechafinal);
        foreach($list as $k=>$v){
            $rec=0;$band=false;

            $obj = array();
            $obj[] = $v->ua->codigo;
            $obj[] = $v->ua->descripcion;
            $obj[] = $v->placa;
            if(!is_null($v->uatrabajo)){
                $obj[] = $v->uatrabajo->codigo;
                $obj[] = $v->uatrabajo->descripcion;
            }else{
                $obj[] = "-";
                $obj[] = "-";
            }
            for ($i=0; $i < count($meses); $i++) { 
                $fechafin = date("Y-m-d",strtotime("-1 day",strtotime("+1 month",strtotime($meses[$i]["fecha"]))));
                $check = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                    ->where('fecha_registro','>=',$meses[$i]["fecha"])
                                    ->where('fecha_registro','<',$fechafin)
                                    ->orderBy('fecha_registro','asc')
                                    ->orderBy('hora_inicio','asc')
                                    ->first();
                if(!is_null($check)){
                    $inicial = $check->k_inicial;
                }else{
                    $inicial = 0;
                }

                $check = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                    ->where('fecha_registro','>=',$meses[$i]["fecha"])
                                    ->where('fecha_registro','<',$fechafin)
                                    ->orderBy('fecha_registro','desc')
                                    ->orderBy('hora_fin','desc')
                                    ->first();
                if(!is_null($check)){
                    $final = $check->k_final;
                }else{
                    $final = 0;
                }
                $obj[] = $final - $inicial;
            }
            $obj[] = $v->concesionaria->abreviatura;
            $obj[] = $v->estado;
            $data[] = $obj;
        }
        return collect($data);
    }

    public function headings(): array{
        $lista = array();
        $lista[] = "UA VEHICULO";
        $lista[] = "DESCRIPCION VEHICULO";
        $lista[] = "PLACA";
        $lista[] = "UA TRABAJO";
        $lista[] = "DESCRIPCION UA TRABAJO";
        $meses = $this->obtenerMes($this->fechainicial,$this->fechafinal);
        for ($i=0; $i < count($meses); $i++) { 
            $lista[] = $this->nombreMes(date("m",strtotime($meses[$i]["fecha"])));
        }
        $lista[] = "CONCESIONARIA";
        $lista[] = "ESTADO";
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
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    private function obtenerMes($fechainicio,$fechafin){
        $meses = array();
        while (strtotime($fechainicio)<=strtotime($fechafin)) {
            $meses[] = array("fecha"=>$fechainicio,"mes"=>date("m",strtotime($fechainicio)));
            $fechainicio=date("Y-m-d",strtotime("+1 month",strtotime($fechainicio)));
        }
        return $meses;
    }

    private function nombreMes($mes){
        $nombre = "";
        if($mes=="01"){
            $nombre="ENE";
        }elseif($mes=="02"){
            $nombre="FEB";
        }elseif($mes=="03"){
            $nombre="MAR";
        }elseif($mes=="04"){
            $nombre="ABR";
        }elseif($mes=="05"){
            $nombre="MAYO";
        }elseif($mes=="06"){
            $nombre="JUN";
        }elseif($mes=="07"){
            $nombre="JUL";
        }elseif($mes=="08"){
            $nombre="AGO";
        }elseif($mes=="09"){
            $nombre="SEP";
        }elseif($mes=="10"){
            $nombre="OCT";
        }elseif($mes=="11"){
            $nombre="NOV";
        }elseif($mes=="12"){
            $nombre="DIC";
        }
        return $nombre;
    }
}
