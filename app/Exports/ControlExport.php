<?php

namespace App\Exports;

use App\Concesionaria;
use App\Vehiculo;
use App\Conductor;
use App\Persona;
use App\Detallecontrol;
use App\Control;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Auth;

class ControlExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fechainicio;
    private $fechafin;
    private $garita;
    private $tipo;
    private $turno;
    private $placa;
    private $destino;
    private $personal;
    private $empresa;
    private $agente_id;
    private $numero;
    private $color;

    public function __construct($fechainicio,$fechafin,$tipo,$garita,$turno,$placa,$destino,$personal,$empresa,$agente_id,$numero,$color)
    {
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->garita = $garita;
        $this->tipo = $tipo;
        $this->turno = $turno;
        $this->placa = $placa;
        $this->destino = $destino;
        $this->personal = $personal;
        $this->empresa = $empresa;
        $this->agente_id = $agente_id;
        $this->numero = $numero;
        $this->color = $color;
    }

    public function collection(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
    
        $resultado        = Control::where('concesionaria_id','=',$idConcAct)->orderBy('fecha','asc');
        if($this->fechainicio!=""){
            $resultado = $resultado->where('fecha','>=',DB::raw("'".$this->fechainicio." 00:00:00'"));
        }
        if($this->fechafin!=""){
            $resultado = $resultado->where('fecha','<=',DB::raw("'".$this->fechafin." 23:59:59'"));
        }
        if($this->garita!="" && $this->garita!="0"){
            $resultado = $resultado->where('garita_id','=',$this->garita);
        }
        if($this->tipo!=""){
            $resultado = $resultado->where('tipo','like',$this->tipo);
        }
        if($this->turno!=""){
            $resultado = $resultado->where('turno_id','=',$this->turno);
        }
        if($this->placa!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where vehiculo like \'%'.$this->placa.'%\' and deleted_at is null)');
        }
        $id = $this->numero;
        if($id!=""){
            $resultado = $resultado->where('id','=',$id);
        }
        if($this->agente_id!=""){
            $resultado = $resultado->where('agente_id','=',$this->agente_id);
        }
        if($this->destino!=""){
            $resultado = $resultado->where('destino','like','%'.$this->destino.'%');
        }
        $color = $this->color;
        if($color!=""){
            $resultado = $resultado->where(function ($sql) {
                $sql = $sql->where('origen_id','=',0)
                        ->orWhere('destino_id','=',0)
                        ->orWhere(function ($sql2){
                            $sql2 = $sql2->whereRaw('id in (select control_id from detallecontrol where persona_id=0 and personawp_id=0 and deleted_at is null)');
                        });
            });
        }
        if($this->placa!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where vehiculo like \'%'.$this->placa.'%\' and deleted_at is null)');
        }
        if($this->personal!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where persona like \'%'.$this->personal.'%\' and deleted_at is null)');
        }
        if($this->empresa!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where empresa like \'%'.$this->empresa.'%\' and deleted_at is null)');
        }
        $lista            = $resultado->get();

        $data = array();
        foreach($lista as $obj) {
            $array = array();
            $array[] = $obj->garita->nombre;
            $array[] = date("d/m/Y",strtotime($obj->fecha));
            $array[] = date("H:i:s",strtotime($obj->fecha));
            $array[] = $obj->tipo;
            $array[] = $obj->turno->nombre;
            $array[] = $obj->origen;
            $array[] = $obj->destino;
            $array[] = $obj->agente->apellidos." ".$obj->agente->nombres;

            $detalle = Detallecontrol::where('control_id','=',$obj->id)->get();
            $personal = "";$km="";$vehiculo="";$empresa="";$conductor="";$empresaconductor = "";
            foreach ($detalle as $k => $v) {
                if($v->conductor=="N"){
                    if($v->persona_id=="0" && $v->personawp_id=="0"){
                        $color = "color:red";
                    }else{
                        $color = "color:black";
                    }
                    $personal .= " - ".$v->dni." ".$v->persona."\n";
                    $km = $v->km;
                    $vehiculo = $v->vehiculo;
                    $empresa .= " - ".$v->empresa."\n";
                }else{
                    $conductor = $v->persona;
                    $km = $v->km;
                    $vehiculo = $v->vehiculo;
                    $empresaconductor = $v->empresa;
                }
            }
            $array[] = $vehiculo;
            $array[] = $km;            
            $array[] = $conductor;
            $array[] = $empresaconductor;
            $array[] = $personal;
            $array[] = $empresa;
            $data[] = $array;
        }
        return collect($data);
    }

    public function headings(): array{
        return [
            'Garita',
            'Fecha',
            'Hora',
            'Tipo',
            'Turno',
            'Origen',
            'Destino',
            'Agente',
            'Vehiculo',
            'KM',
            'Conductor',
            'Empresa Conductor',
            'Pasajeros',
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
