<?php

namespace App\Exports;

use App\Concesionaria;
use App\Equipo;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EquipoExport implements FromCollection, WithHeadings, WithEvents
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

        $resultado        = Equipo::where($filtro)->orderBy('modelo', 'ASC');
        $lista            = $resultado->get();

        $data = array();
        foreach($lista as $obj) {
            $array[] = array();
            $vehiculo = Equipo::find($obj->id);
            $array[] = $vehiculo->ua->codigo;
            $array[] = $vehiculo->descripcion;
            $array[] = $vehiculo->modelo;
            if($vehiculo->marca){
                $array[] = $vehiculo->marca->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $vehiculo->anio;
            $array[] = $vehiculo->placa;
            $array[] = $vehiculo->capacidad_carga;
            if($vehiculo->contratista){
                $array[] = $vehiculo->contratista->razonsocial;
            }else{
                $array[] = "";
            }
            if($vehiculo->area){
                $array[] = $vehiculo->area->descripcion;
            }else{
                $array[] = "";
            }
            $array[] = $vehiculo->horas_min;
            $array[] = $vehiculo->precio;
            if(!$vehiculo->moneda)
                $array[] = "Sol S/";
            else
                $array[] = "Dolar $";
            $array[] = $vehiculo->unidad->descripcion;
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
            'Regla'
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
