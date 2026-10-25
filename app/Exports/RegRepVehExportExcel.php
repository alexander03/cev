<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\RegRepVeh;
use App\RegManVeh;
use App\Preciocombustible;
use App\Vehiculo;
use App\Vehiculodocument;
use App\AbastecimientoCombustible;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\Sheet;

class RegRepVehExportExcel implements FromCollection, WithHeadings, WithEvents{

    /**
    * @return \Illuminate\Support\Collection
    */
    
    //Fecha más antigua
	private $fechainicio;
    //Fecha más reciente
	private $fechafin;

	private $concesionaria;
	private $precio;
    
    public function __construct($fecha1,$fecha2,$precio,$concesionaria){
        $this->fechainicio = $fecha1;
        $this->fechafin = $fecha2;
        $this->concesionaria = $concesionaria;
        if($precio==""){
            $this->precio = 0;
        }else{
            $this->precio = $precio;
        }
    }
    public function collection(){
        
        $fechafin = $this->fechafin;
        $resultQuery = RegRepVeh::select('categoriarepuesto.descripcion as categoria','repuesto.descripcion as DESCRIPCION','unidad.descripcion as UM','descripcionregrepveh.cantidad as CANTIDAD','descripcionregrepveh.monto AS COSTO','regrepveh.tipomantenimiento AS TIPO_MANTENIMIENTO','ua.codigo AS UA','regrepveh.fecharegistro AS FECHA_MANTENIMIENTO','regrepveh.kmman AS KM_MANTENIMIENTO','regrepveh.kminicial AS KM_INICIAL','regrepveh.kmfinal AS KM_FINAL',DB::raw('regrepveh.kmfinal - regrepveh.kminicial as RECORRIDO'),'vehiculo.modelo AS MODELO','marca.descripcion as MARCA','carroceria.descripcion as CARROCERIA','contratista.razonsocial as SUBCONTRATISTA','vehiculo.placa AS PLACA',DB::raw('\'\' as motivo'),'concesionaria.razonsocial as concesionaria')
            -> join('descripcionregrepveh', 'regrepveh.id', '=', 'descripcionregrepveh.regrepveh_id')
            -> join('vehiculo', 'vehiculo.id', '=', 'regrepveh.ua_id')
            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
            -> join('ua', 'ua.id', '=', 'vehiculo.ua_id')
            ->join('concesionaria','concesionaria.id','=','regrepveh.concesionaria_id')
            -> join('repuesto', 'repuesto.id', '=', 'descripcionregrepveh.repuesto_id')
            -> leftjoin('categoriarepuesto','categoriarepuesto.id','=','repuesto.categoriarepuesto_id')
            -> join('unidad', 'unidad.id', '=', 'repuesto.unidad_id')
            -> whereNull('regrepveh.deleted_at') 
            -> where('regrepveh.concesionaria_id','=',$this->concesionaria)
            -> where('regrepveh.fechaentrada','>=',$this->fechainicio)
            -> where(function($sql) use($fechafin){
                    if(!is_null($fechafin) && trim($fechafin)!=""){
                        $sql->where('regrepveh.fechaentrada','<=',$fechafin);
                    }
            })
            -> get();

        $newResult = [];
        $arrId = [];
       
        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->COSTO=$resultQuery[$i]->COSTO*$resultQuery[$i]->CANTIDAD;
            array_push($newResult, $resultQuery[$i]);
            
        }
        
        $resultQuery = RegManVeh::select(DB::raw('\'-\' as categoria'),'trabajo.descripcion as DESCRIPCION',DB::raw('\'Unidad\' as UM'),'descripcionregmanveh.cantidad as CANTIDAD','descripcionregmanveh.monto AS COSTO','regmanveh.tipomantenimiento AS TIPO_MANTENIMIENTO','ua.codigo AS UA','regmanveh.fecharegistro AS FECHA_MANTENIMIENTO','regmanveh.kmman AS KM_MANTENIMIENTO','regmanveh.kminicial AS KM_INICIAL','regmanveh.kmfinal AS KM_FINAL',DB::raw('regmanveh.kmfinal - regmanveh.kminicial as RECORRIDO'),'vehiculo.modelo AS MODELO','marca.descripcion as MARCA','carroceria.descripcion as CARROCERIA','contratista.razonsocial as SUBCONTRATISTA','vehiculo.placa AS PLACA',DB::raw('\'\' as motivo'),'concesionaria.razonsocial as concesionaria')
            -> join('descripcionregmanveh', 'regmanveh.id', '=', 'descripcionregmanveh.regmanveh_id')
            -> join('vehiculo', 'vehiculo.id', '=', 'regmanveh.ua_id')
            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
            -> join('ua', 'ua.id', '=', 'vehiculo.ua_id')
            ->join('concesionaria','concesionaria.id','=','regmanveh.concesionaria_id')
            -> join('trabajo', 'trabajo.id', '=', 'descripcionregmanveh.trabajo_id')
            -> whereNull('regmanveh.deleted_at')
            -> where('regmanveh.concesionaria_id','=',$this->concesionaria)
            -> where('regmanveh.fechaentrada','>=',$this->fechainicio)
            -> where(function($sql) use($fechafin){
                    if(!is_null($fechafin) && trim($fechafin)!=""){
                        $sql->where('regmanveh.fechaentrada','<=',$fechafin);
                    }
            })
            -> get();

        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->COSTO=$resultQuery[$i]->COSTO*$resultQuery[$i]->CANTIDAD;
            array_push($newResult, $resultQuery[$i]);
            
        }
        
        $resultQuery = Vehiculodocument::join('vehiculo','vehiculo.id','=','vehiculodocument.vehiculo_id')
                                ->join('ua','ua.id','=','vehiculo.ua_id')
                                ->join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                                ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
                                ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
                                ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
                                ->where('vehiculodocument.created_at','>=',$this->fechainicio." 00:00:00")
                                ->where('vehiculodocument.created_at','<=',$fechafin." 23:59:59")
                                ->select(DB::raw('\'-\' as categoria'),'vehiculodocument.tipo as DESCRIPCION',DB::raw('\'UN\' as UM'),DB::raw('\'1\' as CANTIDAD'),'vehiculodocument.precio AS COSTO',DB::raw('\'OTROS\' AS TIPO_MANTENIMIENTO'),DB::raw('ua.codigo AS UA'),DB::raw('substr(vehiculodocument.created_at,1,10) AS FECHA_MANTENIMIENTO'),DB::raw('\'-\' AS KM_MANTENIMIENTO'),DB::raw('\'-\' AS KM_INICIAL'),DB::raw('\'-\' AS KM_FINAL'),DB::raw('\'-\' as RECORRIDO'),'vehiculo.modelo AS MODELO','marca.descripcion as MARCA','carroceria.descripcion as CARROCERIA','contratista.razonsocial as SUBCONTRATISTA','vehiculo.placa AS PLACA',DB::raw('\'\' as motivo'),'concesionaria.razonsocial as concesionaria')
                                ->orderBy('vehiculodocument.created_at','desc')
                                ->get();
        
        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $resultQuery[$i]->COSTO=$resultQuery[$i]->COSTO*$resultQuery[$i]->CANTIDAD;
            array_push($newResult, $resultQuery[$i]);
            
        }
        
        $resultQuery = AbastecimientoCombustible::leftjoin('vehiculo','vehiculo.id','=','abastecimiento_combustible.vehiculo_id')
                            ->join('ua','ua.id','=','abastecimiento_combustible.ua_id')
                            ->leftjoin('marca','marca.id','=','vehiculo.marca_id')
                            ->leftjoin('carroceria','carroceria.id','=','vehiculo.carroceria_id')
                            ->leftjoin('contratista','contratista.id','=','vehiculo.contratista_id')
                            ->join('concesionaria','concesionaria.id','=','abastecimiento_combustible.concesionaria_id')
                            ->where('fecha_abastecimiento','>=',$this->fechainicio)
                            ->where('fecha_abastecimiento','<=',$this->fechafin)
                            ->whereNull('abastecimiento_combustible.equipo_id')
                            ->select(DB::raw('\'-\' as categoria'),DB::raw('\'COMBUSTIBLE\' as DESCRIPCION'),DB::raw('\'LT\' as UM'),DB::raw('abastecimiento_combustible.qtdl as CANTIDAD'),DB::raw($this->precio.' AS COSTO'),DB::raw('\'OTROS\' AS TIPO_MANTENIMIENTO'),DB::raw('ua.codigo AS UA'),'abastecimiento_combustible.fecha_abastecimiento AS FECHA_MANTENIMIENTO',DB::raw('\'-\' AS KM_MANTENIMIENTO'),DB::raw('\'-\' AS KM_INICIAL'),DB::raw('abastecimiento_combustible.km AS KM_FINAL'),DB::raw('\'-\' as RECORRIDO'),'vehiculo.modelo AS MODELO','marca.descripcion as MARCA','carroceria.descripcion as CARROCERIA',DB::raw('(case when abastecimiento_combustible.vehiculo_id is null then ua.descripcion else contratista.razonsocial end) as SUBCONTRATISTA'),'vehiculo.placa AS PLACA','abastecimiento_combustible.motivo','concesionaria.razonsocial as concesionaria','abastecimiento_combustible.tipocombustible_id')
                            ->get();
                            
        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $precio = Preciocombustible::join('detallepreciocombustible','detallepreciocombustible.preciocombustible_id','=','preciocombustible.id')
                                ->whereNull('detallepreciocombustible.deleted_at')
                                ->where('fechainicio','<=',$resultQuery[$i]->FECHA_MANTENIMIENTO)
                                ->where('fechafin','>=',$resultQuery[$i]->FECHA_MANTENIMIENTO)
                                ->where('detallepreciocombustible.tipocombustible_id','=',$resultQuery[$i]->tipocombustible_id)
                                ->select('detallepreciocombustible.precio')
                                ->first();
            if(!is_null($precio)){
                $resultQuery[$i]->COSTO=$precio->precio*$resultQuery[$i]->CANTIDAD;
            }else{
                $resultQuery[$i]->COSTO=0*$resultQuery[$i]->CANTIDAD;
            }
            array_push($newResult, $resultQuery[$i]);
            
        }    
        
        $resultQuery = AbastecimientoCombustible::join('equipo','equipo.id','=','abastecimiento_combustible.equipo_id')
                            ->join('ua','ua.id','=','abastecimiento_combustible.ua_id')
                            ->leftjoin('marca','marca.id','=','equipo.marca_id')
                            ->leftjoin('contratista','contratista.id','=','equipo.contratista_id')
                            ->join('concesionaria','concesionaria.id','=','abastecimiento_combustible.concesionaria_id')
                            ->where('fecha_abastecimiento','>=',$this->fechainicio)
                            ->where('fecha_abastecimiento','<=',$this->fechafin)
                            ->select(DB::raw('\'-\' as categoria'),DB::raw('\'COMBUSTIBLE\' as DESCRIPCION'),DB::raw('\'LT\' as UM'),DB::raw('abastecimiento_combustible.qtdl as CANTIDAD'),DB::raw($this->precio.' AS COSTO'),DB::raw('\'OTROS\' AS TIPO_MANTENIMIENTO'),DB::raw('ua.codigo AS UA'),'abastecimiento_combustible.fecha_abastecimiento AS FECHA_MANTENIMIENTO',DB::raw('\'-\' AS KM_MANTENIMIENTO'),DB::raw('\'-\' AS KM_INICIAL'),DB::raw('abastecimiento_combustible.km AS KM_FINAL'),DB::raw('\'-\' as RECORRIDO'),'equipo.modelo AS MODELO','marca.descripcion as MARCA',DB::raw('equipo.descripcion as CARROCERIA'),'contratista.razonsocial as SUBCONTRATISTA','equipo.placa AS PLACA','abastecimiento_combustible.motivo','concesionaria.razonsocial as concesionaria','abastecimiento_combustible.tipocombustible_id')
                            ->get();
                            
        for($i = 0; $i < count($resultQuery); $i++) { 
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="1"){$resultQuery[$i]->TIPO_MANTENIMIENTO='PREVENTIVO';}
            if($resultQuery[$i]->TIPO_MANTENIMIENTO=="2"){$resultQuery[$i]->TIPO_MANTENIMIENTO='CORRECTIVO';}
            $precio = Preciocombustible::join('detallepreciocombustible','detallepreciocombustible.preciocombustible_id','=','preciocombustible.id')
                                ->whereNull('detallepreciocombustible.deleted_at')
                                ->where('fechainicio','<=',$resultQuery[$i]->FECHA_MANTENIMIENTO)
                                ->where('fechafin','>=',$resultQuery[$i]->FECHA_MANTENIMIENTO)
                                ->where('detallepreciocombustible.tipocombustible_id','=',$resultQuery[$i]->tipocombustible_id)
                                ->select('detallepreciocombustible.precio')
                                ->first();
            if(!is_null($precio)){
                $resultQuery[$i]->COSTO=$precio->precio*$resultQuery[$i]->CANTIDAD;
            }else{
                $resultQuery[$i]->COSTO=0*$resultQuery[$i]->CANTIDAD;
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
            'CATEGORIA',
            'REPUESTO',
            'UM',
            'CANTIDAD',
            'COSTO',
            'TIPO MANTENIMIENTO',
            'UA',
            'FECHA DOCMAT/ABAST',
            'KM MANTENIMIENTO',
            'KM INICIAL',
            'KM FINAL',
            'RECORRIDO',
            'MODELO',
            'MARCA',
            'CARROCERIA',
            'PROPIEDAD DE',
            'PLACA',
            'MOTIVO',
            'CONCESIONARIA'
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
                
                
            }
        ];
    }       
}
