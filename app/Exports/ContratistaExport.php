<?php

namespace App\Exports;

use App\Contratista;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ContratistaExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
    
        $uaList = Contratista::select('ruc', 'razonsocial','propietario as contacto1','email as email1','telefono as telefono1','propietario2 as contacto2','email2','telefono2','propietario3 as contacto3','email3','telefono3') 
            -> get();         
        return $uaList;
    }

    public function headings(): array{
        return [
            'RUC',
            'RAZON SOCIAL',
            'CONTACTO1',
            'EMAIL1',
            'TELEFONO1',
            'CONTACTO2',
            'EMAIL2',
            'TELEFONO2',
            'CONTACTO3',
            'EMAIL3',
            'TELEFONO3',
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
                $cellRange = 'A1:K1'; // All headers
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
