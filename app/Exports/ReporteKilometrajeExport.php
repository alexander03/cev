<?php

namespace App\Exports;

use App\Contratista;
use App\Concesionaria;
use App\Vehiculo;
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use App\RegManVeh;
use App\RegRepVeh;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteKilometrajeExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct($placa,$estado)
    {
        $this->placa = $placa;
        $this->estado = $estado;
    }
    
    public function collection(){
        $placa = $this->placa;
        $estado = $this->estado;
        ini_set('memory_limit','900M');
        $resultado        = Vehiculo::join('kilometraje' ,'kilometraje.id','=','vehiculo.kilometraje_id')
                                ->where('placa','LIKE','%' . $placa . '%')
                                ->whereIn('contratista_id',[12,27,38])
                                ->where('concesionaria_id', $this->getConsecionariaActual())
                                ->where(function($sql) use($estado){
                                    if($estado=="1"){
                                        $sql->where(DB::raw('cast(kilometraje.limite_inf as DECIMAL)') , '>' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
                                    }elseif($estado=="2"){
                                        $sql->where(DB::raw('cast(kilometraje.limite_sup as DECIMAL)') , '>' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'))
                                            ->where(DB::raw('cast(kilometraje.limite_inf as DECIMAL)') , '<' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
                                    }elseif($estado=="3"){
                                        $sql->where(DB::raw('cast(kilometraje.limite_sup as DECIMAL)') , '<' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
                                    }
                                });  
        $resultado        = $resultado->select('vehiculo.*');
        $lista            = $resultado->get();
        $data = array();
        $c=0;
        foreach($lista as $k=>$value){
            $c=$c+1;
            if( $value->kilometraje_rec < $value->kilometraje2->limite_inf ){
                $color="";
                $background="";
            }elseif( $value->kilometraje_rec < $value->kilometraje2->limite_sup ){
                $color="";
                $background="color:blue";
            }else{
                $background="color:red";
                $color="";
            }
            //ultimos mantenimientos especiales
            $id = $value->id;
            $UltimosMant = collect([]);
            $UltimoChecklist = Checklistvehicular::where('especial',true)
                                ->where('vehiculo_id',$id)->select('id','fecha_registro as fecha', 'vehiculo_id')
                                ->orderBy('fecha_registro','desc')
                                ->get()->first();
            if($UltimoChecklist){
                $UltimosMant->offsetSet(1,$UltimoChecklist);
            }
            $UltimoControlCombustible = AbastecimientoCombustible::where('especial',true)->where('vehiculo_id',$id)
                                            ->select('id','fecha_abastecimiento as fecha', 'vehiculo_id')
                                            ->orderBy('fecha_abastecimiento','desc')
                                            ->get()->first();
            if($UltimoControlCombustible){
                $UltimosMant->offsetSet(2,$UltimoControlCombustible);
            }
            $UltimoRegRepVeh = RegRepVeh::where('especial',true)->where('ua_id',$id)
                                    ->select('id','fechasalida as fecha','ua_id as vehiculo_id')
                                    ->orderBy('fechasalida','desc')
                                    ->get()->first();
            if($UltimoRegRepVeh){
                $UltimosMant->offsetSet(3,$UltimoRegRepVeh);
            }
            $UltimoRegManVeh = RegManVeh::where('especial',true)->where('ua_id',$id)
                                    ->select('id','fechasalida as fecha','ua_id as vehiculo_id')
                                    ->orderBy('fechasalida','desc')
                                    ->get()->first();
            if($UltimoRegManVeh){
                $UltimosMant->offsetSet(4,$UltimoRegManVeh);
            }
            
            
            $FechaUltimoMantEspecial = null;

            //Verifica si ya existió antes un mant especial
            if($UltimosMant->first()){
                $UltimosMant->sortByDesc('fecha');

                $FechaUltimoMantEspecial = date("d/m/Y",strtotime($UltimosMant->first()->fecha));

            }

            if( $value->kilometraje_rec < $value->kilometraje2->limite_inf )
                $estado="Normal";
            elseif( $value->kilometraje_rec < $value->kilometraje2->limite_sup )
                $estado="Observación";
            else
                $estado="Desgastado";
            

            $data[] = array('reglakm'=>$value->kilometraje->descripcion,
                            'modelo'=>$value->modelo,
                            'placa'=>$value->placa,
                            'kilometraje_inicial'=>$value->kilometraje_inicial,
                            'kilometraje_rec'=>$value->kilometraje_rec,
                            'kilometraje_act'=>number_format($value->kilometraje_act,2,'.',''),
                            'diferencia'=>number_format($value->kilometraje2->limite_sup -  $value->kilometraje_rec,2,'.',''),
                            'estado'=>$estado);
        }
        $this->listadata = $data;
        return collect($data);
    }

    public function headings(): array{
        return [
        'Regla KM',
        'Modelo',
        'Placa',
        'KM inicial de Control',
        'KM recorrido',
        'KM actual (check/comb/mant)',
        'Diferencia KM',
        'Estado',
        ];
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z','AA','AB'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }
                $cellRange = 'A1:H1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                $event->sheet->setAutoFilter($cellRange);
                $x=1;
                foreach ($this->listadata as $key => $value) {
                    $x++;
                    if($value["estado"]=="Desgastado"){
                        $cellRange = 'A'.$x.':H'.$x;
                        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    }elseif($value["estado"]=="Observación"){
                        $cellRange = 'A'.$x.':H'.$x;
                        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                    }
                }
            }
        ];
    }  
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
