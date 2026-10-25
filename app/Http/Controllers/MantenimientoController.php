<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Checklistvehicular;
use App\Unidad;
use App\Equipo;
use App\Vehiculo;
use App\Conductor;
use App\Ua;
use App\Configuracion;
use App\Concesionaria;
use App\RegManVeh;
use App\RegRepVeh;
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
use App\Exports\CheckListExport;
use App\Curl;

// INICIO DE MODIFICACION
use App\Rules\RulePlacaExist;


class MantenimientoController extends Controller
{
    protected $folderview      = 'app.mantenimiento';
    protected $tituloAdmin     = 'Mant vs Checklist vehicular';
    protected $tituloCheckListVehicular = 'Nuevo Checklist Vehicular';
    protected $tituloRegistrar = 'Registro de Checklist Vehicular';
    protected $tituloModificar = 'Modificar Mant. vs Checklist Vehicular';
    protected $tituloEliminar  = 'Eliminar Checklist Vehicular';
    protected $rutas           = array(
        // 'createchecklistvehicular' => 'mantcorrprev.createchecklistvehicular', 
        'create' => 'mantcorrprev.create',
        // 'createrepuesto' => 'mantcorrprev.createrepuesto',
        'buscarporua' => 'mantcorrprev.buscarporua',
        'edit'   => 'mantenimiento.edit', 
        'delete' => 'mantcorrprev.eliminar',
        'activar' => 'mantcorrprev.activar',
        'search' => 'mantenimiento.buscar',
        'store' => 'mantcorrprev.store',
        'index'  => 'mantenimiento.index',
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
        $entidad          = 'Mantenimiento';
        
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        $fechainicio= $request->input('fecha_registro_inicial');
        $fechafin= $request->input('fecha_registro_final');
        $filter = $request->input('modelo');

        $resultado= RegManVeh::leftjoin('vehiculo','vehiculo.id','=','regmanveh.ua_id')
                            ->leftjoin('equipo','equipo.id','=','regmanveh.equipo_id')
                            ->leftJoin('checklistvehicular','checklistvehicular.id','=','regmanveh.checklistvehicular_id')
        ->where('regmanveh.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin) {
                if ( $fechainicio !== null ) $q->where('regmanveh.fechaentrada','>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regmanveh.fechaentrada','<=', $fechafin);
            })
        ->where(function($q) use ($filter){
        $q->where('regmanveh.cliente', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.ordencompra', 'like', '%'.$filter.'%')
          ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%');
        })
        ->select('regmanveh.id',DB::raw('\'Mantenimiento\' as tipo'),'regmanveh.fechaentrada','vehiculo.placa as vehiculo_placa','regmanveh.checklistvehicular_id','checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.fecha_registro as fechachecklist','equipo.modelo as equipo_descripcion','vehiculo.modelo as vehiculo_modelo','equipo.placa as equipo_placa','regmanveh.ordencompra');

        $resultado2= RegRepVeh::leftjoin('vehiculo','vehiculo.id','=','regrepveh.ua_id')
                            ->leftjoin('equipo','equipo.id','=','regrepveh.equipo_id')
                            ->leftJoin('checklistvehicular','checklistvehicular.id','=','regrepveh.checklistvehicular_id')
        ->where('regrepveh.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin) {
                if ( $fechainicio !== null ) $q->where('regrepveh.fechasalida','>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regrepveh.fechasalida','<=', $fechafin);
            })
        ->where(function($q) use ($filter){
        $q->where('regrepveh.cliente', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.ordencompra', 'like', '%'.$filter.'%')
          ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%');
        })
        ->select('regrepveh.id',DB::raw('\'Repuesto\' as tipo'),'regrepveh.fechasalida as fechaentrada','vehiculo.placa as vehiculo_placa','regrepveh.checklistvehicular_id','checklistvehicular.observaciones','checklistvehicular.incidentes','checklistvehicular.fecha_registro as fechachecklist','equipo.modelo as equipo_descripcion','vehiculo.modelo as vehiculo_modelo','equipo.placa as equipo_placa','regrepveh.ordencompra');

        $lista            = $resultado->union($resultado2)->orderBy('fechaentrada','desc')->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'F. Registro Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo / Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden Compra', 'numero' => '1');
        $cabecera[]       = array('valor' => 'F. Registro Checklist', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Observaciones', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Incidentes', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        }

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_registrar = $this->tituloRegistrar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_registrar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Mantenimiento';
        $title            = $this->tituloAdmin;
        $tituloCheckListVehicular = $this->tituloCheckListVehicular;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = auth()->user();
        
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'tituloCheckListVehicular','tituloRegistrar', 'ruta','user'));
    }

    public function create(Request $request) {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Mantenimiento';
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
        return view($this->folderview.'.mant_checklistvehicular')->with(compact('checklistvehicular', 'formData', 'entidad', 'unidad_placa', 'unidad_descripcion', 'conductor', 'boton', 'listar','user'));
    }

    public function store(Request $request) {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');

        $today = \Carbon\Carbon::now('America/Lima')->toDateString();
        $min = $request->all()['k_inicial'];

        //BUSCAR EQUIPO/VEHICULO KM
        $placa = $request->input('unidad_placa');
        $equipo = Equipo::where('placa', $placa)->first();
        if($equipo != null) {
            $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                        ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                        ->orderBy('fecha_registro','desc')->first();
            if(!is_null($obj) && ($request->input('k_inicial')+0)< ($obj->k_final+0)){
                $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es:  ".round($obj->k_final,0)." del ".$obj->fecha_registro],"id"=>$obj->id);
                return json_encode($dat);
            }
            $obj = Checklistvehicular::where('equipo_id','=',$equipo->id)
                        ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                        ->orderBy('fecha_registro','desc')->first();
            if(!is_null($obj)){
                $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
                return json_encode($dat);
            }
        }else {
            $vehiculo = Vehiculo::where('placa', $placa)->first();
            $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                    ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_inicio').'\')'))
                    ->orderBy('fecha_registro','desc')->first();
            if(!is_null($obj) && ($request->input('k_inicial')+0)< ($obj->k_final+0)){
                $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es: ".round($obj->k_final,0)." del ".$obj->fecha_registro],"id"=>$obj->id);
                return json_encode($dat);
            }
            $obj = Checklistvehicular::where('vehiculo_id','=',$vehiculo->id)
                        ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$request->input('fecha_registro')." ".$request->input('hora_fin').'\')'))
                        ->orderBy('fecha_registro','desc')->first();
            
            if(!is_null($obj)){
                $dat = array("km" => ["Checklist coincide con fecha y hora"],"id"=>$obj->id);
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
        );
        
        $validacion = Validator::make($request->all(), $reglas, $mensajes);

        if ( $validacion->fails() ) {
            return $validacion->messages()->toJson();
        }
        $id=0;
        $error = DB::transaction(function() use($request,&$id){
            
            $checklistvehicular = new Checklistvehicular();
            $checklistvehicular->fecha_registro= $request->input('fecha_registro');

            $placa = $request->input('unidad_placa');
            $equipo = Equipo::where('placa', $placa)->first();
            if($equipo != null) {
                $checklistvehicular->equipo_id= $equipo->id;
            }else {
                $vehiculo = Vehiculo::where('placa', $placa)->first();
                $checklistvehicular->vehiculo_id= $vehiculo->id;

                //ACTUALIZO K. RECORRIDO DEL VEHICULO
                if(($request->input('k_final')+0)>$vehiculo->kilometraje_act){
                    $vehiculo->kilometraje_rec =  $request->input('k_final') - $vehiculo->kilometraje_act;
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
            $checklistvehicular->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $checklistvehicular->save();

            $id = $checklistvehicular->id;
            event( new UserHasCreatedOrDeleted($checklistvehicular->id,'checklistvehicular', auth()->user()->id,'crear'));

        });
        if(str_contains($request->input('sistema_electrico'),'"estado":false') || str_contains($request->input('sistema_mecanico'),'"estado":false')  || str_contains($request->input('accesorios'),'"estado":false') || str_contains($request->input('documentos'),'"estado":false') || trim($request->input('observaciones'))!="" && trim($request->input('incidentes'))!=""){
            $cc = new Curl(false,'https://www.h2olmos.pe/h2soft/PHPMailer/notificacionChecklist.php');
            $url = 'https://www.h2olmos.pe/h2soft/PHPMailer/notificacionChecklist.php?id='.$id;
            $Page = $cc->get($url,array());
            $datos = json_decode($Page);
        }
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        

        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();

        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        if($request->input('tipo')=="Repuesto"){
            $obj = RegRepVeh::find($id);
        }else{
            $obj = RegManVeh::find($id);
        }
        $tipo = $request->input('tipo');
        $oua = Vehiculo::where('id','=',$obj->ua_id)->get();
        $placa= $oua[0]->placa;
        $oua2 = Ua::where('id','=',$oua[0]->ua_id)->get();
        $uades= $oua2[0]->codigo;
        $uades=$uades;
        $vehiculo_id=$obj->ua_id;
        $equipo_id=$obj->equipo_id;
        $entidad  = 'RegRepVeh';
        $formData = array('mantenimiento.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('obj','oConcesionaria','idconc','placa','uades','vehiculo_id', 'formData', 'entidad', 'boton', 'listar','equipo_id','tipo'));
    }

    public function update(Request $request, $id)
    {
    
        $error = DB::transaction(function() use($request, $id){
            if($request->input('tipo')=="Repuesto"){
                $obj = RegRepVeh::find($id);
            }else{
                $obj = RegManVeh::find($id);
            }
            $obj->checklistvehicular_id = $request->input('checklistvehicular_id');
            $obj->save();

        });
        return is_null($error) ? "OK" : $error;
    }

    public function searchUnidad(Request $request) {
        $filter = $request->filter;
        $concesionaria_id = $this->getConsecionariaActual();

        $equipos = Equipo::join('ua','ua.id','=','equipo.ua_id')
                        ->select('placa', 'equipo.descripcion','ua.codigo as codigoua','ua.descripcion as ua')
                        ->where('equipo.concesionaria_id', '=', $concesionaria_id)
                        ->where(function ($query) use ($filter) {
                            $query->where('placa', 'like', '%'.$filter.'%')
                                ->orWhere('equipo.descripcion', 'like', '%'.$filter.'%');
                        })->withTrashed()->get();

        $vehiculos = Vehiculo::join('ua','ua.id','=','vehiculo.ua_id')
                            ->select('placa', 'modelo AS descripcion','ua.codigo as codigoua','ua.descripcion as ua')
                            ->where('vehiculo.concesionaria_id', '=', $concesionaria_id)
                            ->where(function ($query) use ($filter) {
                                $query->where('placa', 'like', '%'.$filter.'%')
                                    ->orWhere('modelo', 'like', '%'.$filter.'%');
                            })->withTrashed()->get();
        
        return [
            'equipos' => $equipos,
            'vehiculos' => $vehiculos
        ];
    }


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
        return Excel::download(new CheckListExport($fecha_registro_inicial,$fecha_registro_final,is_null($ua)?'':$ua,is_null($modelo)?'':$modelo,is_null($conductor)?'':$conductor,is_null($lider)?'':$lider), 'checkList.xlsx');
    }
}