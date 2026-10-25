<?php

namespace App\Exports;

use App\Contratista;
use App\Controldiario;
use App\Checklistvehicular;
use App\Vehiculo;
use App\Equipo;
use App\Concesionaria;
use App\Ua;
use App\DetalleChecklistvehicular;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RecorridoResumenDetalladoVehiculoExport implements FromCollection, WithHeadings, WithEvents
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
        $list = Vehiculo::join('checklistvehicular','checklistvehicular.vehiculo_id','=','vehiculo.id')
                    ->leftjoin('ua','checklistvehicular.uatrabajo_id','=','ua.id')
                    //->where('vehiculo.concesionaria_id','=',$this->getConsecionariaActual())
                    ->where('fecha_registro','<=',$this->fechafinal)
                    ->where('fecha_registro','>=',$this->fechainicial)
                    ->whereNull('checklistvehicular.deleted_at')
                    ->orderBy('fecha_registro','asc')
                    ->orderBy('hora_inicio','asc')
                    ->orderBy('modelo','asc')
                    ->select('checklistvehicular.*','ua.codigo as codigoua','ua.descripcion as descripcionua')
                    ->get();
        $total = 0;                 

        foreach($list as $k=>$v){
            $obj = array();
            $vehiculo = Vehiculo::find($v->vehiculo_id);
            $obj[] = $vehiculo->ua->codigo;
            $obj[] = $vehiculo->ua->descripcion;
            $detalle = DetalleChecklistvehicular::where('checklistvehicular_id','=',$v->id)->get();
            if(count($detalle)>0){
                foreach($detalle as $x=>$y){
                    $obj[] = $y->ua->codigo;
                    $obj[] = $y->ua->descripcion;
                    $obj[] = $v->fecha_registro;
                    $obj[] = $y->km_final - $y->km_inicial;
                    $total = $total + $y->km_final - $y->km_inicial;
                }
            }else{
                $obj[] = $v->codigoua;
                $obj[] = $v->descripcionua;
                $obj[] = $v->fecha_registro;
                $obj[] = $v->k_final - $v->k_inicial;
                $total = $total + $v->k_final - $v->k_inicial;
            }
            $data[] = $obj;
        }


        $obj = array();
        $obj[] = " ";
        $obj[] = " ";
        $obj[] = " ";
        $obj[] = " ";
        $obj[] = " ";
        $obj[] = " ";

        $data[] = $obj;

        /*$list = Equipo::join('checklistvehicular','checklistvehicular.equipo_id','=','equipo.id')
                    //->where('equipo.concesionaria_id','=',$this->getConsecionariaActual())
                    ->where('fecha_registro','<=',$this->fechafinal)
                    ->where('fecha_registro','>=',$this->fechainicial)
                    ->whereNull('checklistvehicular.deleted_at')
                    ->orderBy('fecha_registro','asc')
                    ->orderBy('hora_inicio','asc')
                    ->orderBy('modelo','asc')
                    ->select('checklistvehicular.*')
                    ->get();
           
        foreach($list as $k=>$v){
            $obj = array();
            $vehiculo = Equipo::find($v->equipo_id);
            $obj[] = $vehiculo->ua->codigo;
            $obj[] = $vehiculo->ua->descripcion;
            if(!is_null($vehiculo->uatrabajo)){
                $obj[] = $vehiculo->uatrabajo->codigo;
                $obj[] = $vehiculo->uatrabajo->descripcion;
            }else{
                $obj[] = "-";
                $obj[] = "-";
            }
            $obj[] = $v->fecha_registro;
            $obj[] = $v->k_final - $v->k_inicial;
            $total = $total + $v->k_final - $v->k_inicial;
            $data[] = $obj;
        }*/

        $obj = array();
        $obj[] = '';
        $obj[] = '';
        $obj[] = '';
        $obj[] = '';
        $obj[] = 'TOTAL';
        $obj[] = $total;

        $data[] = $obj;
        return collect($data);
    }

    public function headings(): array{
        $lista = array();
        $lista[] = "UA VEHICULO";
        $lista[] = "DESCRIPCION VEHICULO";
        $lista[] = "UA TRABAJO";
        $lista[] = "DESCRIPCION UA TRABAJO";
        $lista[] = "FECHA";
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
