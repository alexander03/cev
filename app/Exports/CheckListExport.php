<?php

namespace App\Exports;

use App\Checklistvehicular;
use App\Concesionaria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;

class CheckListExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $fecha_registro_inicial, $fecha_registro_final, $ua, $conductor, $lider,$modelo;

    public function __construct(string $fecha_registro_inicial, string $fecha_registro_final, string $ua, string $modelo, string $conductor, string $lider)
    {
        $this->fecha_registro_inicial = $fecha_registro_inicial;
        $this->fecha_registro_final = $fecha_registro_final;
        $this->ua = $ua;
        $this->conductor = $conductor;
        $this->lider = $lider;
        $this->modelo = $modelo;
    }
    
    public function collection(){
    
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $fecha_registro_inicial = $this->fecha_registro_inicial ;
        $fecha_registro_final = $this->fecha_registro_final;
        $ua = $this->ua;
        $conductor = $this->conductor;
        $lider = $this->lider;
        $modelo = $this->modelo;

        $list = Checklistvehicular::leftjoin('equipo', 'equipo.id', '=', 'checklistvehicular.equipo_id')
            ->leftjoin('vehiculo', 'vehiculo.id', 'checklistvehicular.vehiculo_id')
            ->leftjoin('ua','ua.id','vehiculo.ua_id')
            ->leftjoin('ua as uatrabajo','uatrabajo.id','vehiculo.uatrabajo_id')
            ->leftjoin('ua as ua2','ua2.id','equipo.ua_id')
            ->join('conductor', 'conductor.id', 'checklistvehicular.conductor_id')
            ->where(function($subquery) use ($fecha_registro_inicial, $fecha_registro_final) {
                if ( $fecha_registro_inicial !== null ) $subquery->where('checklistvehicular.fecha_registro', '>=', $fecha_registro_inicial);
                if ( $fecha_registro_final !== null ) $subquery->where('checklistvehicular.fecha_registro', '<=', $fecha_registro_final);
            })
            ->where(function($subquery) use ($idConcAct) {
                $subquery->where('checklistvehicular.concesionaria_id', $idConcAct);
            })
            ->where(function($subquery) use ($ua) {
                $subquery->where('ua.codigo', 'like','%'.$ua.'%')->orWhere('ua2.codigo','like','%'.$ua.'%');
            })
            ->where(function($subquery) use ($modelo) {
                if($modelo!="") $subquery->where('equipo.placa', 'like','%'.$modelo.'%')->orWhere('vehiculo.placa','like','%'.$modelo.'%')->orWhere('vehiculo.modelo','like','%'.$modelo.'%');
            })
            ->where(function($subquery) use ($conductor) {
                if($conductor!="") $subquery->where(DB::raw('concat(conductor.nombres,\' \',conductor.apellidos)'), 'like','%'.$conductor.'%');
            })
            ->where(function($subquery) use ($lider) {
                if($lider!="") $subquery->where('checklistvehicular.lider_area', 'like','%'.$lider.'%');
            })
            ->orderBy('checklistvehicular.fecha_registro', 'DESC')
            ->select('checklistvehicular.fecha_registro','checklistvehicular.hora_inicio','checklistvehicular.hora_fin',DB::raw('case when checklistvehicular.equipo_id then ua2.codigo else ua.codigo end as vehiculo_ua'),DB::raw('case when checklistvehicular.equipo_id then null else uatrabajo.codigo end as vehiculo_uatrabajo'), DB::raw('case when checklistvehicular.equipo_id then concat(equipo.descripcion,\' / \',equipo.placa) else concat(vehiculo.modelo,\' / \',vehiculo.placa) end as placa'), 'checklistvehicular.k_inicial', 'checklistvehicular.k_final', 'checklistvehicular.lider_area', DB::raw('concat(conductor.nombres,\' \',conductor.apellidos) as conductor'),'checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.justificacion'); 
        return $list->get();
    }

    public function headings(): array{
        return [
            'FECHA REGISTRO',
            'HORA INICIO',
            'HORA FIN',
            'UA VEHICULO',
            'UA TRABAJO',
            'MODELO/PLACA',
            'K. INICIAL',
            'K. FINAL',
            'LIDER DE AREA',
            'CONDUCTOR',
            'OBSERVACIONES',
            'INCIDENTES',
            'JUSTIFICACION'
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
                //$event->sheet->mergeCells($cellRange);
                $cellRange = 'A1:M1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                //$event->sheet->setAutoFilter($cellRange);
            }
        ];
    }  
}
