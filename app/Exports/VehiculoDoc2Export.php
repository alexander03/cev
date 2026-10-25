<?php

namespace App\Exports;

use App\Concesionaria;
use App\Vehiculo;
use App\VehiculoDocumento;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VehiculoDoc2Export implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $documentos;

    public function __construct($documentos)
    {  
        $this->documentos = $documentos;
    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $filtro           = array();
        $filtro[]         = ['concesionaria_id', $idConcAct];
        $documentos = $this->documentos;

        $resultado        = Vehiculo::join('vehiculodocument','vehiculodocument.vehiculo_id','=','vehiculo.id')
                            ->where($filtro)
                            ->whereNull('vehiculodocument.deleted_at')
                            ->where(function($query) use($documentos){
                                if($documentos!=""){
                                    if($documentos=="SI"){
                                        $query->whereRaw('(select count(*) from vehiculodocument where vehiculo_id=vehiculo.id and deleted_at is null and YEAR(fecha)-1 = '.date("Y").')>0');
                                    }else{
                                        $query->whereRaw('(select count(*) from vehiculodocument where vehiculo_id=vehiculo.id and deleted_at is null and YEAR(fecha)-1 = '.date("Y").')=0');
                                    }
                                }
                            })
                            ->orderBy('modelo', 'ASC')
                            ->orderBy('placa', 'ASC')
                            ->orderBy('vehiculodocument.fecha', 'ASC');
        $lista            = $resultado->select('vehiculo.id','vehiculodocument.fecha','vehiculodocument.tipo','vehiculodocument.precio')->get();

        $data = array();$listaVehiculo=array();$lVehiculo=array();
        foreach($lista as $obj) {
            $array = [];
            $vehiculo = Vehiculo::find($obj->id);
            if(!isset($listaVehiculo[$obj->id."-".$obj->tipo])){
                if(!isset($lVehiculo[$obj->id])){
                    $lVehiculo[$obj->id] = array("id"=>$obj->id,
                                                            "tipo"=>$obj->tipo,
                                                            "ua"=>$vehiculo->ua->codigo,
                                                            "modelo"=>$vehiculo->modelo,
                                                            "marca" => $vehiculo->marca?$vehiculo->marca->descripcion:'',
                                                            "anio" => $vehiculo->anio,
                                                            "placa" => $vehiculo->placa,
                                                        );
                }
                $listaVehiculo[$obj->id."-".$obj->tipo] = array("id"=>$obj->id,
                                                            "tipo"=>$obj->tipo,
                                                        );
                $array[] = $vehiculo->ua->codigo;
                $array[] = $vehiculo->modelo;
                if($vehiculo->marca){
                    $array[] = $vehiculo->marca->descripcion;
                }else{
                    $array[] = "";
                }
                $array[] = $vehiculo->anio;
                $array[] = $vehiculo->placa;
                $array[] = $obj->tipo;
                $array[] = $vehiculo->motor;
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
                $array[] = $vehiculo->asientos;
                $array[] = $vehiculo->chasis;
                if($vehiculo->carroceria){
                    $array[] = $vehiculo->carroceria->descripcion;
                }else{
                    $array[] = "";
                }
                if($vehiculo->kilometraje){
                    $array[] = $vehiculo->kilometraje->descripcion;
                }else{
                    $array[] = "";
                }
                $array[] = $vehiculo->color;
                $array[] = date("d/m/Y",strtotime($obj->fecha));
                $array[] = $obj->precio;
                $array[] = $vehiculo->estado;

                $data[] = $array;
            }
        }
        $tipos = ['SOAT'=>'SOAT', 'GPS' => 'GPS' , 'RTV' => 'RTV', 'POLIZA'=>'POLIZA','TARJ. PROP.'=>'TARJ. PROP.'];
        foreach ($lVehiculo as $key => $value) {
            $id = $value["id"];
            foreach($tipos as $k=>$v){
                if(!isset($listaVehiculo[$id."-".$v])){
                    $array = [];
                    $array[] = $value["ua"];
                    $array[] = $value["modelo"];
                    $array[] = $value["marca"];
                    $array[] = $value["anio"];
                    $array[] = $value["placa"];
                    $array[] = $v;
                    $data[] = $array;
                }
            }
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
            'Tipo',
            'Motor',
            'Subcontratista / Propio',
            'Area',
            'Asientos',
            'Chasis',
            'Carroceria',
            'Color',
            'Regla',
            'Fecha',
            'Precio',
            'Estado'
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
