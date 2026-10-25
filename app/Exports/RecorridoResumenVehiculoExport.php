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

class RecorridoResumenVehiculoExport implements FromCollection, WithHeadings, WithEvents
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
        foreach($list as $k=>$v){
            $rec=0;$band=false;
            $check = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                ->where('fecha_registro','>=',$this->fechainicial)
                                ->where('fecha_registro','<=',$this->fechafinal)
                                ->orderBy('fecha_registro','asc')
                                ->orderBy('hora_inicio','asc')
                                ->first();
            if(!is_null($check)){
                $inicial = $check->k_inicial;
            }else{
                $inicial = 0;
            }

            $check = Checklistvehicular::where('vehiculo_id','=',$v->id)
                                ->where('fecha_registro','<=',$this->fechafinal)
                                ->where('fecha_registro','>=',$this->fechainicial)
                                ->orderBy('fecha_registro','desc')
                                ->orderBy('hora_fin','desc')
                                ->first();
            if(!is_null($check)){
                $final = $check->k_final;
            }else{
                $final = 0;
            }

            $obj = array();
            $obj[] = $v->ua->codigo;
            $obj[] = $v->ua->descripcion;
            if(!is_null($v->uatrabajo)){
                $obj[] = $v->uatrabajo->codigo;
                $obj[] = $v->uatrabajo->descripcion;
            }else{
                $obj[] = "-";
                $obj[] = "-";
            }
            $obj[] = $this->fechainicial;
            $obj[] = $this->fechafinal;
            $obj[] = $final - $inicial;

            $data[] = $obj;
        }
        
        /*$list = Equipo:://where('concesionaria_id','=',$this->getConsecionariaActual())
                    get();
        foreach($list as $k=>$v){
            $rec=0;$band=false;
            $check = Checklistvehicular::where('equipo_id','=',$v->id)
                                ->where('fecha_registro','>=',$this->fechainicial)
                                ->orderBy('fecha_registro','asc')
                                ->orderBy('hora_inicio','asc')
                                ->first();
            if(!is_null($check)){
                if(!is_null($check)){
                    $inicial = $check->k_inicial;
                }else{
                    $inicial = 0;
                }

                $check = Checklistvehicular::where('equipo_id','=',$v->id)
                                    ->where('fecha_registro','<=',$this->fechafinal)
                                    ->where('fecha_registro','>=',$this->fechainicial)
                                    ->orderBy('fecha_registro','desc')
                                    ->orderBy('hora_fin','desc')
                                    ->first();
                if(!is_null($check)){
                    $final = $check->k_final;
                }else{
                    $final = 0;
                }

                $obj = array();
                $obj[] = $v->ua->codigo;
                $obj[] = $v->ua->descripcion;
                if(!is_null($v->uatrabajo)){
                    $obj[] = $v->uatrabajo->codigo;
                    $obj[] = $v->uatrabajo->descripcion;
                }else{
                    $obj[] = "-";
                    $obj[] = "-";
                }
                $obj[] = $this->fechainicial;
                $obj[] = $this->fechafinal;
                $obj[] = $final - $inicial;

                $data[] = $obj;
            }
        }*/
        return collect($data);
    }

    public function headings(): array{
        $lista = array();
        $lista[] = "UA VEHICULO";
        $lista[] = "DESCRIPCION VEHICULO";
        $lista[] = "UA TRABAJO";
        $lista[] = "DESCRIPCION UA TRABAJO";
        $lista[] = "FECHA INICIO";
        $lista[] = "FECHA FIN";
        $lista[] = "KM";
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
                $cellRange = 'A1:G1'; // All headers
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
