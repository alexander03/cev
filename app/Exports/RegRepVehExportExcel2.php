<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\RegRepVeh;
use App\RegManVeh;
use App\Vehiculo;
use App\Equipo;
use App\Concesionaria;
use App\Vehiculodocument;
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\Sheet;
use DateTime;

class RegRepVehExportExcel2 implements FromCollection, WithHeadings, WithEvents{


    public function __construct($fechainicial,$fechafinal,$precio,$concesionaria)
    {
        $this->fechainicial = $fechainicial;
        $this->fechafinal = $fechafinal;
        $this->concesionaria = $concesionaria;
        $this->precio = $precio;
    }

    public function collection(){
        $data = array();
        $lista = array();
        $detallepreventivorepuesto = array();
        $detallepreventivorepuesto[]="";
        $detallepreventivorepuesto[]="* MATERIALES";
        $detallepreventivorepuesto[]="=sum(D3:AZ3)";
        $detallepreventivomano = array();
        $detallepreventivomano[] = "";
        $detallepreventivomano[] = "* MANO DE OBRA";
        $detallepreventivomano[]="=sum(D4:AZ4)";
        $detallecorrectivorepuesto = array();
        $detallecorrectivorepuesto[] = "";
        $detallecorrectivorepuesto[] = "* MATERIALES";
        $detallecorrectivorepuesto[] = "=sum(D6:AZ6)";
        $detallecorrectivomano = array();
        $detallecorrectivomano[] = "";
        $detallecorrectivomano[] = "* MANO DE OBRA";
        $detallecorrectivomano[] = "=sum(D7:AZ7)";
        $detallecombustible = array();
        $detallecombustible[]="";
        $detallecombustible[]="COMBUSTIBLE";
        $detallecombustible[]="=sum(D8:AZ8)";
        $detalleotros = array();
        $detalleotros[]="";
        $detalleotros[]="OTROS";
        $detalleotros[]="=sum(D9:AZ9)";
        $detallesoat = array();
        $detallesoat[]="";
        $detallesoat[]="* SOAT";
        $detallesoat[]="=sum(D10:AZ10)";
        $detallegps = array();
        $detallegps[]="";
        $detallegps[]="* GPS";
        $detallegps[]="=sum(D11:AZ11)";
        $detallertv = array();
        $detallertv[]="";
        $detallertv[]="* RTV";
        $detallertv[]="=sum(D12:AZ12)";
        $detallepoliza = array();
        $detallepoliza[]="";
        $detallepoliza[]="* POLIZA";
        $detallepoliza[]="=sum(D13:AZ13)";
        
        
        $lista[] = array('1'=>'UNIDAD','2'=>'MARCA','3'=>'MODELO','4'=>'PLACA','5'=>'AÑO','6'=>'PREVENTIVO','7'=>'CORRECTIVO','8'=>'COMBUSTIBLE','9'=>'','10'=>'','11'=>'OTROS','12'=>'COSTO TOTAL','13'=>'KILOM/HOROM.','14'=>'KILOM/HOROM.','15'=>'RECORRIDO','16'=>'RENDIMIENTO','17'=>'GERENCIA','18'=>'UA','19'=>'PROPIEDAD DE','20'=>'CONCESIONARIA');
        $lista[] = array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'COSTO S/','7'=>'COSTO S/','8'=>'COSTO S/','9'=>'LT','10'=>'GL','11'=>'S/','12'=>'S/','13'=>'INICIAL','14'=>'FINAL','15'=>'M-L','16'=>'KM/GL');
        
        $list = Vehiculo::where('concesionaria_id','=',$this->concesionaria)->orderBy('placa','asc')->get();
        $concesionaria = Concesionaria::find($this->concesionaria);
        foreach($list as $k=>$v){
            $preventivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                            ->where('regrepveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regrepveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->where('regrepveh.tipomantenimiento','=','1')
                            ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                            ->groupBy('regrepveh.ua_id')->first();
            if(is_null($preventivorespuesto)){
                $preventivorespuesto=0;
            }else{
                $preventivorespuesto=$preventivorespuesto->total;
            }
            $correctivorepuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                            ->where('regrepveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regrepveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->where('regrepveh.tipomantenimiento','=','2')
                            ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                            ->groupBy('regrepveh.ua_id')->first();
            if(is_null($correctivorepuesto)){
                $correctivorepuesto=0;
            }else{
                $correctivorepuesto=$correctivorepuesto->total;
            }
            $preventivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                            ->where('regmanveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regmanveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->where('regmanveh.tipomantenimiento','=','1')
                            ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                            ->groupBy('regmanveh.ua_id')->first();
            if(is_null($preventivomano)){
                $preventivomano=0;
            }else{
                $preventivomano=$preventivomano->total;
            }
            
            $correctivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                            ->where('regmanveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regmanveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->where('regmanveh.tipomantenimiento','=','2')
                            ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                            ->groupBy('regmanveh.ua_id')->first();
            if(is_null($correctivomano)){
                $correctivomano=0;
            }else{
                $correctivomano=$correctivomano->total;
            }
            
            $otrorespuesto = RegRepVeh::where('regrepveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regrepveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->select(DB::raw('sum(regrepveh.otros) as total'),'regrepveh.ua_id')
                            ->groupBy('regrepveh.ua_id')->first();
            if(is_null($otrorespuesto)){
                $otrorespuesto=0;
            }else{
                $otrorespuesto=$otrorespuesto->total;
            }
            
            $otrosmano = RegManVeh::where('regmanveh.fechaentrada','>=',$this->fechainicial)
                            ->where('regmanveh.fechaentrada','<=',$this->fechafinal)
                            ->where('ua_id','=',$v->id)
                            ->select(DB::raw('sum(regmanveh.otros) as total'))
                            ->groupBy('regmanveh.ua_id')->first();
            if(is_null($otrosmano)){
                $otrosmano=0;
            }else{
                $otrosmano=$otrosmano->total;
            }
            
            $combustible = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$this->fechainicial)
                            ->where('fecha_abastecimiento','<=',$this->fechafinal)
                            ->where('vehiculo_id','=',$v->id)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'))
                            ->first();
                            
            $checklist = Checklistvehicular::where('vehiculo_id','=',$v->id)
                            ->where('fecha_registro','>=',$this->fechainicial)
                            ->orderBy('fecha_registro','asc')
                            ->limit(1)
                            ->first();
            if(!is_null($checklist)){
                $inicial = $checklist->k_inicial;
            }else{
                $inicial = 0;
            }
            $checklist = Checklistvehicular::where('vehiculo_id','=',$v->id)
                            ->where('fecha_registro','<=',$this->fechafinal)
                            ->orderBy('fecha_registro','desc')
                            ->limit(1)
                            ->first();
            if(!is_null($checklist)){
                $final = $checklist->k_final;
            }else{
                $final = 0;
            }
            
            $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$v->id)
                                ->where('created_at','>=',$this->fechainicial." 00:00:00")
                                ->where('created_at','<=',$this->fechafinal." 23:59:59")
                                ->where(DB::raw('trim(tipo)'),'like','%SOAT%')
                                ->select(DB::raw('sum(precio) as precio'))
                                ->orderBy('created_at','desc')
                                ->first();
            if(!is_null($vehiculodocument)){
                $soat = $vehiculodocument->precio;
            }else{
                $soat = 0;
            }
            
            $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$v->id)
                                ->where('created_at','>=',$this->fechainicial." 00:00:00")
                                ->where('created_at','<=',$this->fechafinal." 23:59:59")
                                ->where(DB::raw('trim(tipo)'),'like','%GPS%')
                                ->select(DB::raw('sum(precio) as precio'))
                                ->orderBy('created_at','desc')
                                ->first();
            if(!is_null($vehiculodocument)){
                $gps = $vehiculodocument->precio;
            }else{
                $gps = 0;
            }
            
            $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$v->id)
                                ->where('created_at','>=',$this->fechainicial." 00:00:00")
                                ->where('created_at','<=',$this->fechafinal." 23:59:59")
                                ->where(DB::raw('trim(tipo)'),'like','%RTV%')
                                ->select(DB::raw('sum(precio) as precio'))
                                ->orderBy('created_at','desc')
                                ->first();
            if(!is_null($vehiculodocument)){
                $rtv = $vehiculodocument->precio;
            }else{
                $rtv = 0;
            }
            
            $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$v->id)
                                ->where('created_at','>=',$this->fechainicial." 00:00:00")
                                ->where('created_at','<=',$this->fechafinal." 23:59:59")
                                ->where(DB::raw('trim(tipo)'),'like','%POLIZA%')
                                ->select(DB::raw('sum(precio) as precio'))
                                ->orderBy('created_at','desc')
                                ->first();
            if(!is_null($vehiculodocument)){
                $poliza = $vehiculodocument->precio;
            }else{
                $poliza = 0;
            }
            
            
            $detallepreventivorepuesto[]=$preventivorespuesto;
            $detallepreventivomano[] = $preventivomano;
            $detallecorrectivorepuesto[] = $correctivorepuesto;
            $detallecorrectivomano[] = $correctivomano;
            $detallecombustible[] = $this->precio*$combustible->qtdl;
            $detalleotros[] = $otrosmano + $otrorespuesto;
            $detallesoat[] = $soat;
            $detallegps[] = $gps;
            $detallertv[] = $rtv;
            $detallepoliza[] = $poliza;
            
            $lista[]=array('1'=>$v->carroceria->descripcion,'2'=>$v->marca->descripcion,'3'=>$v->modelo,'4'=>$v->placa,'5'=>$v->anio,'6'=>$preventivorespuesto+$preventivomano,'7'=>$correctivorepuesto+$correctivomano,'8'=>$this->precio*$combustible->qtdl,
                            '9'=>$combustible->qtdl,'10'=>$combustible->qtdgl,'11'=>$otrosmano + $otrorespuesto+$soat+$gps+$rtv+$poliza,'12'=>$preventivorespuesto+$preventivomano+$correctivorepuesto+$correctivomano+$otrosmano + $otrorespuesto+$soat+$gps+$rtv+$poliza+$this->precio*$combustible->qtdl,'13'=>$inicial,'14'=>$final,'15'=>$final-$inicial,
                            '16'=>($final-$inicial)/($combustible->qtdgl>0?$combustible->qtdgl:1),'17'=>$v->area->descripcion,'18'=>$v->ua->codigo,'19'=>$v->contratista->razonsocial,'20'=>$concesionaria->razonsocial);
        }
        
        $combustible2 = AbastecimientoCombustible::join('equipo','equipo.id','=','abastecimiento_combustible.equipo_id')
                            ->where('fecha_abastecimiento','>=',$this->fechainicial)
                            ->where('fecha_abastecimiento','<=',$this->fechafinal)
                            ->where('abastecimiento_combustible.concesionaria_id','=',$this->concesionaria)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'),DB::raw('equipo.id as id'))
                            ->groupBy('equipo.id')
                            ->orderBy('equipo.id')
                            ->get();
        foreach($combustible2 as $k=>$v){
            $equipo = Equipo::where('id','=',$v->id)->first();
            
            $detallepreventivorepuesto[]=0;
            $detallepreventivomano[] = 0;
            $detallecorrectivorepuesto[] = 0;
            $detallecorrectivomano[] = 0;
            $detallecombustible[] = $this->precio*$v->qtdl;
            $detalleotros[] = 0;
            $detallesoat[] = 0;
            $detallegps[] = 0;
            $detallertv[] = 0;
            $detallepoliza[] = 0;
            
            if(!is_null($equipo)){
                $lista[]=array('1'=>$equipo->descripcion,'2'=>$equipo->marca->descripcion,'3'=>$equipo->modelo,'4'=>($equipo->placa==""?"-":$equipo->placa),'5'=>$equipo->anio,'6'=>0,'7'=>0,'8'=>$this->precio*$v->qtdl,
                            '9'=>$v->qtdl,'10'=>$v->qtdgl,'11'=>0,'12'=>$this->precio*$v->qtdl,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>'','18'=>$equipo->ua->codigo,'19'=>$equipo->contratista->razonsocial,'20'=>$concesionaria->razonsocial);
            }else{
                $lista[]=array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>0,'7'=>0,'8'=>$this->precio*$v->qtdl,
                            '9'=>$v->qtdl,'10'=>$v->qtdgl,'11'=>0,'12'=>$this->precio*$v->qtdl,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>'','18'=>'','19'=>'','20'=>'');
            }
        }
    
        setlocale(LC_TIME, 'es_ES');

        $month = new DateTime($this->fechainicial);

        $mes = strtolower(strftime('%B', $month->getTimestamp()));
        
        $data[]=array('1'=>$mes,'2'=>'PREVENTIVO');
        $data[]=$detallepreventivorepuesto;
        $data[]=$detallepreventivomano;
        $data[]=array('1'=>'','2'=>'CORRECTIVO');
        $data[]=$detallecorrectivorepuesto;
        $data[]=$detallecorrectivomano;
        $data[]=$detallecombustible;
        $data[]=$detalleotros;
        $data[]=$detallesoat;
        $data[]=$detallegps;
        $data[]=$detallertv;
        $data[]=$detallepoliza;
        
        $data[]=array('1'=>'','2'=>'');
        $data[]=$lista;
        return collect($data);
    }

    public function headings(): array{
        $list = Vehiculo::where('concesionaria_id','=',$this->concesionaria)->orderBy('placa','asc')->get();
        $arr = array();
        $arr[] = 'MES';
        $arr[] = 'DETALLE';
        $arr[] = 'TOTAL MES';
        foreach($list as $k=>$v){
            $arr[]=$v->placa;
        }
        $combustible2 = AbastecimientoCombustible::join('equipo','equipo.id','=','abastecimiento_combustible.equipo_id')
                            ->where('fecha_abastecimiento','>=',$this->fechainicial)
                            ->where('fecha_abastecimiento','<=',$this->fechafinal)
                            ->where('abastecimiento_combustible.concesionaria_id','=',$this->concesionaria)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'),DB::raw('equipo.id as id'))
                            ->groupBy('equipo.id')
                            ->orderBy('equipo.id')
                            ->get();
        foreach($combustible2 as $k=>$v){
            $equipo = Equipo::where('id','=',$v->id)->first();
            //$arr[]=($equipo->placa==''?$equipo->descripcion:$equipo->placa);
            if(!is_null($equipo)){
                $arr[]="'".$equipo->ua->codigo;
            }else{
                $arr[]='';
            }
        }
        return $arr;
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
                
                /*$event->sheet->mergeCells('D1:F1');
                $event->sheet->mergeCells('G1:I1');
                $event->sheet->mergeCells('J1:K1');
                $event->sheet->mergeCells('N1:Z1');*/
                $cellRange = 'A1:AZ1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                       
                $cellRange = 'A15:AZ15'; // All headers
                $event->sheet->mergeCells('H14:J14');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');           
                
                $cellRange = 'A16:AZ16'; // All headers
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
