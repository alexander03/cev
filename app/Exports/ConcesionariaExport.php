<?php

namespace App\Exports;

use App\Concesionaria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConcesionariaExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
    
        $uaList = Concesionaria::select('ruc', 'razonsocial','abreviatura') 
            -> get();         
        return $uaList;
    }

    public function headings(): array{
        return [
            'RUC',
            'RAZON SOCIAL',
            'ABREVIATURA',
        ];
    }
}
