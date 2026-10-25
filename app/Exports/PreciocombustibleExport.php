<?php

namespace App\Exports;

use App\Concesionaria;
use App\Preciocombustible;
use App\Detallepreciocombustible;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PreciocombustibleExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $fechainicio, $fechafin;

    public function __construct(string $fechainicio, string $fechafin)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $List = Preciocombustible::select('preciocombustible.fechainicio','preciocombustible.fechafin', 'grifo.descripcion as grifo','tipocombustible.descripcion as tipocombustible', 'dpc.precio') 
            -> join('detallepreciocombustible as dpc','dpc.preciocombustible_id','=','preciocombustible.id')
            -> join('grifo','grifo.id','=','preciocombustible.grifo_id')
            -> join('tipocombustible','tipocombustible.id','=','dpc.tipocombustible_id')
            -> where('preciocombustible.fechainicio','>=',$this->fechainicio)
            -> where('preciocombustible.fechainicio','<=',$this->fechafin)
            -> whereNull('dpc.deleted_at')
            -> get();         

        foreach($List as $ua) {
            $ua->fechainicio = date("d/m/Y",strtotime($ua->fechainicio)); 
            $ua->fechafin = date("d/m/Y",strtotime($ua->fechafin));
        }

        return $List;
    }

    public function headings(): array{
        return [
            'Fecha Inicio',
            'Fecha Fin',
            'Grifo',
            'Tipo Combustible',
            'Precio'
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
            }
        ];
    }  
}
