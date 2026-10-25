<?php

namespace App\Exports;

use DateTime;
use App\Ua;
use App\Controldiario;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class ReportHrsEquiposUasVertical implements FromCollection, WithHeadings, WithEvents
{
    
    //Fecha más antigua
	private $fecha1;
    //Fecha más reciente
	private $fecha2;
	private $equipo_ua;

	private $concesionaria;

    private $filaBase;
//    private $dias;

	public function __construct($fecha1,$fecha2,$equipo_ua,$concesionaria)
    {
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
        $this->equipo_ua = $equipo_ua;
        $this->concesionaria = $concesionaria;

        $base  = [
            '',
            '',
            '',
            '',
            '',
            ''
        ];

        $this->filaBase =  $base;
        
       
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $equipo_ua = $this->equipo_ua;
        $fechaInicio = $this->fecha1;
        $fechaFin = $this->fecha2;
        $concesionaria = $this->concesionaria;
        $info = Ua::join('controldiario','controldiario.ua_id','=','ua.id')
                ->join('equipo','equipo.id','=','controldiario.equipo_id')
                ->join('ua as ua2','ua2.id','=','equipo.ua_id')
                ->where('ua.concesionaria_id',$this->concesionaria)
                ->whereBetween('controldiario.fecha',[$fechaInicio,$fechaFin])
                ->where('controldiario.hora_total','>',0)
                ->whereNull('controldiario.deleted_at')
                ->where(function($q) use($equipo_ua,$concesionaria){
                    if($equipo_ua!=""){
                        $q->where('ua','like','%'.$equipo_ua."%'")->
                            where('concesionaria_id','=',$concesionaria);
                    }
            	})->select('ua.id','ua.codigo','ua.descripcion','equipo.descripcion as equipo2','controldiario.fecha','controldiario.hora_total','ua2.codigo as codigoua2')
                ->orderBY('controldiario.fecha','asc')->get();
                                        
        $collection = collect([]);

        //filas de sumas
        $sumas_uas = [];
        $valor=0;$TotalPorEquipo=0;
        //generar tabla
        $HorasUa = collect([]);
        foreach ($info as $FragUa) {
            $fila = $this->filaBase;
            $fila[0] = $FragUa->codigoua2;
            $fila[1] = $FragUa->equipo2;
            $fila[2] = $FragUa->codigo;
            $fila[3] = $FragUa->descripcion;
            $fila[4] = date("d/m/Y",strtotime($FragUa->fecha));
            $fila[5] = $FragUa->hora_total;
            $TotalPorEquipo = $TotalPorEquipo + $FragUa->hora_total;
            $HorasUa->offsetSet($valor,$fila);
            $valor++;
        }
        //SUMA TOTAL POR UA
        $suma = $this->filaBase;
        $suma[0] = 'TOTAL ';
        $suma[5] = $TotalPorEquipo;

        $HorasUa->offsetSet($valor,$suma);  
        
        //Ingreso de los datos a la tabla final
        $collection = $collection->merge($HorasUa);

        return $collection;

    }

    public function headings(): array{
        $base  = [
            'Código',
            'Descripción Equipo',
            'UA',
            'Descripción Equipo',
            'Fecha',
            'Horas Trabajadas'
        ];

        $head = $base;

        
        return  $head;
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }
                $cellRange = 'A1:AJ1'; // All headers
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
