<?php

namespace App\Exports;

use App\Concesionaria;
use App\Diagnostico;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DiagnosticoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $fechainicio;
    private $fechafin;
    private $tipofecha;

    public function __construct($fechainicio,$fechafin,$tipofecha)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->tipofecha = $tipofecha;
    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        $fechainicio= $this->fechainicio;
        $fechafin= $this->fechafin;
        $tipofecha= $this->tipofecha;

        $resultado= Diagnostico::leftjoin('vehiculo','vehiculo.id','=','diagnostico.vehiculo_id')
                            ->leftjoin('equipo','equipo.id','=','diagnostico.equipo_id')
        ->where('diagnostico.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('diagnostico.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('diagnostico.'.$tipofecha,'<=', $fechafin);
            });
        /*->where(function($q) use ($filter){
            $q->Where('vehiculo.placa', 'like', '%'.$filter.'%')
              ->orWhere('vehiculo.modelo', 'like', '%'.$filter.'%')
              ->orWhere('equipo.modelo', 'like', '%'.$filter.'%')
              ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
              ->orWhere('equipo.descripcion', 'like', '%'.$filter.'%');
        });*/
        $lista            = $resultado->get();

        $data = array();
        foreach($lista as $obj) {
            $array = array();
            $array[] = $obj->numero;
            $array[] = date("d/m/Y",strtotime($obj->fecharegistro));
            if($obj->fecha!="")
                $array[] = date("d/m/Y",strtotime($obj->fecha));
            else
                $array[] = '-';
            if($obj->taller_id>0){
                $array[] = $obj->taller->razonsocial;
            }else{
                $array[] = "";
            }
            if($obj->vehiculo_id>0)
                $array[] = $obj->vehiculo->modelo;
            else
                $array[] = $obj->equipo->modelo;
            $array[] = (!is_null($obj->vehiculo)?$obj->vehiculo->placa:($obj->equipo->descripcion.' '.$obj->equipo->placa));
            $check="";
            if(!is_null($obj->checklistvehicular)){
                $check = date("d/m/Y",strtotime($obj->checklistvehicular->fecha_registro))." ".substr($obj->checklistvehicular->hora_inicio,0,5)." - ".substr($obj->checklistvehicular->hora_fin,0,5)." | ".$obj->checklistvehicular->conductor->nombres." ".$obj->checklistvehicular->conductor->apellidos." | ".$obj->checklistvehicular->observaciones." ".$obj->checklistvehicular->incidentes;
            }
            $array[] = $check;
            $array[] = $obj->recorrido;
            $array[] = $obj->revision;
            $array[] = $obj->diagnostico;
            $array[] = $obj->recomendacion;
            $array[] = $obj->cierre;
            $array[] = ($obj->tipomantenimiento=="1"?"Preventivo":"Correctivo");
            $data[] = $array;
        }

        return collect($data);
    }

    public function headings(): array{
        return [
            'Numero',
            'Fecha Registro',
            'Fecha Diag.',
            'Taller',
            'Modelo',
            'Descrip / Placa',
            'Checklist',
            'Recorrido KM / Horometro',
            'Revision',
            'Diagnostico',
            'Recomendacion',
            'Cierre',
            'Tipo'
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
