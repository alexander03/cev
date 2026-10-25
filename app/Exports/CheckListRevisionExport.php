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

class CheckListRevisionExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $fecha_registro_inicial, $fecha_registro_final, $ua, $conductor, $lider,$modelo, $accesorio;

    public function __construct(string $fecha_registro_inicial, string $fecha_registro_final, string $ua, string $modelo, string $conductor, string $lider, string $accesorio)
    {
        $this->fecha_registro_inicial = $fecha_registro_inicial;
        $this->fecha_registro_final = $fecha_registro_final;
        $this->ua = $ua;
        $this->conductor = $conductor;
        $this->lider = $lider;
        $this->modelo = $modelo;
        $this->accesorio = $accesorio;
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
        $accesorio = $this->accesorio;

        $list = Checklistvehicular::leftjoin('equipo', 'equipo.id', '=', 'checklistvehicular.equipo_id')
            ->leftjoin('vehiculo', 'vehiculo.id', 'checklistvehicular.vehiculo_id')
            ->leftjoin('ua','ua.id','vehiculo.ua_id')
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
            ->select('checklistvehicular.fecha_registro','checklistvehicular.hora_inicio','checklistvehicular.hora_fin',DB::raw('case when checklistvehicular.equipo_id then ua2.codigo else ua.codigo end as vehiculo_ua'), DB::raw('case when checklistvehicular.equipo_id then concat(equipo.descripcion,\' / \',equipo.placa) else concat(vehiculo.modelo,\' / \',vehiculo.placa) end as placa'), 'checklistvehicular.k_inicial', 'checklistvehicular.k_final', 'checklistvehicular.lider_area', DB::raw('concat(conductor.nombres,\' \',conductor.apellidos) as conductor'),'checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.justificacion',DB::raw("'".$accesorio."' as revision")); 
        $sistema_electrico = [
            ['orden' => '1', 'id' => 'freno_emergencia', 'titulo' => 'Freno de emergencia', 'estado' => false],
            ['orden' => '2', 'id' => 'funcionamiento_tablero', 'titulo' => 'Funcionamiento de tablero','estado' => false],
            ['orden' => '3', 'id' => 'estado_bateria_funcionamiento', 'titulo' => 'Estado de batería y funcionamiento', 'estado' => false],
            ['orden' => '4', 'id' => 'funcionamiento_claxon', 'titulo' => 'Funcionamiento de claxon', 'estado' => false],
            ['orden' => '5', 'id' => 'luces_retroceso_pirata', 'titulo' => 'Luces de retroceso pirata','estado' => false],
            ['orden' => '6', 'id' => 'luces_direccional', 'titulo' => 'Luces direccional','estado' => false],
            ['orden' => '7', 'id' => 'faros_neblineros', 'titulo' => 'Faros neblineros','estado' => false],
            ['orden' => '8', 'id' => 'faros_delanteros', 'titulo' => 'Faros delanteros','estado' => false],
            ['orden' => '9', 'id' => 'faros_posteriores', 'titulo' => 'Faros posteriores','estado' => false],
            ['orden' => '10','id' => 'alarma_retroceso', 'titulo' => 'Alarma de retroceso','estado' => false],
        ];
        $sistema_mecanico = [
            ['orden' => '1', 'id' => 'nivel_liquido_freno', 'titulo' => 'Nivel liquido de freno', 'estado' => false],
            ['orden' => '2', 'id' => 'sistema_direccion', 'titulo' => 'Sistema de dirección', 'estado' => false],
            ['orden' => '3', 'id' => 'palancas_cambios', 'titulo' => 'Palancas de cambios', 'estado' => false],
            ['orden' => '4', 'id' => 'estado_neumaticos', 'titulo' => 'Estado de neumáticos', 'estado' => false],
            ['orden' => '5', 'id' => 'llantas_repuesto', 'titulo' => 'Llantas de repuesto', 'estado' => false],
            ['orden' => '6', 'id' => 'ajustes_tuercas', 'titulo' => 'Ajustes de tuercas', 'estado' => false],
            ['orden' => '7', 'id' => 'presion_llantas_libras', 'titulo' => 'Presion de llantas en libras', 'estado' => false],
            ['orden' => '8', 'id' => 'cinturon_seguridad_conductor', 'titulo' => 'Cinturon de seguridad conductor', 'estado' => false],
            ['orden' => '9', 'id' => 'cinturon_seguridad_pasajeros', 'titulo' => 'Cinturon de seguridad pasajeros', 'estado' => false],
            ['orden' => '10', 'id' => 'suspension', 'titulo' => 'Suspensión', 'estado' => false],

            ['orden' => '11', 'id' => 'sistema_freno', 'titulo' => 'Sistema de freno', 'estado' => false],
            ['orden' => '12', 'id' => 'pernos_neumaticos', 'titulo' => 'Pernos de neumáticos', 'estado' => false],
            ['orden' => '13', 'id' => 'nivel_aceite', 'titulo' => 'Nivel de aceite', 'estado' => false],
            ['orden' => '14', 'id' => 'espejos_int_ext', 'titulo' => 'Espejos int y ext', 'estado' => false],
            ['orden' => '15', 'id' => 'parachoques', 'titulo' => 'Parachoques', 'estado' => false],
            ['orden' => '16', 'id' => 'parabrisas_ventanas', 'titulo' => 'Parabrisas y ventanas', 'estado' => false],
            ['orden' => '17', 'id' => 'puertas_cabina', 'titulo' => 'Puertas de cabina', 'estado' => false],
            ['orden' => '18', 'id' => 'puertas_tolva', 'titulo' => 'Puertas de tolva', 'estado' => false],
            ['orden' => '19', 'id' => 'plumillas', 'titulo' => 'Plumillas', 'estado' => false],
            ['orden' => '20', 'id' => 'estado_carroceria', 'titulo' => 'Estado de carrocería', 'estado' => false],
        ];
        $accesorios = [
            ['orden' => '1', 'id' => 'estuche_herramientas', 'titulo' => 'Estuche de herramientas', 'estado' => false],
            ['orden' => '2', 'id' => 'estado_carga_extintor', 'titulo' => 'Estado y carga de extintor', 'estado' => false],
            ['orden' => '3', 'id' => 'botiquin', 'titulo' => 'Botiquín', 'estado' => false],
            ['orden' => '4', 'id' => 'cable_remolque', 'titulo' => 'Cable de remolque', 'estado' => false],
            ['orden' => '5', 'id' => 'tacos_seguridad_cuna_2', 'titulo' => 'Tacos de seguridad cu&ntilde;a(2)', 'estado' => false],
            ['orden' => '6', 'id' => 'llave_ruedas', 'titulo' => 'Llave de ruedas', 'estado' => false],
            ['orden' => '7', 'id' => 'kit_antiderrames', 'titulo' => 'Kit antiderrames', 'estado' => false],
            ['orden' => '8', 'id' => 'limpieza_unidad', 'titulo' => 'Limpieza de la unidad', 'estado' => false],
        ];
        $documentos = [
            ['orden' => '1', 'id' => 'tarjeta_propiedad', 'titulo' => 'Tarjeta de propiedad', 'estado' => false],
            ['orden' => '2', 'id' => 'soat', 'titulo' => 'SOAT', 'estado' => false],
            ['orden' => '3', 'id' => 'licencia_conducir', 'titulo' => 'Licencia de conducir', 'estado' => false],
            ['orden' => '4', 'id' => 'revision_tecnica', 'titulo' => 'Revisión técnica', 'estado' => false],
        ];
        $filtro_accesorio = "";
        if($accesorio!=""){
            foreach ($sistema_electrico as $key => $value) {
                if($value["id"]==$accesorio){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($sistema_mecanico as $key => $value) {
                if($value["id"]==$accesorio){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($accesorios as $key => $value) {
                if($value["id"]==$accesorio){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($documentos as $key => $value) {
                if($value["id"]==$accesorio){
                    $filtro_accesorio = $value;
                }
            }
            $filtro_accesorio = json_encode($filtro_accesorio);
            $list = $list->whereRaw("(sistema_electrico like '%".$filtro_accesorio."%' or sistema_mecanico like '%".$filtro_accesorio."%' or accesorios like '%".$filtro_accesorio."%' or documentos like '%".$filtro_accesorio."%')");
        }      
        return $list->get();
    }

    public function headings(): array{
        return [
            'FECHA REGISTRO',
            'HORA INICIO',
            'HORA FIN',
            'UA VEHICULO',
            'MODELO/PLACA',
            'K. INICIAL',
            'K. FINAL',
            'LIDER DE AREA',
            'CONDUCTOR',
            'OBSERVACIONES',
            'INCIDENTES',
            'JUSTIFICACION',
            'REVISION'
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
                $cellRange = 'A1:L1'; // All headers
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
