<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\RegRepVeh;
use App\RegManVeh;
use App\Vehiculo;
use App\Checklistvehicular;
use App\Vehiculodocument;
use App\AbastecimientoCombustible;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\Sheet;

class RegRepVehExportResumenUsuarioMes implements FromCollection, WithHeadings, WithEvents{

    /**
    * @return \Illuminate\Support\Collection
    */
    
    //Fecha mÃ¡s antigua
    private $fechainicio;
    //Fecha mÃ¡s reciente
    private $fechafin;
    private $tipofecha;
    private $filter;

    private $concesionaria;
    
    public function __construct($fecha1,$fecha2,$concesionaria,$tipofecha,$filter){
        $this->fechainicio = $fecha1;
        $this->fechafin = $fecha2;
        $this->concesionaria = $concesionaria;
        $this->tipofecha = $tipofecha;
        $this->filter = $filter;
    }
    public function collection(){
        
        $fechafin = $this->fechafin;
        $idConcAct = $this->concesionaria;
        $fechainicio = $this->fechainicio;
        $tipofecha = $this->tipofecha;
        $filter = $this->filter;
        
        $resultado= RegRepVeh::join('bitacora','bitacora.registro_id','=','regrepveh.id')
        ->join('users','users.id','=','bitacora.user_id')
        ->join('concesionaria','concesionaria.id','=','regrepveh.concesionaria_id')
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('regrepveh.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regrepveh.'.$tipofecha,'<=', $fechafin);
            })
        ->where('bitacora.accion','like','crear')->where('bitacora.tabla','like','regrepveh');

        $resultQuery = $resultado->select(DB::raw('MONTHNAME(regrepveh.'.$tipofecha.') as fecha'),DB::raw('count(*) as total'),'users.name as usuario','concesionaria.abreviatura',DB::raw('\'REPUESTO\'  as tipo'))
                        ->groupBy('users.id')
                        ->groupBy('users.name')
                        ->groupBy('concesionaria.abreviatura')
                        ->groupBy(DB::raw('MONTHNAME(regrepveh.'.$tipofecha.')'))
                        ->orderBy(DB::raw('MONTHNAME(regrepveh.'.$tipofecha.')'),'desc')->get();        

        $newResult = [];
        for($i = 0; $i < count($resultQuery); $i++) { 
            array_push($newResult, $resultQuery[$i]);
        }

        $resultado= RegManVeh::join('bitacora','bitacora.registro_id','=','regmanveh.id')
        ->join('users','users.id','=','bitacora.user_id')
        ->join('concesionaria','concesionaria.id','=','regmanveh.concesionaria_id')
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('regmanveh.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regmanveh.'.$tipofecha,'<=', $fechafin);
            })
        ->where('bitacora.accion','like','crear')->where('bitacora.tabla','like','regmanveh');

        $resultQuery = $resultado->select(DB::raw('MONTHNAME(regmanveh.'.$tipofecha.') as fecha'),DB::raw('count(*) as total'),'users.name as usuario','concesionaria.abreviatura',DB::raw('\'MANTENIMIENTO\'  as tipo'))
                        ->groupBy('users.id')
                        ->groupBy('users.name')
                        ->groupBy('concesionaria.abreviatura')
                        ->groupBy(DB::raw('MONTHNAME(regmanveh.'.$tipofecha.')'))
                        ->orderBy(DB::raw('MONTHNAME(regmanveh.'.$tipofecha.')'),'desc')->get();   

        for($i = 0; $i < count($resultQuery); $i++) { 
            array_push($newResult, $resultQuery[$i]);
        }

        $collectionResult = collect($newResult);

        return $collectionResult;
    }

    public function headings(): array{
        return [
            'MES',
            'CANTIDAD',
            'USUARIO',
            'CONCESIONARIA',
            'TIPO',
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

                $cellRange = 'A1:Q1'; // All headers
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
