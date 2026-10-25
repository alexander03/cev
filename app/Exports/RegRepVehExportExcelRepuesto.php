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

class RegRepVehExportExcelRepuesto implements FromCollection, WithHeadings, WithEvents{

    /**
    * @return \Illuminate\Support\Collection
    */
    
    //Fecha mÃ¡s antigua
    private $fechainicio;
    //Fecha mÃ¡s reciente
    private $fechafin;
    private $repuesto;
    private $filter;

    private $concesionaria;
    
    public function __construct($fecha1,$fecha2,$repuesto,$concesionaria,$filter){
        $this->fechainicio = $fecha1;
        $this->fechafin = $fecha2;
        $this->repuesto = $repuesto;
        $this->concesionaria = $concesionaria;
        $this->filter = $filter;
    }
    public function collection(){
        
        $fechafin = $this->fechafin;
        $filter = $this->filter;
        $resultQuery = RegRepVeh::select('regrepveh.numero','regrepveh.fechamantenimiento','taller.razonsocial','regrepveh.telefono',DB::raw('case when regrepveh.equipo_id>0 then equipo.placa else vehiculo.placa end as placa'),'regrepveh.kminicial AS KM_INICIAL','regrepveh.kmman AS KM_MANTENIMIENTO','regrepveh.kmfinal AS KM_FINAL','regrepveh.fechaentrada','regrepveh.fechasalida','regrepveh.fecharegistro','regrepveh.tipomantenimiento AS TIPO_MANTENIMIENTO','repuesto.descripcion as repuesto','descripcionregrepveh.cantidad','descripcionregrepveh.monto')
            ->leftjoin('vehiculo', 'vehiculo.id', '=', 'regrepveh.ua_id')
            ->leftjoin('equipo','equipo.id','=','regrepveh.equipo_id')
            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
            ->leftjoin('taller','taller.id','=','regrepveh.taller_id')
            ->join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
            ->join('repuesto','repuesto.id','=','descripcionregrepveh.repuesto_id')
            -> whereNull('regrepveh.deleted_at')
            -> where('regrepveh.concesionaria_id','=',$this->concesionaria)
            -> where('repuesto.descripcion','like','%'.strtoupper($this->repuesto).'%')
            -> where('regrepveh.fecharegistro','>=',$this->fechainicio)
            -> where(function($sql) use($fechafin){
                    if(!is_null($fechafin) && trim($fechafin)!=""){
                        $sql->where('regrepveh.fecharegistro','<=',$fechafin);
                    }
            })->where(function($q) use ($filter){
                $q->where('regrepveh.cliente', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.ua_id', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.telefono', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.ordencompra', 'like', '%'.$filter.'%')
                  ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%')
                  ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.numero', 'like', '%'.$filter.'%')
                  ->orWhere('regrepveh.fechasalida', 'like', '%'.$filter.'%');
            })->orderBy('regrepveh.fechamantenimiento','desc')-> get();

        $newResult = [];
        $arrId = [];

        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->fechaentrada=date("d/m/Y",strtotime($resultQuery[$i]->fechaentrada));
            $resultQuery[$i]->fechasalida=date("d/m/Y",strtotime($resultQuery[$i]->fechasalida));
            array_push($newResult, $resultQuery[$i]);
            
        }
        
        for($i = 0; $i < count($newResult); $i++) unset($newResult[$i] -> id);

        $collectionResult = collect($newResult);


        return $collectionResult;
        //return $resultQuery;
    }

    public function headings(): array{
        return [
            'NUMERO',
            'FECHA MANT./INGRESO',
            'TALLER',
            'NRO DOCMAT',
            'PLACA',
            'KM INICIAL',
            'KM MANT',
            'KM FINAL',
            'FECHA DOCMAT',
            'FECHA COTIZACION/SALIDA',
            'FECHA REGISTRO',
            'TIPO',
            'REPUESTO',
            'CANTIDAD',
            'P. UNIT'
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

                $cellRange = 'A1:O1'; // All headers
                $cellRange1 = 'A1:L1'; // All headers
                $cellRange2 = 'M1:O1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange1)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                $event->sheet->getDelegate()->getStyle($cellRange2)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('0000FF');
                
            }
        ];
    }       
}
