<?php

namespace App\Exports;

use App\Concesionaria;
use App\Vehiculo;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VehiculoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct()
    {

    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $filtro           = array();
        $filtro[]         = ['concesionaria_id', $idConcAct];

        $resultado        = Vehiculo::where($filtro)->orderBy('modelo', 'ASC');
        $lista            = $resultado->get();

        $data = array();
        foreach($lista as $obj) {
            $array = array();
            $array[] = $obj->ua->codigo;
            $array[] = $obj->modelo;
            if($obj->marca){
                $array[] = $obj->marca->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $obj->anio;
            $array[] = $obj->placa;
            $array[] = $obj->motor;
            if($obj->contratista){
                $array[] = $obj->contratista->razonsocial;
            }else{
                $array[] = "";
            }
            if($obj->area){
                $array[] = $obj->area->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $obj->asientos;
            $array[] = $obj->chasis;
            if($obj->carroceria){
                $array[] = $obj->carroceria->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $obj->color;
            if($obj->kilometraje){
                $array[] = $obj->kilometraje->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $obj->estado; 
            $data[] = $array;
        }

        return collect($data);
    }

    public function headings(): array{
        return [
            'Ua',
            'Modelo',
            'Marca',
            'Año de Fbr',
            'Placa',
            'Motor',
            'Subcontratista / Propio',
            'Area',
            'Asientos',
            'Chasis',
            'Carroceria',
            'Color',
            'Regla',
            'Estado'
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
