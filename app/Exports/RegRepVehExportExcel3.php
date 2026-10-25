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

class RegRepVehExportExcel3 implements FromCollection, WithHeadings, WithEvents{

    /**
    * @return \Illuminate\Support\Collection
    */
    
    //Fecha mÃ¡s antigua
    private $fechainicio;
    //Fecha mÃ¡s reciente
    private $fechafin;

    private $concesionaria;
    
    public function __construct($fecha1,$fecha2,$concesionaria){
        $this->fechainicio = $fecha1;
        $this->fechafin = $fecha2;
        $this->concesionaria = $concesionaria;
    }
    public function collection(){
        
        $fechafin = $this->fechafin;
        $resultQuery = RegRepVeh::select('taller.razonsocial','regrepveh.ordencompra','vehiculo.placa','regrepveh.kminicial AS KM_INICIAL','regrepveh.kmman AS KM_MANTENIMIENTO','regrepveh.kmfinal AS KM_FINAL','regrepveh.fechaentrada','regrepveh.fechasalida','regrepveh.tipomantenimiento AS TIPO_MANTENIMIENTO','checklistvehicular.fecha_registro as FECHA_CHECKLIST',DB::raw('concat(conductor.nombres,\'\',conductor.apellidos) as conductor'),'checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.id as checklistvehicular_id')
            -> join('vehiculo', 'vehiculo.id', '=', 'regrepveh.ua_id')
            -> join('checklistvehicular','checklistvehicular.id','=','regrepveh.checklistvehicular_id')
            -> join('conductor','conductor.id','=','checklistvehicular.conductor_id')
            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
            ->leftjoin('taller','taller.id','=','regrepveh.taller_id')
            -> join('ua', 'ua.id', '=', 'vehiculo.ua_id')
            -> whereNull('regrepveh.deleted_at')
            -> where('ua.concesionaria_id','=',$this->concesionaria)
            -> where('regrepveh.fecharegistro','>=',$this->fechainicio)
            -> where(function($sql) use($fechafin){
                    if(!is_null($fechafin) && trim($fechafin)!=""){
                        $sql->where('regrepveh.fecharegistro','<=',$fechafin);
                    }
            })
            -> get();

        $newResult = [];
        $arrId = [];

        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->fechaentrada=date("d/m/Y",strtotime($resultQuery[$i]->fechaentrada));
            $resultQuery[$i]->fechasalida=date("d/m/Y",strtotime($resultQuery[$i]->fechasalida));
            $resultQuery[$i]->FECHA_CHECKLIST=date("d/m/Y",strtotime($resultQuery[$i]->FECHA_CHECKLIST));
            $revisiones="";
            $checklistvehicular = Checklistvehicular::find($resultQuery[$i]->checklistvehicular_id);
            if(!is_null($checklistvehicular)){
                $sistema_electrico = ($checklistvehicular->sistema_electrico);
                $sistema_mecanico = ($checklistvehicular->sistema_mecanico);
                $accesorios = ($checklistvehicular->accesorios);
                $documentos = ($checklistvehicular->documentos);
                foreach ($sistema_electrico as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($sistema_mecanico as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($accesorios as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($documentos as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                $resultQuery[$i]->checklistvehicular_id = $revisiones;
            }
            array_push($newResult, $resultQuery[$i]);
            
        }
        
        $resultQuery = RegManVeh::select('taller.razonsocial','regmanveh.ordencompra','vehiculo.placa','regmanveh.kminicial AS KM_INICIAL','regmanveh.kmman AS KM_MANTENIMIENTO','regmanveh.kmfinal AS KM_FINAL','regmanveh.fechaentrada','regmanveh.fechasalida','regmanveh.tipomantenimiento AS TIPO_MANTENIMIENTO','checklistvehicular.fecha_registro as FECHA_CHECKLIST',DB::raw('concat(conductor.nombres,\'\',conductor.apellidos) as conductor'),'checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.id as checklistvehicular_id')
            -> join('vehiculo', 'vehiculo.id', '=', 'regmanveh.ua_id')
            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
            -> join('checklistvehicular','checklistvehicular.id','=','regmanveh.checklistvehicular_id')
            -> join('conductor','conductor.id','=','checklistvehicular.conductor_id')
            -> join('ua', 'ua.id', '=', 'vehiculo.ua_id')
            ->leftjoin('taller','taller.id','=','regmanveh.taller_id')
            -> whereNull('regmanveh.deleted_at')
            -> where('ua.concesionaria_id','=',$this->concesionaria)
            -> where('regmanveh.fecharegistro','>=',$this->fechainicio)
            -> where(function($sql) use($fechafin){
                    if(!is_null($fechafin) && trim($fechafin)!=""){
                        $sql->where('regmanveh.fecharegistro','<=',$fechafin);
                    }
            })
            -> get();

        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->fechaentrada=date("d/m/Y",strtotime($resultQuery[$i]->fechaentrada));
            $resultQuery[$i]->fechasalida=date("d/m/Y",strtotime($resultQuery[$i]->fechasalida));
            $resultQuery[$i]->FECHA_CHECKLIST=date("d/m/Y",strtotime($resultQuery[$i]->FECHA_CHECKLIST));
            $revisiones="";
            $checklistvehicular = Checklistvehicular::find($resultQuery[$i]->checklistvehicular_id);
            if(!is_null($checklistvehicular)){
                $sistema_electrico = ($checklistvehicular->sistema_electrico);
                $sistema_mecanico = ($checklistvehicular->sistema_mecanico);
                $accesorios = ($checklistvehicular->accesorios);
                $documentos = ($checklistvehicular->documentos);
                foreach ($sistema_electrico as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($sistema_mecanico as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($accesorios as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                foreach ($documentos as $item){
                    if($item['estado']==''){
                        $revisiones .= '* '.$item['titulo']."\n";
                    }
                }
                $resultQuery[$i]->checklistvehicular_id = $revisiones;
            }
            array_push($newResult, $resultQuery[$i]);
        }
        

        for($i = 0; $i < count($newResult); $i++) unset($newResult[$i] -> id);

        $collectionResult = collect($newResult);


        return $collectionResult;
        //return $resultQuery;
    }

    public function headings(): array{
        return [
            'TALLER',
            'ORDEN DE COMPRA',
            'PLACA',
            'KM INICIAL',
            'KM MANT',
            'KM FINAL',
            'FECHA DOCMAT',
            'FECHA COTIZACION',
            'TIPO',
            'FECHA CHECKLIST',
            'CONDUCTOR',
            'OBSERVACIONES',
            'INCIDENTES',
            'REVISIONES'
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

                $cellRange = 'A1:N1'; // All headers
                $cellRange1 = 'A1:I1'; // All headers
                $cellRange2 = 'J1:N1'; // All headers
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
