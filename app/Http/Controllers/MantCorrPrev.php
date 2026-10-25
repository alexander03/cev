<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Checklistvehicular;
use App\DetalleChecklistvehicular;
use App\Unidad;
use App\Equipo;
use App\Vehiculo;
use App\Conductor;
use App\Ua;
use App\Configuracion;
use App\Concesionaria;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;
use App\Contratista;
use App\Vehiculodocument;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecorridoVehiculoExport;
use App\Exports\RecorridoResumenVehiculoExport;
use App\Exports\RecorridoResumenMesVehiculoExport;
use App\Exports\RecorridoResumenDetalladoVehiculoExport;
use App\Exports\CheckListExport;
use App\Exports\CheckListRevisionExport;
use App\Exports\CheckListNoRegistradoExport;
use App\Curl;

// INICIO DE MODIFICACION
use App\Rules\RulePlacaExist;


class MantCorrPrev extends Controller
{
    protected $folderview      = 'app.mantcorrprev';
    protected $tituloAdmin     = 'Checklist vehicular';
    protected $tituloCheckListVehicular = 'Nuevo Checklist Vehicular';
    protected $tituloRegistrar = 'Registro de Checklist Vehicular';
    protected $tituloModificar = 'Modificar Checklist Vehicular';
    protected $tituloEliminar  = 'Eliminar Checklist Vehicular';
    protected $tituloActivar  = 'Activar repuesto';
    protected $rutas           = array(
        // 'createchecklistvehicular' => 'mantcorrprev.createchecklistvehicular', 
        'create' => 'mantcorrprev.create',
        // 'createrepuesto' => 'mantcorrprev.createrepuesto',
        'buscarporua' => 'mantcorrprev.buscarporua',
        'edit'   => 'mantcorrprev.edit', 
        'delete' => 'mantcorrprev.eliminar',
        'activar' => 'mantcorrprev.activar',
        'search' => 'mantcorrprev.buscar',
        'store' => 'mantcorrprev.store',
        'index'  => 'mantcorrprev.index',
        'justificar' => 'mantcorrprev.justificar',
    );

    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 2)->first()->valor));
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Checklistvehicular';
        // $filter           = Libreria::getParam($request->input('filter'));
        // $estado           = $request->input('estado');
        // $unidad           = $request->input('unidad');
        $fecha_registro_inicial = $request->input('fecha_registro_inicial');
        $fecha_registro_final   = $request->input('fecha_registro_final');
        $ua = $request->input('ua');
        $modelo = $request->input('modelo');
        $conductor = $request->input('conductor');
        $lider = $request->input('txtLider');
        //\DB::enableQueryLog();
        $resultado        = Checklistvehicular::getFilter($fecha_registro_inicial, $fecha_registro_final,$ua,$modelo,$conductor,$lider);
        $sistema_electrico = [
            ['orden' => '1', 'id' => 'freno_emergencia', 'titulo' => 'Freno de emergencia', 'estado' => false],
            ['orden' => '2', 'id' => 'funcionamiento_tablero', 'titulo' => 'Funcionamiento de tablero','estado' => false],
            ['orden' => '3', 'id' => 'estado_bateria_funcionamiento', 'titulo' => 'Estado de bater�a y funcionamiento', 'estado' => false],
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
            ['orden' => '2', 'id' => 'sistema_direccion', 'titulo' => 'Sistema de direcci�n', 'estado' => false],
            ['orden' => '3', 'id' => 'palancas_cambios', 'titulo' => 'Palancas de cambios', 'estado' => false],
            ['orden' => '4', 'id' => 'estado_neumaticos', 'titulo' => 'Estado de neum�ticos', 'estado' => false],
            ['orden' => '5', 'id' => 'llantas_repuesto', 'titulo' => 'Llantas de repuesto', 'estado' => false],
            ['orden' => '6', 'id' => 'ajustes_tuercas', 'titulo' => 'Ajustes de tuercas', 'estado' => false],
            ['orden' => '7', 'id' => 'presion_llantas_libras', 'titulo' => 'Presion de llantas en libras', 'estado' => false],
            ['orden' => '8', 'id' => 'cinturon_seguridad_conductor', 'titulo' => 'Cinturon de seguridad conductor', 'estado' => false],
            ['orden' => '9', 'id' => 'cinturon_seguridad_pasajeros', 'titulo' => 'Cinturon de seguridad pasajeros', 'estado' => false],
            ['orden' => '10', 'id' => 'suspension', 'titulo' => 'Suspensi�n', 'estado' => false],

            ['orden' => '11', 'id' => 'sistema_freno', 'titulo' => 'Sistema de freno', 'estado' => false],
            ['orden' => '12', 'id' => 'pernos_neumaticos', 'titulo' => 'Pernos de neum�ticos', 'estado' => false],
            ['orden' => '13', 'id' => 'nivel_aceite', 'titulo' => 'Nivel de aceite', 'estado' => false],
            ['orden' => '14', 'id' => 'espejos_int_ext', 'titulo' => 'Espejos int y ext', 'estado' => false],
            ['orden' => '15', 'id' => 'parachoques', 'titulo' => 'Parachoques', 'estado' => false],
            ['orden' => '16', 'id' => 'parabrisas_ventanas', 'titulo' => 'Parabrisas y ventanas', 'estado' => false],
            ['orden' => '17', 'id' => 'puertas_cabina', 'titulo' => 'Puertas de cabina', 'estado' => false],
            ['orden' => '18', 'id' => 'puertas_tolva', 'titulo' => 'Puertas de tolva', 'estado' => false],
            ['orden' => '19', 'id' => 'plumillas', 'titulo' => 'Plumillas', 'estado' => false],
            ['orden' => '20', 'id' => 'estado_carroceria', 'titulo' => 'Estado de carrocer�a', 'estado' => false],
        ];
        $accesorios = [
            ['orden' => '1', 'id' => 'estuche_herramientas', 'titulo' => 'Estuche de herramientas', 'estado' => false],
            ['orden' => '2', 'id' => 'estado_carga_extintor', 'titulo' => 'Estado y carga de extintor', 'estado' => false],
            ['orden' => '3', 'id' => 'botiquin', 'titulo' => 'Botiqu�n', 'estado' => false],
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
            ['orden' => '4', 'id' => 'revision_tecnica', 'titulo' => 'Revisi�n t�cnica', 'estado' => false],
        ];
        $filtro_accesorio = "";
        if($request->input('accesorio')!=""){
            foreach ($sistema_electrico as $key => $value) {
                if($value["id"]==$request->input('accesorio')){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($sistema_mecanico as $key => $value) {
                if($value["id"]==$request->input('accesorio')){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($accesorios as $key => $value) {
                if($value["id"]==$request->input('accesorio')){
                    $filtro_accesorio = $value;
                }
            }
            foreach ($documentos as $key => $value) {
                if($value["id"]==$request->input('accesorio')){
                    $filtro_accesorio = $value;
                }
            }
            $filtro_accesorio = json_encode($filtro_accesorio);
            $resultado = $resultado->whereRaw("(sistema_electrico like '%".$filtro_accesorio."%' or sistema_mecanico like '%".$filtro_accesorio."%' or accesorios like '%".$filtro_accesorio."%' or documentos like '%".$filtro_accesorio."%')");
        }
        $lista            = $resultado->get();
        $user = auth()->user();
        //dd(\DB::getQueryLog());
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $cabecera[]       = array('valor' => 'F. Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA Vehiculo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA Trabajo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo / Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'K. Inicial', 'numero' => '1');
        $cabecera[]       = array('valor' => 'K. Final', 'numero' => '1');
        $cabecera[]       = array('valor' => 'K. Recorrido', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Lider del area', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Conductor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Observaciones', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Incidentes', 'numero' => '1');
        if($user->tipouser_id==1 || $user->tipouser_id==6 || $user->tipouser_id==12){
            $cabecera[]       = array('valor' => 'Justificacion', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'F. Bitacora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_activar  = $this->tituloActivar;
        $ruta             = $this->rutas;
        
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_registrar', 'titulo_activar','ruta','user'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Checklistvehicular';
        $title            = $this->tituloAdmin;
        $tituloCheckListVehicular = $this->tituloCheckListVehicular;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = auth()->user();
        
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'tituloCheckListVehicular','tituloRegistrar', 'ruta','user'));
    }

    public function create(Request $request) {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Checklistvehicular';
        $checklistvehicular = null;
        $unidad_placa = null;
        $unidad_descripcion = null;
        $formData = array('mantcorrprev.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        $user = auth()->user();
        if($user->tipouser->id==7){
            $conductor = Conductor::leftjoin('lider','lider.id','=','conductor.lider_id')
                    ->select('conductor.id', 'dni', DB::raw("CONCAT(conductor.nombres, ' ', apellidos) AS nombres"),DB::raw('(case when lider_id>0 then lider.nombres else \'-\' end) as lider'))
                    ->where(DB::raw('concat(trim(conductor.nombres),\' \',trim(apellidos))'), 'like', '%'.trim(strtoupper($user->name)).'%')->first();   
        }else{
            $conductor = [
                'id' => null,
                'dni' => '',
                'nombres' => '',
                'lider' => ''
            ];
        }
        // $arrConductores = Conductor::getAll();
        // $cboConductores = array('' => 'Seleccione');
        // foreach($arrConductores as $k=>$v){
        //     $cboConductores += array($v->id => $v->nombres . $v->apellidos);
        // }
        $detalle = array();
        return view($this->folderview.'.mant_checklistvehicular')->with(compact('checklistvehicular', 'formData', 'entidad', 'unidad_placa', 'unidad_descripcion', 'conductor', 'boton', 'listar','user','detalle'));
    }

    public function store(Request $request) {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');

        $today = \Carbon\Carbon::now('America/Lima')->toDateString();
        $min = $request->all()['k_inicial'];

        //BUSCAR EQUIPO/VEHICULO KM
        $placa = $request->input('unidad_placa');
        $equipo = Equipo::where('placa', $placa)->first();

        if(auth()->user()->tipouser_id!="1" && auth()->user()->tipouser_id!="22" && auth()->user()->id!="261"){//todos los usuario menos el admin
            if($equipo != null) {
                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_fin','desc')->first();
                if(!is_null($obj) && ($request->input('k_inicial')+0)<> ($obj->k_final+0)){
                    $dat = array("km" => ["El ultimo km es:  ".round($obj->k_final,2)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km inicial."],"id"=>$obj->id);
                    return json_encode($dat);
                }

                //para fecha mayor
                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                        ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                        ->orderBy('fecha_registro','asc')->orderBY('hora_inicio','asc')->first();
                if(!is_null($obj) && ($request->input('k_final')+0)<> 0 &&  ($request->input('k_final')+0)>$obj->k_inicial){
                    $dat = array("km" => ["El siguiente km es:  ".round($obj->k_inicial,2)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km final."],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->where('fecha_registro','like',$request->input('fecha_registro'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                
                if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }
                
            }else {
                $vehiculo = Vehiculo::where('placa', $placa)->first();
                if($vehiculo->saltarkm=="N"){
                    $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_fin','desc')->first();
                    if(!is_null($obj) && ($request->input('k_inicial')+0)<> ($obj->k_final+0)){
                        $dat = array("km" => ["El ultimo km es: ".round($obj->k_final,0)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km inicial."],"id"=>$obj->id);
                        return json_encode($dat);
                    }

                    //para fecha mayor
                    $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                            ->orderBy('fecha_registro','asc')->orderBY('hora_inicio','asc')->first();
                    if(!is_null($obj) && ($request->input('k_final')+0)<> 0 &&  ($request->input('k_final')+0)>$obj->k_inicial){
                        $dat = array("km" => ["El siguiente km es:  ".round($obj->k_inicial,2)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km final."],"id"=>$obj->id);
                        return json_encode($dat);
                    }
                }
                $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                
                if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->where('fecha_registro','like',$request->input('fecha_registro'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                
                if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }
                
            }
            if($request->input('conductor_id')!="26"){//MOISES QUEZADA
                $obj = Checklistvehicular::where('conductor_id','=',$request->input('conductor_id'))
                                ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                                ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                                ->where('fecha_registro','like',$request->input('fecha_registro'))
                                ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                
                if(!is_null($obj) && !is_null($obj->vehiculo)){
                    $dat = array("km" => ["Conductor ya tiene registrado Checklist con otro vehiculo (".$obj->vehiculo->placa.") en el mismo horario"],"id"=>$obj->id);
                    return json_encode($dat);
                }
            }
        

            if(($request->input('k_inicial')+0)== ($request->input('k_final')+0)){
                $dat = array("km" => ["Kilometraje final no puede ser el mismo al inicial."]);
                return json_encode($dat);
            }
            if(($request->input('k_final')+0)>0){
                if(($request->input('k_final')+0)<($request->input('k_inicial')+0)){
                    $dat = array("km" => ["Kilometraje final no puede ser el menor al inicial."]);
                    return json_encode($dat);   
                }
            }
            if((($request->input('k_final')+0) - ($request->input('k_inicial')+0)) >500){
                $dat = array("km" => ["Kilometraje final no puede ser mayor a 500KM del kilometraje inicial"]);
                return json_encode($dat);   
            }
            
            $datemin = strtotime("-".MIN_DIAS." day",strtotime(date("Y-m-d")));
            $datemax = strtotime(date("Y-m-d"));
            if($datemin > strtotime($request->input('fecha_registro'))){
                $dat = array("fecha_registro" => ["Fecha de Registro incorrecta, no debe manipular los inputs"]);
                return json_encode($dat);   
            }

            if($datemax < strtotime($request->input('fecha_registro'))){
                $dat = array("fecha_registro" => ["Fecha de Registro incorrecta, no debe manipular los inputs"]);
                return json_encode($dat);   
            }

        }

        $reglas     = array(
            //'fecha_registro' => 'required|date|after_or_equal:' . $today,
            'fecha_registro' => 'required|date',
            'unidad_placa' => (new RulePlacaExist()),
            'k_inicial' => 'required|numeric|min:0',
            //'k_final' => 'required|numeric|min:' . ($min + 1),
            'lider_area' => 'required|max:300',
            'conductor_id' => 'exists:App\Conductor,id',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'ua_id_trabajo' => 'required',
        );

        $mensajes = array(
            'fecha_registro.required' => 'Debe seleccionar una fecha',
            'fecha_registro.date' => 'La fecha tiene formato incorrecto',
            'hora_inicio.required' => 'Debe indicar una hora de inicio',
            'hora_fin.required' => 'Debe indicar una hora de fin',
            //'fecha_registro.after_or_equal' => 'La fecha ingresada es incorrecta',
            'unidad_placa.required' => 'Debe ingresar una unidad placa',
            'unidad_placa.regex' => 'La unidad placa ingresada es incorrecta',
            'k_inicial.required' => 'Debe ingresar un kilometraje inicial',
            'k_inicial.min' => 'El kilometraje inicial ingresado es incorrecto',
            //'k_final.required' => 'Debe ingresar un kilometraje final',
            //'k_final.min' => 'El kilometraje final ingresado es incorrecto',
            'lider_area.required' => 'Debe ingresar un lider de area',
            'lider_area.max' => 'El nombre del lider de area debe tener max. 300 caracteres',
            'conductor_id.exists' => 'Debe ingresar un conductor',
            'hora_fin.after' => 'La hora fin debe ser mayor a la hora de inicio',
            'ua_id_trabajo.required' => 'Debe registrar una ua de trabajo',
        );
        
        $validacion = Validator::make($request->all(), $reglas, $mensajes);

        if ( $validacion->fails() ) {
            return $validacion->messages()->toJson();
        }

        $uaList = $request->input('uaList');
        $KmInicialList = $request->input('KmInicialList');
        $KmFinalList = $request->input('KmFinalList');
        $HoraInicioList = $request->input('HoraInicioList');
        $HoraFinList = $request->input('HoraFinList');

        if($uaList!=""){
            $kmMax = 0;
            $kmMin = 0;
            $horaMin = 0;
            $horaMax = 0;
            for ($i = 0; $i < count($uaList); $i++) {
                if(($KmInicialList[$i]+0) < ($request->input('k_inicial')+0)){
                    $dat = array("km_inicial" => ["Kilometraje Inicial del detalle no puede ser menor al Kilometraje Inicial del checklist"]);
                    return json_encode($dat);   
                }
                if(($KmFinalList[$i]+0) > ($request->input('k_final')+0) && ($request->input('k_final')+0)>0){
                    $dat = array("km_inicial" => ["Kilometraje Final del detalle no puede ser mayor al Kilometraje Final del checklist"]);
                    return json_encode($dat);   
                }
                if(strtotime("2024-01-01 ".$HoraInicioList[$i]) < strtotime("2024-01-01 ".$request->input('hora_inicio'))){
                    $dat = array("km_inicial" => ["Hora Inicial del detalle no puede ser menor a la Hora Inicial del checklist"]);
                    return json_encode($dat);   
                }
                if(strtotime("2024-01-01 ".$HoraFinList[$i]) > strtotime("2024-01-01 ".$request->input('hora_fin'))){
                    $dat = array("km_final" => ["Hora Final del detalle no puede ser mayor a la Hora Final del checklist"]);
                    return json_encode($dat);   
                }
                if(($KmFinalList[$i]+0)>$kmMax){
                    $kmMax = $KmFinalList[$i]+0;
                }
                if($kmMin==0){
                    $kmMin = $KmInicialList[$i]+0;
                }
                if(($KmInicialList[$i]+0)<$kmMin){
                    $kmMin = $KmInicialList[$i]+0;
                }

                if(strtotime("2024-01-01 ".($HoraFinList[$i]))>$horaMax){
                    $horaMax = strtotime("2024-01-01 ".$HoraFinList[$i]);
                }
                if($horaMin==0){
                    $horaMin = strtotime("2024-01-01 ".$HoraInicioList[$i]);
                }
                if(strtotime("2024-01-01 ".$HoraInicioList[$i])<$horaMin){
                    $kmMin = strtotime("2024-01-01 ".$HoraInicioList[$i]);
                }
            }
            if($kmMin <> ($request->input('k_inicial')+0)){
                $dat = array("km_inicial" => ["Kilometraje Inicial del detalle no coincide al Kilometraje Inicial del checklist"]);
                return json_encode($dat);
            }
            if($kmMax <> ($request->input('k_final')+0) && ($request->input('k_final')+0)!="0"){
                $dat = array("km_inicial" => ["Kilometraje Final del detalle no coincide al Kilometraje Final del checklist"]);
                return json_encode($dat);   
            }
            if($horaMin <> strtotime("2024-01-01 ".$request->input('hora_inicio'))){
                $dat = array("km_inicial" => ["Hora Inicial del detalle no coincide con la Hora Inicial del checklist"]);
                return json_encode($dat);
            }
            if($horaMax <> strtotime("2024-01-01 ".$request->input('hora_fin'))){
                $dat = array("km_inicial" => ["Hora Final del detalle no coincide con la Hora Final del checklist"]);
                return json_encode($dat);   
            }
        }

        $id=0;
        $error = DB::transaction(function() use($request,&$id){
            
            $checklistvehicular = new Checklistvehicular();
            $checklistvehicular->fecha_registro= $request->input('fecha_registro');

            $placa = $request->input('unidad_placa');
            $equipo = Equipo::where('placa', $placa)->first();
            if($equipo != null) {
                $checklistvehicular->equipo_id= $equipo->id;
                $equipo->horometroactual=$request->input('k_final');
                $equipo->save();
            }else {
                $vehiculo = Vehiculo::where('placa', $placa)->first();
                $checklistvehicular->vehiculo_id= $vehiculo->id;

                //ACTUALIZO K. RECORRIDO DEL VEHICULO
                if(($request->input('k_final')+0)>$vehiculo->kilometraje_act){
                    $vehiculo->kilometraje_act = $request->input('k_final');
                    $vehiculo->save();
                }
            }

            $checklistvehicular->hora_inicio = $request->input('hora_inicio');
            $checklistvehicular->hora_fin = $request->input('hora_fin');
            $checklistvehicular->k_inicial = $request->input('k_inicial');
            if($request->input('k_final')!="0"){
                $checklistvehicular->k_final = $request->input('k_final');
            }
            $checklistvehicular->lider_area = mb_strtoupper($request->input('lider_area'), 'utf-8');
            $checklistvehicular->conductor_id= $request->input('conductor_id');
            $checklistvehicular->observaciones = $request->input('observaciones');
            $checklistvehicular->incidentes = $request->input('incidentes');
            $checklistvehicular->sistema_electrico = json_decode($request->input('sistema_electrico'));
            $checklistvehicular->sistema_mecanico = json_decode($request->input('sistema_mecanico'));
            $checklistvehicular->accesorios = json_decode($request->input('accesorios'));
            $checklistvehicular->documentos = json_decode($request->input('documentos'));
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $checklistvehicular->ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            if($request -> input('ua_id_trabajo')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id_trabajo')) -> get();
                $checklistvehicular->uatrabajo_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            $checklistvehicular->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $checklistvehicular->save();

            $uaList = $request->input('uaList');
            $KmInicialList = $request->input('KmInicialList');
            $KmFinalList = $request->input('KmFinalList');
            $HoraInicioList = $request->input('HoraInicioList');
            $HoraFinList = $request->input('HoraFinList');

            if($uaList!=""){
                for ($i = 0; $i < count($uaList); $i++) {
                    $detalle = new DetalleChecklistvehicular();
                    $detalle -> checklistvehicular_id =$checklistvehicular->id;
                    $uaDB =  Ua::where('codigo', $uaList[$i]) -> get();
                    $detalle -> ua_id  = $uaDB[0]->id;
                    $detalle -> km_inicial  = $KmInicialList[$i];
                    $detalle -> km_final  = $KmFinalList[$i];
                    $detalle -> hora_inicio  = $HoraInicioList[$i];
                    $detalle -> hora_fin  = $HoraFinList[$i];
                    $detalle -> save();
                }
            }

            $id = $checklistvehicular->id;
            event( new UserHasCreatedOrDeleted($checklistvehicular->id,'checklistvehicular', auth()->user()->id,'crear'));

        });
        if(str_contains($request->input('sistema_electrico'),'"estado":false') || str_contains($request->input('sistema_mecanico'),'"estado":false')  || str_contains($request->input('accesorios'),'"estado":false') || str_contains($request->input('documentos'),'"estado":false') || trim($request->input('observaciones'))!="" || trim($request->input('incidentes'))!=""){
            /*$cc = new Curl(false,'https://h2olmos.pe/cev/PHPMailer/notificacionChecklist.php');
            $url = 'https://h2olmos.pe/cev/PHPMailer/notificacionChecklist.php?id='.$id;
            $Page = $cc->get($url,array());
            $datos = json_decode($Page);*/
        }
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $checklistvehicular = Checklistvehicular::find($id);
        
        if($checklistvehicular->equipo_id != '') {
            $equipo = Equipo::find($checklistvehicular->equipo_id);
            $unidad_placa = $equipo->placa;
            $unidad_descripcion = $equipo->descripcion;
        } else {
            $vehiculo = Vehiculo::find($checklistvehicular->vehiculo_id);
            $unidad_placa = $vehiculo->placa;
            $unidad_descripcion = $vehiculo->modelo;

            //ACTUALIZO H. ACTUAL DEL VEHICULO
            $vehiculo->kilometraje_rec =  $request->input('k_final') - $vehiculo->kilometraje_act;
            $vehiculo->save();
        }

        $entidad  = 'Checklistvehicular';
        $formData = array('mantcorrprev.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $conductor = Conductor::leftjoin('lider','lider.id','=','conductor.lider_id')
                    ->select('conductor.id', 'dni', DB::raw("CONCAT(conductor.nombres, ' ', apellidos) AS nombres"),DB::raw('(case when lider_id>0 then lider.nombres else \'-\' end) as lider'))->where('conductor.id', '=', $checklistvehicular->conductor_id)->first();
        // $arrConductores      = Conductor::getAll();
        // $cboConductores = array('' => 'Seleccione');
        // foreach($arrConductores as $k=>$v){
        //     $cboConductores += array($v->id => $v->nombres . $v->apellidos);
        // }
        $user = auth()->user();
        $sistema_electrico = $checklistvehicular->sistema_electrico;
        $sistema_mecanico = $checklistvehicular->sistema_mecanico;
        $accesorios = $checklistvehicular->accesorios;
        $documentos = $checklistvehicular->documentos;

        $detalle = DetalleChecklistvehicular::where('checklistvehicular_id','=',$id)->get();
        return view($this->folderview.'.mant_checklistvehicular')
            ->with(compact('checklistvehicular', 'formData', 'entidad', 'boton', 'unidad_placa', 'unidad_descripcion', 'conductor', 'sistema_electrico', 'sistema_mecanico', 'accesorios', 'documentos', 'listar','user','detalle'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) {
            return $existe;
        }
        
        $today = \Carbon\Carbon::now('America/Lima')->toDateString();
        $min = $request->all()['k_inicial'];

        //BUSCAR EQUIPO/VEHICULO KM
        $placa = $request->input('unidad_placa');
        $equipo = Equipo::where('placa', $placa)->first();

        if(auth()->user()->tipouser_id!="1"  && auth()->user()->tipouser_id!="22" && auth()->user()->id!="261"){//todos los usuario menos el admin
            if($equipo != null) {
                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_fin','desc')->first();
                if(!is_null($obj) && ($request->input('k_inicial')+0)<> ($obj->k_final+0)){
                    $dat = array("km" => ["El ultimo km es:  ".round($obj->k_final,0)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km inicial."],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->whereNotIn('id',[$id])
                            ->orderBy('fecha_registro','asc')->orderBY('hora_fin','asc')->first();
                if(!is_null($obj) && ($request->input('k_final')+0)<> ($obj->k_inicial+0)){
                    $dat = array("km" => ["El km final deberia ser:  ".round($obj->k_inicial,0)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km final."],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                /*if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }*/
            }else {
                $vehiculo = Vehiculo::where('placa', $placa)->first();
                if($vehiculo->saltarkm=="N"){
                    $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_fin','desc')->first();
                    if(!is_null($obj) && ($request->input('k_inicial')+0)<> ($obj->k_final+0)){
                        $dat = array("km" => ["El ultimo km es: ".round($obj->k_final,0)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km inicial."],"id"=>$obj->id);
                        return json_encode($dat);
                    }

                    //para fecha mayor
                    $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                            ->orderBy('fecha_registro','asc')->orderBY('hora_inicio','asc')->first();
                    if(!is_null($obj) && ($request->input('k_final')+0)<> 0 &&  ($request->input('k_final')+0)>$obj->k_inicial){
                        $dat = array("km" => ["El siguiente km es:  ".round($obj->k_inicial,2)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km final."],"id"=>$obj->id);
                        return json_encode($dat);
                    }
                }

                $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                        ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                        ->whereNotIn('id',[$id])
                        ->orderBy('fecha_registro','asc')->orderBY('hora_fin','asc')->first();
                if(!is_null($obj) && ($request->input('k_final')+0)<> ($obj->k_inicial+0)){
                    $dat = array("km" => ["El km final deberia ser: ".round($obj->k_inicial,0)." del ".date("d/m/Y",strtotime($obj->fecha_registro)).", verifique su km final."],"id"=>$obj->id);
                    return json_encode($dat);
                }

                $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                            ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                
                /*if(!is_null($obj)){
                    $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                    return json_encode($dat);
                }*/
            }
            if($request->input('conductor_id')!="26"){//MOISES QUEZADA
                $obj = Checklistvehicular::where('conductor_id','=',$request->input('conductor_id'))
                                    ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                                    ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                                    ->where('fecha_registro','like',$request->input('fecha_registro'))
                                    ->whereNotIn('id',[$id])
                                    ->orderBy('fecha_registro','desc')->orderBY('hora_inicio','desc')->first();
                    
                if(!is_null($obj)){
                    $dat = array("km" => ["Conductor ya tiene registrado Checklist con otro vehiculo (".$obj->vehiculo->placa.") en el mismo horario"],"id"=>$obj->id);
                    return json_encode($dat);
                }
            }
        }

        if(($request->input('k_inicial')+0)== ($request->input('k_final')+0)){
            $dat = array("km" => ["Kilometraje final no puede ser el mismo al inicial."]);
            return json_encode($dat);
        }
        if(($request->input('k_final')+0)>0){
            if(($request->input('k_final')+0)<($request->input('k_inicial')+0)){
                $dat = array("km" => ["Kilometraje final no puede ser el menor al inicial."]);
                return json_encode($dat);   
            }
        }

        if(auth()->user()->tipouser_id!="1"){//todos los usuario menos el admin
            if((($request->input('k_final')+0) - ($request->input('k_inicial')+0)) >500){
                $dat = array("km" => ["Kilometraje final no puede ser mayor a 500KM del kilometraje inicial"]);
                return json_encode($dat);   
            }
        }

        $reglas     = array(
            //'fecha_registro' => 'required|date|after_or_equal:' . $today,
            'fecha_registro' => 'required|date',
            'unidad_placa' => (new RulePlacaExist()),
            'k_inicial' => 'required|numeric|min:0',
            'k_final' => 'required|numeric|min:' . ($min+1),
            'lider_area' => 'required|max:300',
            'conductor_id' => 'exists:App\Conductor,id',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'ua_id_trabajo' => 'required',
        );

        $mensajes = array(
            'fecha_registro.required' => 'Debe seleccionar una fecha',
            'fecha_registro.date' => 'La fecha tiene formato incorrecto',
            'hora_inicio.required' => 'Debe indicar una hora de inicio',
            'hora_fin.required' => 'Debe indicar una hora de fin',
            //'fecha_registro.after_or_equal' => 'La fecha ingresada es incorrecta',
            'unidad_placa.required' => 'Debe ingresar una unidad placa',
            'unidad_placa.regex' => 'La unidad placa ingresada es incorrecta',
            'k_inicial.required' => 'Debe ingresar un kilometraje inicial',
            'k_inicial.min' => 'El kilometraje inicial ingresado es incorrecto',
            'k_final.required' => 'Debe ingresar un kilometraje final',
            'k_final.min' => 'El kilometraje final ingresado es incorrecto',
            'lider_area.required' => 'Debe ingresar un lider de area',
            'lider_area.max' => 'El nombre del lider de area debe tener max. 300 caracteres',
            'conductor_id.exists' => 'Debe ingresar un conductor',
            'hora_fin.after' => 'La hora fin debe ser mayor a la hora de inicio.',
            'ua_id_trabajo.required' => 'Debe registrar una ua de trabajo',
        );

        $hora_inicio = strtotime(date("Y-m-d ").$request->input('hora_inicio'));
        $hora_fin = strtotime(date("Y-m-d ").$request->input('hora_fin'));
        if((($hora_fin - $hora_inicio)/(60))<40){
            $dat = array("hora_fin" => ["Hora fin debe ser mayor a la hora de inicio en 40 min "]);
            return json_encode($dat);
        }
        
        $validacion = Validator::make($request->all(), $reglas, $mensajes);

        if ( $validacion->fails() ) {
            return $validacion->messages()->toJson();
        }

        $uaList = $request->input('uaList');
        $KmInicialList = $request->input('KmInicialList');
        $KmFinalList = $request->input('KmFinalList');
        $HoraInicioList = $request->input('HoraInicioList');
        $HoraFinList = $request->input('HoraFinList');

        if($uaList!=""){
            $kmMax = 0;
            $kmMin = 0;
            $horaMin = 0;
            $horaMax = 0;
            for ($i = 0; $i < count($uaList); $i++) {
                if(($KmInicialList[$i]+0) < ($request->input('k_inicial')+0)){
                    $dat = array("km_inicial" => ["Kilometraje Inicial del detalle no puede ser menor al Kilometraje Inicial del checklist"]);
                    return json_encode($dat);   
                }
                if(($KmFinalList[$i]+0) > ($request->input('k_final')+0) && ($request->input('k_final')+0)>0){
                    $dat = array("km_inicial" => ["Kilometraje Final del detalle no puede ser mayor al Kilometraje Final del checklist"]);
                    return json_encode($dat);   
                }
                if(strtotime("2024-01-01 ".$HoraInicioList[$i]) < strtotime("2024-01-01 ".$request->input('hora_inicio'))){
                    $dat = array("km_inicial" => ["Hora Inicial del detalle no puede ser menor a la Hora Inicial del checklist"]);
                    return json_encode($dat);   
                }
                if(strtotime("2024-01-01 ".$HoraFinList[$i]) > strtotime("2024-01-01 ".$request->input('hora_fin'))){
                    $dat = array("km_final" => ["Hora Final del detalle no puede ser mayor a la Hora Final del checklist"]);
                    return json_encode($dat);   
                }
                if(($KmFinalList[$i]+0)>$kmMax){
                    $kmMax = $KmFinalList[$i]+0;
                }
                if($kmMin==0){
                    $kmMin = $KmInicialList[$i]+0;
                }
                if(($KmInicialList[$i]+0)<$kmMin){
                    $kmMin = $KmInicialList[$i]+0;
                }

                if(strtotime("2024-01-01 ".($HoraFinList[$i]))>$horaMax){
                    $horaMax = strtotime("2024-01-01 ".$HoraFinList[$i]);
                }
                if($horaMin==0){
                    $horaMin = strtotime("2024-01-01 ".$HoraInicioList[$i]);
                }
                if(strtotime("2024-01-01 ".$HoraInicioList[$i])<$horaMin){
                    $kmMin = strtotime("2024-01-01 ".$HoraInicioList[$i]);
                }
            }
            if($kmMin <> ($request->input('k_inicial')+0)){
                $dat = array("km_inicial" => ["Kilometraje Inicial del detalle no coincide al Kilometraje Inicial del checklist"]);
                return json_encode($dat);
            }
            if($kmMax <> ($request->input('k_final')+0) && ($request->input('k_final')+0)!="0"){
                $dat = array("km_inicial" => ["Kilometraje Final del detalle no coincide al Kilometraje Final del checklist"]);
                return json_encode($dat);   
            }
            if($horaMin <> strtotime("2024-01-01 ".$request->input('hora_inicio'))){
                $dat = array("km_inicial" => ["Hora Inicial del detalle no coincide con la Hora Inicial del checklist"]);
                return json_encode($dat);
            }
            if($horaMax <> strtotime("2024-01-01 ".$request->input('hora_fin'))){
                $dat = array("km_inicial" => ["Hora Final del detalle no coincide con la Hora Final del checklist"]);
                return json_encode($dat);   
            }
        }
    
        $error = DB::transaction(function() use($request, $id){
            $checklistvehicular = Checklistvehicular::find($id);
            
            $checklistvehicularOrg = json_encode($checklistvehicular);

            $checklistvehicular->fecha_registro= $request->input('fecha_registro');
            $checklistvehicular->hora_inicio = $request->input('hora_inicio');
            $checklistvehicular->hora_fin = $request->input('hora_fin');

            $placa = $request->input('unidad_placa');

            $equipo = Equipo::where('placa', $placa)->first();
            $checklistvehicular->equipo_id = $equipo!=null ? $equipo->id : null;
            if(!is_null($equipo)){
                $equipo->horometroactual=$request->input('k_final');
                $equipo->save();
            }

            $vehiculo = Vehiculo::where('placa', $placa)->first();
            $checklistvehicular->vehiculo_id = $vehiculo!=null ? $vehiculo->id : null;
            if($vehiculo != null) {
                //ACTUALIZO K. RECORRIDO DEL VEHICULO
                if(($request->input('k_final')+0)>$vehiculo->kilometraje_act){
                    $vehiculo->kilometraje_act = $request->input('k_final');
                    $vehiculo->save();
                }
            }

            $checklistvehicular->k_inicial = $request->input('k_inicial');
            $checklistvehicular->k_final = $request->input('k_final');
            $checklistvehicular->lider_area = mb_strtoupper($request->input('lider_area'), 'utf-8');
            $checklistvehicular->conductor_id= $request->input('conductor_id');
            $checklistvehicular->observaciones = $request->input('observaciones');
            $checklistvehicular->incidentes = $request->input('incidentes');
            
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $checklistvehicular->ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            if($request -> input('ua_id_trabajo')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id_trabajo')) -> get();
                $checklistvehicular->uatrabajo_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }

            if($request->input('sistema_electrico') != null) $checklistvehicular->sistema_electrico = json_decode($request->input('sistema_electrico'));
            if($request->input('sistema_mecanico') != null) $checklistvehicular->sistema_mecanico = json_decode($request->input('sistema_mecanico'));
            if($request->input('accesorios') != null) $checklistvehicular->accesorios = json_decode($request->input('accesorios'));
            if($request->input('documentos') != null) $checklistvehicular->documentos = json_decode($request->input('documentos'));
            
            $checklistvehicular->sincronizado = 'N';
            $checklistvehicular->save();

            $uaList = $request->input('uaList');
            $KmInicialList = $request->input('KmInicialList');
            $KmFinalList = $request->input('KmFinalList');
            $HoraInicioList = $request->input('HoraInicioList');
            $HoraFinList = $request->input('HoraFinList');
            $ids = $request->input('iddetalle');

            if($uaList!=""){
                for ($i = 0; $i < count($uaList); $i++) {
                    if($ids[$i]>=0){
                        $detalle = DetalleChecklistvehicular::find($ids[$i]);
                    }else{
                        $detalle = new DetalleChecklistvehicular();
                    }
                    
                    $detalle -> checklistvehicular_id =$checklistvehicular->id;
                    $uaDB =  Ua::where('codigo', $uaList[$i]) -> get();
                    $detalle -> ua_id  = $uaDB[0]->id;
                    $detalle -> km_inicial  = $KmInicialList[$i];
                    $detalle -> km_final  = $KmFinalList[$i];
                    $detalle -> hora_inicio  = $HoraInicioList[$i];
                    $detalle -> hora_fin  = $HoraFinList[$i];
                    $detalle -> save();
                }
            }


            $idseliminados=$request->idseliminados;

            if($idseliminados!=null){
                foreach($idseliminados as $k=>$idd){
                    $error = DB::transaction(function() use($idd){
                        if($idd>0){
                            $detalle = DetalleChecklistvehicular::find($idd);
                            $detalle->delete();
                        }
                    }); 
                }
            }  

            event( new UserHasEdited($checklistvehicularOrg,$checklistvehicular,'checklistvehicular', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function searchUnidad(Request $request) {
        $filter = $request->filter;
        $concesionaria_id = $this->getConsecionariaActual();

        $equipos = Equipo::join('ua','ua.id','=','equipo.ua_id')
                        ->leftjoin('ua as uatrabajo','uatrabajo.id','=','equipo.uatrabajo_id')
                        ->select('placa', 'equipo.descripcion','ua.codigo as codigoua','ua.descripcion as ua','decimales','equipo.id','equipo.uatrabajo_id','uatrabajo.codigo as codigouatrabajo','uatrabajo.descripcion as uatrabajo')
                        ->where('equipo.concesionaria_id', '=', $concesionaria_id)
                        ->where(function ($query) use ($filter) {
                            $query->where('placa', 'like', '%'.$filter.'%')
                                ->orWhere('equipo.descripcion', 'like', '%'.$filter.'%')
                                ->orWhere('ua.codigo', 'like', '%'.$filter.'%');
                        })->get();

        $vehiculos = Vehiculo::join('ua','ua.id','=','vehiculo.ua_id')
                            ->leftjoin('ua as uatrabajo','uatrabajo.id','=','vehiculo.uatrabajo_id')
                            ->select('placa', 'modelo AS descripcion','ua.codigo as codigoua','ua.descripcion as ua','decimales','vehiculo.id','vehiculo.uatrabajo_id','uatrabajo.codigo as codigouatrabajo','uatrabajo.descripcion as uatrabajo')
                            ->where('vehiculo.concesionaria_id', '=', $concesionaria_id)
                            ->where(function ($query) use ($filter) {
                                $query->where('placa', 'like', '%'.$filter.'%')
                                    ->orWhere('modelo', 'like', '%'.$filter.'%')
                                    ->orWhere('ua.codigo', 'like', '%'.$filter.'%');
                            })->get();
        
        return [
            'equipos' => $equipos,
            'vehiculos' => $vehiculos
        ];
    }

    public function generatePDF(Request $request) {
        if ( $request->checklistvehicular_id == null || !is_numeric($request->checklistvehicular_id) ) return;

        $namefile = 'Check List Vehicular - '.time().'.pdf';  
        
        $checklistvehicular = Checklistvehicular::leftJoin('equipo', 'equipo.id', '=', 'checklistvehicular.equipo_id')
                                                ->leftJoin('vehiculo', 'vehiculo.id', '=', 'checklistvehicular.vehiculo_id')
                                                ->join('conductor', 'conductor.id', 'checklistvehicular.conductor_id')
                                                ->leftJoin('marca AS marca_equipo', 'marca_equipo.id', 'equipo.marca_id')
                                                ->leftJoin('marca AS marca_vehiculo', 'marca_vehiculo.id', 'vehiculo.marca_id')
                                                ->leftJoin('tipocombustible', 'tipocombustible.id', 'vehiculo.tipocombustible_id')
                                                ->leftJoin('contratista AS contratista_vehiculo', 'contratista_vehiculo.id', 'vehiculo.contratista_id')
                                                ->leftJoin('contratista AS contratista_equipo', 'contratista_equipo.id', 'equipo.contratista_id')
                                                ->leftJoin('ua AS ua_vehiculo', 'ua_vehiculo.id', '=', 'vehiculo.ua_id')
                                                ->leftJoin('ua AS ua_equipo', 'ua_equipo.id', 'equipo.ua_id')
                                                ->leftJoin('ua AS ua_principal', 'ua_principal.id', 'checklistvehicular.ua_id')
                                                ->select('checklistvehicular.fecha_registro', 'checklistvehicular.k_inicial', 'checklistvehicular.k_final', 'checklistvehicular.lider_area', 'checklistvehicular.observaciones', 'checklistvehicular.incidentes', 
                                                        'checklistvehicular.sistema_electrico', 'checklistvehicular.sistema_mecanico', 'checklistvehicular.accesorios', 'checklistvehicular.documentos',
                                                        'equipo.placa as equipo_placa', 'equipo.modelo AS equipo_modelo', 'equipo.descripcion as equipo_descripcion',
                                                        'marca_equipo.descripcion AS equipo_marca', 'ua_equipo.codigo AS equipo_ua','checklistvehicular.conductor_id',
                                                        'contratista_equipo.razonsocial AS equipo_contratista', 'contratista_equipo.id AS equipo_contratista_id',
                                                        'vehiculo.placa AS vehiculo_placa', 'vehiculo.modelo AS vehiculo_modelo', 'vehiculo.color AS vehiculo_color', 'tipocombustible.descripcion AS vehiculo_combustible', 'vehiculo.id AS vehiculo_id',
                                                        'marca_vehiculo.descripcion AS vehiculo_marca', 'ua_vehiculo.codigo AS vehiculo_ua','ua_principal.codigo as ua_codigo',
                                                        'contratista_vehiculo.razonsocial AS vehiculo_contratista', 'contratista_vehiculo.id AS vehiculo_contratista_id', 
                                                        'conductor.nombres as conductor_nombres', 'conductor.apellidos as conductor_apellidos', 'conductor.licencia','checklistvehicular.hora_inicio','checklistvehicular.hora_fin')
                                                ->where('checklistvehicular.id', '=', $request->checklistvehicular_id)
                                                ->withTrashed()->first();
        
        if ( $checklistvehicular == NULL ) return redirect('/home');

        $data = [];

        $is_v = !is_null($checklistvehicular->vehiculo_placa) ? true : false;
        
        // datos que puede ser de vehiculo o equipo
        $data['placa'] = $is_v ? $checklistvehicular->vehiculo_placa : $checklistvehicular->equipo_placa;
        $data['marca'] = $is_v ? $checklistvehicular->vehiculo_marca : $checklistvehicular->equipo_marca;
        $data['modelo'] = $is_v ? $checklistvehicular->vehiculo_modelo : $checklistvehicular->equipo_modelo;
        $data['contratista'] = $is_v ? $checklistvehicular->vehiculo_contratista : $checklistvehicular->equipo_contratista;
        $contratista = $is_v ? $checklistvehicular->vehiculo_contratista_id : $checklistvehicular->equipo_contratista_id;
        $contratista = Contratista::join('concesionaria', 'concesionaria.ruc', '=', 'contratista.ruc')
                                ->select('contratista.ruc')->where('contratista.id', '=', $contratista)->first();
        $data['directo'] = is_null($contratista) ? 0 : 1;
        
        $data['ua'] = $is_v ? $checklistvehicular->vehiculo_ua : $checklistvehicular->equipo_ua;
        $data['ua2'] = $checklistvehicular->ua_codigo;
        // dd($checklistvehicular->vehiculo_ua, $checklistvehicular->equipo_ua, $is_v);
        // datos que es de vehiculo
        $data['color'] = $checklistvehicular->vehiculo_color;
        $data['combustible'] = $checklistvehicular->vehiculo_combustible;
        $fecha_soat = Vehiculodocument::select('fecha')
                                            ->where('vehiculo_id', '=', $checklistvehicular->vehiculo_id)
                                            ->where('tipo', '=', 'SOAT')->first()['fecha'];
        $data['fecha_soat'] = is_null($fecha_soat) ? ($is_v ? 'Sin SOAT' : '----') : $fecha_soat;        
        // datos que es de equipo
        $data['descripcion'] = $checklistvehicular->equipo_descripcion;

        // datos del checklistvehicular
        $data['fecha_registro'] = $checklistvehicular->fecha_registro;
        $data['hora_inicio'] = $checklistvehicular->hora_inicio;
        $data['hora_fin'] = $checklistvehicular->hora_fin;
        $data['k_inicial'] = $checklistvehicular->k_inicial;
        $data['k_final'] = $checklistvehicular->k_final;
        $data['lider_area'] = $checklistvehicular->lider_area;
        $data['observaciones'] = strtoupper($checklistvehicular->observaciones);
        $data['incidentes'] = strtoupper($checklistvehicular->incidentes);

        // datos del conductor
        $data['conductor'] = $checklistvehicular->conductor_nombres . ' ' . $checklistvehicular->conductor_apellidos;
        $data['licencia'] = $checklistvehicular->licencia;
        
        $limite = intval((count($checklistvehicular->sistema_electrico) + count($checklistvehicular->sistema_mecanico) + count($checklistvehicular->accesorios) + count($checklistvehicular->documentos) + 5)/2);
        
        $steps = [ 
            [
                'title' => 'SISTEMA ELECTRICO',
                'values' => $checklistvehicular->sistema_electrico
            ], [
                'title' => 'SISTEMA MECANICO',
                'values' => $checklistvehicular->sistema_mecanico
            ], [
                'title' => 'ACCESORIOS',
                'values' => $checklistvehicular->accesorios
            ], [
                'title' => 'DOCUMENTOS',
                'values' => $checklistvehicular->documentos
            ]
        ];

        $lista = [];
        $row = -1;  // para saber en que fila esta
        foreach ($steps as $step) { // para controlar la etapa en la que se encuentra
            $row++; //aumento de uno en uno
            $lista[] = [
                'title' => true,
                'caption' => $step['title'],
                'value_yes' => 'SI',
                'value_not' => 'NO'
            ];
            foreach ($step['values'] as $param) {
                $row++;
                if ( $row == ($limite) ) {
                    $lista[] = [
                        'title' => true,
                        'caption' => $step['title'],
                        'value_yes' => 'SI',
                        'value_not' => 'NO'
                    ];
                }
                $lista[] = [
                    'title' => false,
                    'caption' => mb_strtoupper($param['titulo'], 'UTF-8'),
                    'value_yes' => ( !is_null($param['estado']) && $param['estado'] )? 'X': '',
                    'value_not' => ( !is_null($param['estado']) && !$param['estado'] )? 'X': ''
                ];
                
            }
        }
        
        $data['lista'] = $lista;
        $data['limite'] = $limite;
        $data['namefile'] = $namefile;
        $firma = Conductor::leftJoin('conductordocument as cd','cd.conductor_id','=','conductor.id')->where('conductor.id','=',$checklistvehicular->conductor_id)->where('cd.tipo','like','imagen-firma')->whereNull('cd.deleted_at')->select('cd.archivo as firma')->first();
        if(!is_null($firma)){
            $data['firma'] = $firma->firma;
        }else{
            $data['firma'] = '';
        }
        $data['concesionaria_id'] = Concesionaria::getConcesionariaActual()->id;
        //dd($data);
        $html = view('app.mantcorrprev.pdf.template_individual', $data)->render();

        $mpdf = new Mpdf();
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        $mpdf->Output($namefile, "I");
    }

    // INICIO DE MODIFICACION
    public function searchConductor(Request $request){
        $filter = $request->filter;
        $concesionaria_id = Concesionaria::getConcesionariaActual()->id;

        $list = Conductor:://rightJoin('conductorconcesionaria AS cc', 'cc.conductor_id', '=', 'conductor.id')
                        leftJoin('lider','lider.id','=','conductor.lider_id')
                        ->select('conductor.id', 'dni', DB::raw("CONCAT(conductor.nombres, ' ', apellidos) AS nombres"),DB::raw('(case when lider_id>0 then lider.nombres else \'-\' end) as lider'))
                        ->where(function ($subquery) use ($filter) {
                            $subquery->where('dni', 'like', '%'.$filter.'%')
                                    ->orWhere(DB::raw("CONCAT(conductor.nombres, ' ', apellidos)"), 'like', '%'.$filter.'%');
                        })
                        //->where('tipo','like','Conductor')
                        //->where('cc.concesionaria_id', '=', $concesionaria_id)
                        ->orderBy('dni', 'desc')->get();

        return [
            'list' => $list
        ];
    }
    // FIN DE MODIFICACION

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) return $existe;
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;

        $mensaje=true;
        $modelo   = Checklistvehicular::find($id);
        $entidad  = 'Checklistvehicular';
        $formData = array('route' => array('mantcorrprev.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) return $existe;
        $error = DB::transaction(function() use($id){
            $checklistvehicular = Checklistvehicular::find($id);
            
            //ACTUALIZO K. RECORRIDO DEL VEHICULO
            $vehiculo = $checklistvehicular->vehiculo;
            if(!is_null($vehiculo)){
                $vehiculo->kilometraje_rec -=  ($checklistvehicular->k_final - $vehiculo->kilometraje_act);
                $vehiculo->save();
            }
            $checklistvehicular->sincronizado = 'N';
            event( new UserHasCreatedOrDeleted($checklistvehicular->id,'checklistvehicular', auth()->user()->id,'eliminar'));
            $checklistvehicular->delete();

        });
        return is_null($error) ? "OK" : $error;
    }
    
    public function recorridoExcel(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicial;
        $fecha2 = $request->fechafinal;
        
        return Excel::download(new RecorridoVehiculoExport($fecha1,$fecha2,$concesionaria), 'ReporteRecorridoVehiculo.xlsx');
    }

    public function recorridoResumenexcel(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicial;
        $fecha2 = $request->fechafinal;
        
        return Excel::download(new RecorridoResumenVehiculoExport($fecha1,$fecha2,$concesionaria), 'ReporteSeguimientoKmVehiculo.xlsx');
    }
    
    public function recorridoResumenMesexcel(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicial;
        $fecha2 = $request->fechafinal;
        
        return Excel::download(new RecorridoResumenMesVehiculoExport($fecha1,$fecha2,$concesionaria), 'ReporteSeguimientoMesKmVehiculo.xlsx');
    }

    public function recorridoResumenDetalladoexcel(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicial;
        $fecha2 = $request->fechafinal;
        
        return Excel::download(new RecorridoResumenDetalladoVehiculoExport($fecha1,$fecha2,$concesionaria), 'ReporteSeguimientoKmDetalladoVehiculo.xlsx');
    }
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
    
    public function exportExcel(Request $request){

        $fecha_registro_inicial = $request->input('fecha_registro_inicial');
        $fecha_registro_final   = $request->input('fecha_registro_final');
        $ua = $request->input('ua');
        $modelo = $request->input('modelo');
        $conductor = $request->input('conductor');
        $lider = $request->input('lider');
        $accesorio = $request->input('accesorio');
        return Excel::download(new CheckListExport($fecha_registro_inicial,$fecha_registro_final,is_null($ua)?'':$ua,is_null($modelo)?'':$modelo,is_null($conductor)?'':$conductor,is_null($lider)?'':$lider), 'checkList.xlsx');
    }

    public function exportExcelRevision(Request $request){

        $fecha_registro_inicial = $request->input('fecha_registro_inicial');
        $fecha_registro_final   = $request->input('fecha_registro_final');
        $ua = $request->input('ua');
        $modelo = $request->input('modelo');
        $conductor = $request->input('conductor');
        $lider = $request->input('lider');
        $accesorio = $request->input('accesorio');
        return Excel::download(new CheckListRevisionExport($fecha_registro_inicial,$fecha_registro_final,is_null($ua)?'':$ua,is_null($modelo)?'':$modelo,is_null($conductor)?'':$conductor,is_null($lider)?'':$lider,is_null($accesorio)?'':$accesorio), 'checkListRevision.xlsx');
    }

    public function exportExcelNoRegistrado(Request $request){

        $fecha_registro_inicial = $request->input('fecha_registro_inicial');
        $fecha_registro_final   = $request->input('fecha_registro_final');
        return Excel::download(new CheckListNoRegistradoExport($fecha_registro_inicial,$fecha_registro_final), 'checkListNoRegistrado.xlsx');
    }

    public function justificar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = $listarLuego;
        $checklistvehicular = Checklistvehicular::find($id);

        $entidad  = 'Checklistvehicular';
        $formData = array('mantcorrprev.guardarJustificar', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        $user = auth()->user();

        return view($this->folderview.'.mantJustificar')
            ->with(compact('checklistvehicular', 'formData', 'entidad', 'boton', 'listar','user'));
    }

    public function guardarJustificar($id,Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'checklistvehicular');
        if ($existe !== true) {
            return $existe;
        }
    
        $error = DB::transaction(function() use($request, $id){
            $checklistvehicular = Checklistvehicular::find($id);
            
            $checklistvehicular->justificacion = $request->input('justificacion');
            $checklistvehicular->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}