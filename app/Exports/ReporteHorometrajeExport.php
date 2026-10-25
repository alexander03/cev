<?php

namespace App\Exports;

use App\Contratista;
use App\Concesionaria;
use App\Equipo;
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use App\RegManVeh;
use App\RegRepVeh;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteHorometrajeExport implements FromCollection, WithHeadings, WithEvents
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
        $resultado        = Equipo::join('horometraje' ,'horometraje.id','=','equipo.horometraje_id')
                                ->where('placa','LIKE','%' . $placa . '%')
                                ->whereIn('contratista_id',[12,27,38])
                                ->where('concesionaria_id', $this->consecionariaActual())
                                ->where(function($sql) use($estado){
                                    if($estado=="1"){
                                        $sql->where(DB::raw('cast(horometraje.limite_inf as DECIMAL)') , '>' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
                                    }elseif($estado=="2"){
                                        $sql->where(DB::raw('cast(horometraje.limite_sup as DECIMAL)') , '>' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'))
                                            ->where(DB::raw('cast(horometraje.limite_inf as DECIMAL)') , '<' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
                                    }elseif($estado=="3"){
                                        $sql->where(DB::raw('cast(horometraje.limite_sup as DECIMAL)') , '<' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
                                    }
                                });
                
        $resultado        = $resultado->select('equipo.*');
        $lista            = $resultado->get();
        $data = array();
        $c=0;
        foreach($lista as $k=>$value){
            $c=$c+1;
            if( $value->horometro_rec < $value->horometraje2->limite_inf ){
                $color="";
                $background="";
            }elseif( $value->horometro_rec < $value->horometraje2->limite_sup ){
                $color="";
                $background="color:blue";
            }else{
                $background="color:red";
                $color="";
            }
            //ultimos mantenimientos especiales
            $id = $value->id;

            if( $value->horometro_rec < $value->horometraje2->limite_inf )
                $estado="Normal";
            elseif( $value->horometro_rec < $value->horometraje2->limite_sup )
                $estado="Observación";
            else
                $estado="Desgastado";
            

            $data[] = array('reglakm'=>$value->horometraje2->descripcion,
                            'descripcion'=>$value->descripcion,
                            'modelo'=>$value->modelo,
                            'placa'=>$value->placa,
                            'horometraje_inicial'=>$value->horometroinicial,
                            'horometraje_rec'=>$value->horometro_rec,
                            'horometraje_act'=>number_format($value->horometroactual,2,'.',''),
                            'diferencia'=>number_format($value->horometraje2->limite_sup -  $value->horometro_rec,2,'.',''),
                            'estado'=>$estado);
        }
        $this->listadata = $data;
        return collect($data);
    }

    public function headings(): array{
        return [
        'Regla KM',
        'Descripcion',
        'Modelo',
        'Placa',
        'Horometro inicial de Control',
        'Horometro recorrido',
        'Horometro actual (comb/mant)',
        'Diferencia Horometro',
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
