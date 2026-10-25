<?php

namespace App\Exports;

use App\Concesionaria;
use App\Vehiculo;
use App\VelocidadVehiculo;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VelocidadVehiculoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fechainicio;
    private $fechafin;
    private $localizacion;
    private $zona;
    private $ultimo=10;

    public function __construct($fechainicio,$fechafin,$localizacion,$zona)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->localizacion = $localizacion;
        $this->zona = $zona;

    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $resultado        = VelocidadVehiculo::select(DB::raw('count(*) as cantidad'),'velocidad','vehiculo_id')
            -> where([
                [ 'concesionaria_id', $idConcAct ] 
                ])
            ->groupBy('vehiculo_id')
            ->groupBy('velocidad')
            -> orderBy('fecha', 'ASC');
        if($this->fechainicio!=""){
            $resultado = $resultado->whereRaw("fecha >= '$this->fechainicio 00:00:00'");
        }
        if($this->fechafin!=""){
            $resultado = $resultado->whereRaw("fecha <= '$this->fechafin 00:00:00'");
        }
        if($this->zona!=""){
            $resultado = $resultado->where('zona','like','%'.strtoupper(trim($this->zona)).'%');
        }
        if($this->localizacion!=""){
            $resultado = $resultado->where('localizacion','like','%'.strtoupper(trim($this->localizacion)).'%');
        }
        $lista            = $resultado -> get();
        $listaVehiculo = array();
        foreach($lista as $obj) {
            if(!isset($listaVehiculo[$obj->vehiculo_id])){
                $vehiculo = Vehiculo::find($obj->vehiculo_id);
                $listaVehiculo[$obj->vehiculo_id] = array("vehiculo_id"=>$obj->vehiculo_id,"placa"=>$vehiculo->placa,"area"=>$vehiculo->area->descripcion);
            }
            $listaVehiculo[$obj->vehiculo_id][$obj->velocidad] = $obj->cantidad;
        }
        $this->ultimo = count($listaVehiculo)+2;
        $resultado        = VelocidadVehiculo::select(DB::raw('max(velocidad) as maximo'),DB::raw('min(velocidad) as minimo'))
            -> where([
                [ 'concesionaria_id', $idConcAct ] 
                ])
            -> orderBy('fecha', 'ASC');
        if($this->fechainicio!=""){
            $resultado = $resultado->whereRaw("fecha >= '$this->fechainicio 00:00:00'");
        }
        if($this->fechafin!=""){
            $resultado = $resultado->whereRaw("fecha <= '$this->fechafin 00:00:00'");
        }
        if($this->zona!=""){
            $resultado = $resultado->where('zona','like','%'.strtoupper(trim($this->zona)).'%');
        }
        if($this->localizacion!=""){
            $resultado = $resultado->where('localizacion','like','%'.strtoupper(trim($this->localizacion)).'%');
        }
        $lista            = $resultado -> first();

        $data = array();
        $listaTotal = array();
        foreach($listaVehiculo as $k=>$v) {
            $array = array();
            $array[] = $v["placa"];
            $array[] = $v["area"];
            $total = 0;
            for ($i=$lista->minimo; $i <= $lista->maximo; $i++) { 
                if(isset($v[$i])){
                    $array[] = $v[$i];
                    $total += $v[$i];
                    if(!isset($listaTotal[$i])){
                        $listaTotal[$i] = $v[$i];
                    }else{
                        $listaTotal[$i] += $v[$i];
                    }
                }else{
                    $array[] = "";
                }
            }
            $array[] = $total;
            $data[] = $array;
        }
        $array = array();
        $array[] = "";
        $array[] = "TOTAL GENERAL";
        $totalGeneral = 0;
        for ($i=$lista->minimo; $i <= $lista->maximo; $i++) { 
            if(isset($listaTotal[$i])){
                $array[] = $listaTotal[$i];
                $totalGeneral += $listaTotal[$i];
            }else{
                $array[] = "";
            }
        }
        $array[] = $totalGeneral;
        $data[] = $array;
        return collect($data);
    }

    public function headings(): array{
        $arr = array();
        $arr[] = 'PLACA';
        $arr[] = 'AREA / KM';
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $resultado        = VelocidadVehiculo::select(DB::raw('max(velocidad) as maximo'),DB::raw('min(velocidad) as minimo'))
            -> where([
                [ 'concesionaria_id', $idConcAct ] 
                ])
            -> orderBy('fecha', 'ASC');
        if($this->fechainicio!=""){
            $resultado = $resultado->whereRaw("fecha >= '$this->fechainicio 00:00:00'");
        }
        if($this->fechafin!=""){
            $resultado = $resultado->whereRaw("fecha <= '$this->fechafin 00:00:00'");
        }
        if($this->zona!=""){
            $resultado = $resultado->where('zona','like','%'.strtoupper(trim($this->zona)).'%');
        }
        if($this->localizacion!=""){
            $resultado = $resultado->where('localizacion','like','%'.strtoupper(trim($this->localizacion)).'%');
        }
        $lista            = $resultado -> first();
        for ($i=$lista->minimo; $i <= $lista->maximo; $i++) { 
            $arr[] = $i;
        }
        $arr[]="Total General";
        return $arr;
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
                'Y', 'Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK',
                'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX',
                'AY', 'AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK',
                'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX',
                'BY', 'BZ'
                ];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }

                $cellRange = 'A1:BZ1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');

                $cellRange = 'A'.$this->ultimo.':BZ'.$this->ultimo; // All headers
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
