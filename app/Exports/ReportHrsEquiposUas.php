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


class ReportHrsEquiposUas implements FromCollection, WithHeadings, WithEvents
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
            ''
        ];

        $fechas = [];
//        $dias = 0;
        while($fecha1 != $fecha2){
            
            $fechas[] = 0;
//            $dias++;
            $fecha1 = date("Y-m-d",strtotime($fecha1."+ 1 day"));
        }
        $fechas[] = 0;
//        $dias++;

//        $this->dias = $dias;
        $this->filaBase =  array_merge($base,$fechas);
        
       
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
        $info = Ua::where('concesionaria_id',$this->concesionaria)
                ->whereHas('controlesdiarios', function($q) use($fechaInicio, $fechaFin,$equipo_ua,$concesionaria){
                    if(!is_null($fechaFin) && $fechaFin!=""){
                        $q->whereBetween('fecha',[$fechaInicio,$fechaFin])->where('hora_total','!=',null);
                    }else{
                        $q->where('fecha','>=',$fechaInicio)->where('hora_total','!=',null);
                    }
                    if($equipo_ua!=""){
                        $q->join('equipo','equipo.id','=','equipo_id')
                            ->where('equipo_id','=',DB::raw("(select id from equipo where ua_id in (select id from ua where codigo like '".$equipo_ua."' and concesionaria_id=".$concesionaria." and deleted_at is null) and deleted_at is null)"));;
                    }
            	})->select('ua.id','ua.codigo','ua.descripcion')
        		->with(['equipos' => function($q) use($fechaInicio, $fechaFin, $equipo_ua,$concesionaria){
        			$q->select('equipo.id','equipo.descripcion' ,'equipo.ua_id' )//->with('ua:id,codigo');
        			->with(['controlesdiarios' => function($q) use($fechaInicio, $fechaFin){
        				$q->select('controldiario.id','controldiario.fecha','controldiario.hora_total','controldiario.ua_id', 'controldiario.equipo_id')->where('controldiario.ua_id','=','ua.id')
        				->whereBetween('controldiario.fecha',[$fechaInicio,$fechaFin]);
        			}]);
;
        		}])->get();

        /*$info =     Ua::join('controldiario','controldiario.ua_id','=','ua.id')
                        ->join('equipo','equipo.id','=','controldiario.equipo_id')
                        ->where(function($subquery) use ($fechaInicio, $fechaFin) {
                                if ( $fechaInicio !== null ) $subquery->where('fecha', '>=', $fechaInicio);
                                if ( $fechaFin !== null ) $subquery->where('fecha', '<=', $fechaFin);
                            })
                        ->where(function($query) use($equipo_ua,$concesionaria){
                            if($equipo_ua!=""){
                                $query->whereIn('equipo.ua_id',function($sql) use($equipo_ua,$concesionaria) {
                                    $sql->select("id")
                                        ->from("ua")
                                        ->where("codigo","like","%".$equipo_ua."%")
                                        ->where("concesionaria_id","=",$concesionaria);
                                });
                            }  
                        })
                        ->select('controldiario.*','ua.codigo','ua.descripcion')->orderBy('fecha', 'desc')->get();*/
                                        
        $collection = collect([]);
        $fechaInicio = $this->fecha1;
        $fechaFin = $this->fecha2;

        //filas de sumas
        $sumas_uas = [];

        //generar tabla
        foreach ($info as $FragUa) {
            //tabla para una Ua
            $HorasUa = collect([]);

            //Equipos de ua
            $Equipos1 = $FragUa->equipos->unique('id');
            if(count($Equipos1)>1){
                $Equipos = array();
                foreach($Equipos1 as $equip){
                    $Equipos[] = $equip;
                }
            }else{
                $Equipos = $Equipos1;
            }
            
            
            $fila = $this->filaBase;
            $fila[0] = $FragUa->codigo;
            $fila[1] = $FragUa->descripcion;
            $fila[2] = $Equipos[0]->ua->codigo;
            $fila[3] = $Equipos[0]->descripcion;

            $reglas = [];
            $reglas[] = ['ua_id',$FragUa->id];
            $reglas[] = ['equipo_id',$Equipos[0]->id];
            $reglas[] = ['hora_total','>',0];

            $controles = Controldiario::whereBetween('controldiario.fecha',[$fechaInicio,$fechaFin])
                            ->where($reglas)->select('id','hora_total','fecha')->get();
            
            //Fecha de donde inicia a contar
            $fechaInicioDatatime = new DateTime($fechaInicio);

            //Acumulación de horas
            $TotalPorEquipo=0;
            //Agrega datos a la fila del equipo
            foreach ($controles as $control) {
                $fechaFinDatatime = new DateTime($control->fecha);
                $diff = $fechaInicioDatatime->diff($fechaFinDatatime);

                //inserta la hora en el día
                $fila[$diff->days + 4] +=  $control->hora_total;
                $TotalPorEquipo += $control->hora_total;
            }


            $fila[count($fila)  ] = $TotalPorEquipo;
            //$fila->take( -count($fila) + 4)->sum();

            //Agrega la primera fila de la tabla
            $HorasUa->offsetSet(0,$fila);

            //revisa el resto de equipos
            $valor=1;
            
            //print_r($Equipos);die();
            
            if( count($Equipos) > 1 && isset($Equipos[1])){
                for($valor = 1 ; $valor < count($Equipos) ; $valor++){
                    if(isset($Equipos[$valor])){//print_r($Equipos);die();
                        //Acumulación de horas
                        $TotalPorEquipo=0;
                        $fila = $this->filaBase;
    
                        $reglas = [];
                        $reglas[] = ['ua_id',$FragUa->id];
                        $reglas[] = ['equipo_id',$Equipos[$valor]->id];
                        $reglas[] = ['hora_total','>',0];
                        $controles = Controldiario::whereBetween('controldiario.fecha',[$fechaInicio,$fechaFin])
                                ->where($reglas)->select('id','hora_total','fecha')->get();
                        
                        $fila[0] = $FragUa->codigo;
                        $fila[1] = $FragUa->descripcion;
                        $fila[2] = $Equipos[$valor]->ua->codigo;
                        $fila[3] = $Equipos[$valor]->descripcion;
    
                        //Agrega datos a la fila del equipo
                        foreach ($controles as $control) {
                            $fechaFinDatatime = new DateTime($control->fecha);
                            $diff = $fechaInicioDatatime->diff($fechaFinDatatime);
    
                            //inserta la hora en el día
                            $fila[$diff->days + 4] +=  $control->hora_total;
                            $TotalPorEquipo += $control->hora_total;
    
                        }
                        $fila[count($fila)  ] = $TotalPorEquipo;
                        //$fila->take( -count($fila) + 4)->sum();
                        $HorasUa->offsetSet($valor,$fila);
                    }
                }

            }

            //SUMA TOTAL POR UA
            $suma = $this->filaBase;
            $suma[0] = 'TOTAL ' . $FragUa->codigo;
            $cols = count($suma);
            for($columna = 4; $columna <= $cols ; $columna++){
                $suma[$columna] = $HorasUa->sum($columna);
            }

            $HorasUa->offsetSet($valor,$suma);  
            
            //Ingreso de los datos a la tabla final
            $collection = $collection->merge($HorasUa);

            $sumas_uas[] = count($collection) - 1;

           
          
        }


        $SumaGeneral = $this->filaBase;
        $SumaGeneral[0] = 'TOTAL GENERAL';
        $cols = count($SumaGeneral);
        for($columna = 4; $columna <= $cols ; $columna++){
            //suma de las horas totales de cada ua por día
            $sumafila = 0;
            foreach ($sumas_uas as $fila) {

                $sumafila += $collection[$fila][$columna];
            }


            $SumaGeneral[$columna] = $sumafila;

        }
        
        $collection->offsetSet( count($collection) ,$SumaGeneral);

        return $collection;

    }

    public function headings(): array{
        $base  = [
            'UA',
            'Descripción',
            'Código',
            'Descripción Equipo'
        ];

        $fechaInicio = $this->fecha1;
        $fechaFin = $this->fecha2;

        $fechas = [];

        while($fechaInicio != $fechaFin){
        	
        	$fechas[] = substr($fechaInicio,8,2)."/".substr($fechaInicio,5,2);

        	$fechaInicio = date("Y-m-d",strtotime($fechaInicio."+ 1 day"));
        }
        $fechas[] = substr($fechaInicio,8,2)."/".substr($fechaInicio,5,2);

        $fechas[] = 'Total';

        $head = array_merge($base,$fechas);

        
        return  $head;
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ'];

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
