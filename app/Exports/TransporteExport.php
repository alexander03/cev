<?php

namespace App\Exports;

use App\Contratista;
use App\Controldiario;
use App\Equipo;
use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransporteExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct($fechainicial,$fechafinal)
    {
        $this->fechainicial = $fechainicial;
        $this->fechafinal = $fechafinal;
    }
    
    public function collection(){
        ini_set('memory_limit','900M');
        $list = Controldiario::
                        where('fecha','>=',$this->fechainicial)
                        ->where('fecha','<=',$this->fechafinal)
                        //->where('hora_total','>',0)
                        -> get();    
        $data = array();
        $data[] = array('item'=>'');
        $data[] = array('item'=>'');
        $c=0;
        foreach($list as $k=>$v){
            $c=$c+1;
            $data[] = array('item'=>$c,'fecha'=>$v->fecha,'turno'=>$v->turno==1?'Diurno':'Nocturno','propietario'=>$v->equipo->contratista->razonsocial,'descripcion'=>$v->equipo->descripcion,'codigo'=>$v->equipo->ua->codigo,'hrinicio'=>$v->horometro_inicial,'hrfinal'=>$v->horometro_final,
                            'horas'=>($v->horometro_final - $v->horometro_inicial)==0?"0":($v->horometro_final - $v->horometro_inicial),'ua'=>$v->ua->codigo,'descripcionua'=>$v->ua->descripcion,'tipohora'=>is_null($v->tipohora_id)?'Hora Trabajada':$v->tipohora->descripcion,'horasexpediente'=>$v->hora_total,'nroviajes'=>$v->viajes,
                            'kminicial'=>$v->km_inicial,'origen'=>$v->acceso_origen,'uaorigen'=>(is_null($v->ua_origen)?0:$v->ua_origen->codigo),'descripcionorigen'=>(is_null($v->ua_origen)?'':$v->ua_origen->descripcion),'kmdestino'=>$v->km_destino,'destino'=>$v->acceso_destino,'uadestino'=>(is_null($v->ua_destino)?0:$v->ua_destino->codigo),'descripciondestino'=>(is_null($v->ua_destino)?'':$v->ua_destino->descripcion),
                            'distanciareal'=>(is_null($v->km_inicial)?'-':(abs((integer)str_replace("+","",$v->km_inicial) - (integer)str_replace("+","",$v->km_destino))+ (integer)str_replace("+","",$v->acceso_destino)+ (integer)str_replace("+","",$v->acceso_origen))/1000),
                            'tipomaterial'=>$v->tipo_material,'volumen'=>$v->viajes*$v->equipo->capacidad_carga,'observaciones'=>$v->observaciones);
        }
        return collect($data);
    }

    public function headings(): array{
        return [[
            '',
            '',
            '',
            'DESCRIPCION',
            '',
            '',
            'HOROMETROS',
            '',
            '',
            'DESCRIPTION DE TIPO DE HORA',
            '',
            'CLASIFICACION',
            '',
            'TRANSPORTE DE MATERIAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ],
        [
        'ITEM',
        'FECHA',
        'TURNO',
        'PROPIETARIO',
        'EQUIPO',
        'CODIGO',
        'HR INICIO',
        'HR FINAL',
        'HORAS',
        'UA',
        'DESCRIPCION',
        'TIPO DE HORA',
        'HORAS EXPEDIENTE',
        'NRO VIAJES',
        'KM INICIAL',
        'ACCESO ORIGEN',
        'UA ORIGEN',
        'DESCRIPCION ORIGEN',
        'KM DESTINO',
        'ACCESO DESTINO',
        'UA DESTINO',
        'DESCRIPCION DESTINO',
        'DISTANCIA REAL',
        'TIPO MATERIAL',
        'VOLUMEN',
        'OBSERVACIONES'
        ]
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
                $event->sheet->mergeCells('D1:F1');
                $event->sheet->mergeCells('G1:I1');
                $event->sheet->mergeCells('J1:K1');
                $event->sheet->mergeCells('N1:Z1');
                $cellRange = 'A2:AA2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
                $event->sheet->setAutoFilter($cellRange);
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
