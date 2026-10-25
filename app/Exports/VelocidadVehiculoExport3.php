<?php

namespace App\Exports;

use App\Concesionaria;
use App\Checklistvehicular;
use App\Vehiculo;
use App\VelocidadVehiculo;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VelocidadVehiculoExport3 implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fechainicio;
    private $fechafin;
    private $localizacion;
    private $zona;

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
    
        $resultado        = VelocidadVehiculo::select('velocidad','vehiculo_id','fecha','zona','localizacion')
            -> where([
                //[ 'concesionaria_id', $idConcAct ] ,
                [ 'velocidad','>=','90']
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
            $dat = array();
            $vehiculo = Vehiculo::find($obj->vehiculo_id);
            $dat[] = $vehiculo->placa;
            $dat[] = $vehiculo->area->descripcion;
            $dat[] = $obj->fecha;
            $check = Checklistvehicular::where('vehiculo_id','=',$obj->vehiculo_id)
                                ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$obj->fecha.'\')'))
                                ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$obj->fecha.'\')'))
                                ->first();
            if(!is_null($check)){
                $dat[] = $check->conductor->nombres." ".$check->conductor->apellidos;
            }else{
                $dat[] = '-';
            }
            $dat[] = $obj->velocidad;
            $dat[] = $obj->zona;
            $dat[] = $obj->localizacion;
            $data[] = $dat;
        }
        return collect($data);
    }

    public function headings(): array{
        $arr = array();
        $arr[] = 'PLACA';
        $arr[] = 'AREA';
        $arr[] =" FECHA";
        $arr[] =" CONDUCTOR";
        $arr[] = 'KM';
        $arr[] = 'ZONA';
        $arr[] = 'LOCALIZACION';
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
                'Y', 'Z','AA','AB','AC','AD','AE','AF'
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
            }
        ];
    }  
}
