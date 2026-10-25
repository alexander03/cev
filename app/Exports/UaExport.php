<?php

namespace App\Exports;

use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UaExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
    
        $uaList = Ua::select('ua.codigo', 'ua.descripcion','u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc',
        'ua.padre','ua.habilitada', 'ua.fecha_inicio', 'ua.fecha_fin','rep.nombre as responsable','c.razonsocial as concesionaria') 
            -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
            -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
            -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
            -> whereNull('ua.deleted_at')
            -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
            -> get();         

        foreach($uaList as $ua) {
           if(!$ua->getOriginal('padreCodigo')) $ua->padreCodigo = 'SIN PADRE';
           if(!$ua->getOriginal('padreDesc')) $ua->padreDesc = 'SIN PADRE';
           ($ua->getOriginal('habilitada') == 0) ? $ua->habilitada = 'INHABILITADA' : $ua->habilitada = 'HABILITADA';
           if(!$ua->getOriginal('fecha_fin')){
               $ua->fecha_fin = 'ILIMITADA';
           }else{
               $ua->fecha_fin = date("d/m/Y",strtotime($ua->fecha_fin)); 
           }
           $ua->fecha_inicio = date("d/m/Y",strtotime($ua->fecha_inicio));
        }

        return $uaList;
    }

    public function headings(): array{
        return [
            'Codigo',
            'Descripción',
            'Código ua padre',
            'Descripción ua padre',
            'Es padre',
            'Estado',
            'Fecha de inicio',
            'Fecha de fin',
            'Responsable',
            'Concesionaria'
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
