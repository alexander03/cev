<?php

namespace App\Exports;

use App\Checklistvehicular;
use App\Concesionaria;
use App\Vehiculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;

class CheckListNoRegistradoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $fecha_registro_inicial, $fecha_registro_final;

    public function __construct(string $fecha_registro_inicial, string $fecha_registro_final)
    {
        $this->fecha_registro_inicial = $fecha_registro_inicial;
        $this->fecha_registro_final = $fecha_registro_final;
    }
    
    public function collection(){
    
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        

        $fecha_registro_inicial = $this->fecha_registro_inicial ;
        $fecha_registro_final = $this->fecha_registro_final;
           
        $table = [];
        $vehiculo = Vehiculo::whereIn('contratista_id',[12,27,38])->orderBy('placa','asc')->get();
        foreach ($vehiculo as $key => $value) {
            $inicio = strtotime($fecha_registro_inicial);
            $end_date = strtotime($fecha_registro_final);
            while ( $inicio <= $end_date ) {
                $check = Checklistvehicular::where('fecha_registro','=',date("Y-m-d",$inicio))->where('vehiculo_id','=',$value->id)->first();
                if(!is_null($check)){
                    $checkant = Checklistvehicular::where('fecha_registro','<',date("Y-m-d",$inicio))->where('vehiculo_id','=',$value->id)->orderBy('fecha_registro','desc')->first();
                    $array = [];
                    $array[] = date("d/m/Y",$inicio);
                    $array[] = ($value->modelo)." / ".$value->placa;
                    $array[] = $value->carroceria->descripcion;
                    $array[] = $checkant->conductor->apellidos." ".$checkant->conductor->nombres;
                    $array[] = $value->concesionaria->razonsocial;
                    $array[] = date("d/m/Y",strtotime($checkant->fecha_registro));
                    $table[] = $array;
                }
                $inicio = strtotime("+1 day",$inicio);
            }
        }   

        return collect($table);
    }

    public function headings(): array{
        return [
            'FECHA',
            'MODELO/PLACA',
            'CARROCERIA',
            'ULTIMO CONDUCTOR',
            'CONCESIONARIA',
            'FECHA ULTIMO CHECKLIST'
        ];
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
                //$event->sheet->mergeCells($cellRange);
                $cellRange = 'A1:L1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                //$event->sheet->setAutoFilter($cellRange);
            }
        ];
    }  
}
