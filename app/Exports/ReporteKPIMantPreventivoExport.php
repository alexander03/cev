<?php

namespace App\Exports;

use App\Checklistvehicular;
use App\Concesionaria;
use App\RegRepVeh;
use App\RegManVeh;
use App\Dataantigua;
use App\Vehiculo;
use App\Periodo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;

class ReporteKPIMantPreventivoExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $periodoinicial, $periodofinal, $filafinal;

    public function __construct(string $periodoinicial, string $periodofinal)
    {
        $this->periodoinicial = $periodoinicial;
        $this->periodofinal = $periodofinal;

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $this->filafinal = count($vehiculo) + 3;
    }
    
    public function collection(){
    
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $table = [];
        $total = ["TOTAL"," "," "," "," "," "," "," "];
        $subtotal = array();
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths();
        foreach ($vehiculo as $key => $value) {
            $array = [];
            $array[] = "Liviano";
            $array[] = $value->carroceria->descripcion;
            $array[] = $value->marca->descripcion;
            $array[] = $value->modelo;
            $array[] = $value->placa;
            $array[] = $value->anio;
            $array[] = $value->area->descripcion;
            $array[] = $value->kilometraje->descripcion;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                    ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                    ->where('fecha_registro','<=',$fecha_registro_final_final)
                                    ->orderBy('fecha_registro','asc')
                                    ->orderBy('hora_inicio','asc')
                                    ->first();
                    if(!is_null($check)){
                        $inicial = $check->k_inicial;
                    }else{
                        $inicial = 0;
                    }

                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                        ->where('fecha_registro','<=',$fecha_registro_final_final)
                                        ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                        ->orderBy('fecha_registro','desc')
                                        ->orderBy('hora_fin','desc')
                                        ->first();
                    if(!is_null($check)){
                        $final = $check->k_final;
                    }else{
                        $final = 0;
                    }

                    $array[] = $final - $inicial;

                    $preventivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                                ->where('regrepveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regrepveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regrepveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                                ->groupBy('regrepveh.ua_id')->first();
                    if(is_null($preventivorespuesto)){
                        $preventivorespuesto=0;
                    }else{
                        $preventivorespuesto=$preventivorespuesto->total;
                    }

                    $preventivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                                ->where('regmanveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regmanveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regmanveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                                ->groupBy('regmanveh.ua_id')->first();
                    if(is_null($preventivomano)){
                        $preventivomano=0;
                    }else{
                        $preventivomano=$preventivomano->total;
                    }

                    $array[] = $preventivorespuesto + $preventivomano;

                    if(isset($subtotal[$month["value"]])){
                        $subtotal[$month["value"]] = $subtotal[$month["value"]] + $preventivorespuesto + $preventivomano;
                    }else{
                        $subtotal[$month["value"]] = $preventivorespuesto + $preventivomano;
                    }

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','MANTENIMIENTO PREVENTIVO')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $array[] = $dataantigua->valor;
                        $array[] = $dataantigua->costo;
                        if(isset($subtotal[$month["value"]])){
                            $subtotal[$month["value"]] = $subtotal[$month["value"]] + $dataantigua->costo;
                        }else{
                            $subtotal[$month["value"]] = $dataantigua->costo;
                        }
                    }else{
                        $array[] = "";
                        $array[] = "";
                    }
                }
            }
            $table[] = $array;
        }
        foreach ($months as $month) {
            $total[] = " ";
            if(isset($subtotal[$month["value"]])){
                $total[] = $subtotal[$month["value"]];
            }else{
                $total[] = 0;
            }
        }
        $table[] = $total;

        // fusionamos el valor de $table con el de $array

        return collect($table);
    }

    private function getMonths()
    {
        setlocale(LC_TIME, 'es_ES');

        $months = [];
        $periodo = Periodo::find($this->periodoinicial);
        $month = new DateTime($periodo->fechafin);
        $month = new DateTime($month->format('Y-m')."-01");
    
        $periodo = Periodo::find($this->periodofinal);
        $end_date = new DateTime($periodo->fechafin);
        $end_date = new DateTime($end_date->format("Y-m")."-01");

        while ( $month <= $end_date ) {
            $new_month["name"] = substr(strtolower(strftime('%b', $month->getTimestamp())), 0, 3)."-".$month->format('Y');
            $new_month["value"] = $month->format('Y-m-')."01";
            $months[] = $new_month;
            $month = new DateTime(date('Y-m-d', strtotime($month->format('Y-m-d') . ' + 1 months')));
        }

        return $months;
    }

    public function headings(): array{
        $months = $this->getMonths();

        $array = ['TIPO', 'UNIDAD','MARCA', 'MODELO','PLACA','AÑO','GER','REGLA KM'];
        $array1 = [' ',' ',' ',' ',' ',' ',' ',' '];

        foreach ($months as $month) {
            $array1 = array_merge($array1, [$month['name']]);
            $array1 = array_merge($array1, [' ']);
            $array = array_merge($array,['KM Recorridos']);
            $array = array_merge($array,['Costo S/']);
        }
        return [$array1,$array];
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
                $cellRange = 'I1:EF1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');

                $cellRange = 'A2:EF2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');


                $months = $this->getMonths();
                $letraInicial = 'I';
                $numFilas = count($months); // Cambia esto al número de filas que desees agregar
                $salto = 1;

                // Convierte la letra inicial a su valor ASCII
                $inicio = ord($letraInicial);

                for ($i = 0; $i < $numFilas; $i++) {
                    $filaActual = chr($inicio + ($i * $salto)); // Convierte el valor ASCII a letra
                    $filaSiguiente = chr($inicio + ($i * $salto) + $salto); // Calcula la fila consecutiva
                    
                    if ($i > 0 && $i % 26 === 0) {
                        $filaActual = chr($inicio + intdiv($i, 26) - 1) . $filaActual;
                        $filaSiguiente = chr($inicio + intdiv($i, 26) - 1) . $filaSiguiente;
                    }
                    $cellRange = $filaActual.'1:'.$filaSiguiente.'1'; // All headers
                    //$event->sheet->mergeCells($cellRange);
                }
                $cellRange = "A".$this->filafinal.":H".$this->filafinal;
                $event->sheet->mergeCells($cellRange);
                $cellRange = "A".$this->filafinal.":EF".$this->filafinal;
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
