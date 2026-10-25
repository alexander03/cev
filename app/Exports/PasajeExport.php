<?php

namespace App\Exports;

use App\Concesionaria;
use App\Persona;
use App\Pasaje;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Auth;

class PasajeExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fechainicio;
    private $fechafin;
    private $tipofecha;
    private $reserva;
    private $origen;
    private $destino;
    private $personal;
    private $empresa;
    private $concesionaria;

    public function __construct($fechainicio,$fechafin,$tipofecha,$reserva,$origen,$destino,$personal,$empresa,$concesionaria)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->tipofecha = $tipofecha;
        $this->reserva = $reserva;
        $this->origen = $origen;
        $this->destino = $destino;
        $this->personal = $personal;
        $this->empresa = $empresa;
        $this->concesionaria = $concesionaria;
    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $resultado        = Pasaje::join('lugarpasaje as origen','origen.id','=','pasaje.origen_id')
                            ->join('lugarpasaje as destino','destino.id','=','pasaje.destino_id')
                            ->leftjoin('ua','ua.id','=','pasaje.ua_id')
                            ->orderBy('fechaorigen','desc');
        if($this->fechainicio!=""){
            $resultado = $resultado->where('fecha'.$this->tipofecha,'>=',DB::raw("'".$this->fechainicio." 00:00:00'"));
        }
        if($this->fechafin!=""){
            $resultado = $resultado->where('fecha'.$this->tipofecha,'<=',DB::raw("'".$this->fechafin." 23:59:59'"));
        }
        if($this->reserva!=""){
            $resultado = $resultado->where('reserva','like','%'.$this->reserva.'%');
        }
        if($this->origen!=""){
            $resultado = $resultado->where('origen.nombre','like','%'.$this->origen.'%');
        }
        if($this->destino!=""){
            $resultado = $resultado->where('destino.nombre','like','%'.$this->destino.'%');
        }
        if($this->personal!=""){
            $resultado = $resultado->where('pasaje.persona','like','%'.$this->personal.'%');
        }
        if($this->empresa!=""){
            $resultado = $resultado->where('pasaje.empresa','like','%'.$this->empresa.'%');
        }
        if($this->concesionaria!=""){
            $resultado = $resultado->where('pasaje.concesionaria_id','=',$this->concesionaria);
        }
        $lista            = $resultado->select('pasaje.*')->get();

        $data = array();
        foreach($lista as $obj) {
            $array = array();
            $array[] = $obj->persona;
            $array[] = $obj->dni;
            $array[] = (!is_null($obj->ua)?($obj->ua->codigo.' '.$obj->ua->descripcion):'-');
            $array[] = $obj->origen->nombre;
            $array[] = date("d/m/Y",strtotime($obj->fechaorigen));
            $array[] = date("H:i:s",strtotime($obj->horaorigen));
            $array[] = $obj->destino->nombre;
            //$array[] = date("d/m/Y",strtotime($obj->fechadestino));
            $array[] = date("H:i:s",strtotime($obj->horadestino));
            $array[] = $obj->numvuelo;
            $array[] = $obj->reserva;
            $array[] = $obj->aerolinea;
            $array[] = $obj->empresa;
            $data[] = $array;
        }
        return collect($data);
    }

    public function headings(): array{
        return [
            'Nombres',
            'DNI',
            'UA',
            'Origen',
            'Fecha Origen',
            'Hora Origen',
            'Destino',
            //'Fecha Destino',
            'Hora Destino',
            'Nro Vuelo',
            'Cod. Reserva',
            'Aerolinea',
            'Empresa',
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
