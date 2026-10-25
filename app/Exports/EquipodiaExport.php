<?php

namespace App\Exports;

use App\Contratista;
use App\Controldiario;
use App\Equipo;
use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EquipodiaExport implements FromCollection, WithHeadings, WithEvents
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
    
        $list = Equipo::
                        where('concesionaria_id','=',$this->getConsecionariaActual())
                        -> get();    
        $data = array();
        $c=0;
        foreach($list as $k=>$v){
            $dat = array();
            $dat[]=$v->ua->codigo;
            $dat[]=$v->descripcion;
            $ini = strtotime($this->fechainicial);
            $fin = strtotime($this->fechafinal);
            while($ini<=$fin){
                $total = 0;
                $control = Controldiario::where('equipo_id','=',$v->id)
                                    ->where('fecha','=',date("Y-m-d",$ini))
                                    ->select(DB::raw('sum(hora_total) as total'),DB::raw('sum(hora_parada) as parada'))
                                    ->groupBy('equipo_id')
                                    ->groupBy('fecha')
                                    ->get();
                foreach($control as $x=>$y){
                    $total = $total + $y->total + $y->parada;
                }
                if($total>0){
                    $dat[]=$total;
                }else{
                    $dat[]="";
                }
                $ini = strtotime("+1 day",$ini);                        
            }    
            $data[]=$dat;
        }
        return collect($data);
    }

    public function headings(): array{
        $array = array();
        $array[] = 'CODIGO';
        $array[] = 'EQUIPO';
        $ini = strtotime($this->fechainicial);
        $fin = strtotime($this->fechafinal);
        while($ini<=$fin){
            $fecha = date("Y-m-d",$ini);
            $array[] = substr($fecha,8,2)."/".substr($fecha,5,2);
            //$array[] = $fecha;
            $ini = strtotime("+1 day",$ini);
        }
        return $array;
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
                $cellRange = 'A1:AA1'; // All headers
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
