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

class RegRepVehExportExcelList implements FromCollection, WithHeadings, WithEvents{

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
        
        $resultado= RegRepVeh::leftjoin('vehiculo','vehiculo.id','=','regrepveh.ua_id')
                            ->leftjoin('equipo','equipo.id','=','regrepveh.equipo_id')
                            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
                            ->leftjoin('taller','taller.id','=','regrepveh.taller_id')
        ->where('regrepveh.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('regrepveh.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regrepveh.'.$tipofecha,'<=', $fechafin);
            })
        ->where(function($q) use ($filter){
                $q->where('regrepveh.cliente', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.ua_id', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.telefono', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.ordencompra', 'like', '%'.$filter.'%')
                  ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%')
                  ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.numero', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.fechasalida', 'like', '%'.$filter.'%');
            });


        $resultQuery            = $resultado->select('regrepveh.numero','regrepveh.fecharegistro','regrepveh.fechamantenimiento','regrepveh.titulo','taller.razonsocial','regrepveh.telefono','regrepveh.docmat_id',DB::raw('(case when vehiculo.id is not null then vehiculo.placa else equipo.placa end) as placa'),DB::raw('(case when vehiculo.id is not null then carroceria.descripcion else equipo.descripcion end) as carroceria'),DB::raw('(case when vehiculo.id is not null then vehiculo.modelo else equipo.modelo end) as modelo'),DB::raw('(case when vehiculo.id is not null then (select descripcion from marca where id=vehiculo.marca_id) else (select descripcion from marca where id=equipo.marca_id) end) as marca'),'regrepveh.kminicial','regrepveh.kmman','regrepveh.kmfinal','regrepveh.fechaentrada','regrepveh.fechasalida',DB::raw('(case when regrepveh.tipomantenimiento=\'1\' then \'Preventivo\' else \'Correctivo\' end) as tipo'))->orderBy('regrepveh.fechamantenimiento','desc')->get();        

        for($i = 0; $i < count($resultQuery); $i++) { 
            $resultQuery[$i]->fechaentrada = date("d/m/Y",strtotime($resultQuery[$i]->fechaentrada));
            $resultQuery[$i]->fechasalida = date("d/m/Y",strtotime($resultQuery[$i]->fechasalida));
            $resultQuery[$i]->fecharegistro = date("d/m/Y",strtotime($resultQuery[$i]->fecharegistro));
            $docref = "";
            if(!is_null($resultQuery[$i]->docmat_id) && ($resultQuery[$i]->docmat_id+0)>0){
                $docmat = DB::connection('mysql3')->select('select docmat.* from docmat where id='.$resultQuery[$i]->docmat_id);
                $docref.=$docmat[0]->numero;
            }
            $resultQuery[$i]->docmat_id = $docref;
        }

        return $resultQuery;
    }

    public function headings(): array{
        return [
            'NUMERO',
            'FECHA REGISTRO',
            'FECHA MANT./INGRESO',
            'DESCRIPCION',
            'TALLER',
            'NRO DOCMAT',
            'DOCMAT',
            'PLACA',
            'CARROCERIA/DESC.',
            'MODELO',
            'MARCA',
            'KM INICIAL',
            'KM MANT',
            'KM FINAL',
            'FECHA DOCMAT',
            'FECHA COTIZACION/SALIDA',
            'TIPO'
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
