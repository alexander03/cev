<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Garita;
use App\Control;
use App\Turno;
use App\Lugar;
use App\Agente;
use App\Detallecontrol;
use App\Concesionaria;
use App\Equipo;
use App\Empresa;
use App\Vehiculo;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use App\Curl;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ControlExport;

class ControlController extends Controller
{
    protected $folderview      = 'app.control';
    protected $tituloAdmin     = 'Control Ingresos / Salidas';
    protected $tituloRegistrar = 'Registrar Control';
    protected $tituloModificar = 'Modificar Control';
    protected $tituloEliminar  = 'Eliminar Control';
    protected $rutas           = array('create' => 'control.create', 
            'edit'   => 'control.edit', 
            'delete' => 'control.eliminar',
            'search' => 'control.buscar',
            'index'  => 'control.index',
        );

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Control';
        $fechafin         = $request->input('fechafin');
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $garita           = $request->input('garita');
        $turno            = $request->input('turno');
        $tipo             = $request->input('tipo');
        $color            = $request->input('color');
        $id               = $request->input('numero');
        $agente_id        = $request->input('agente_id');
        $placa            = $request->input('placa');
        $personal         = $request->input('personal');
        $conductor        = $request->input('conductor');
        $destino          = $request->input('destino');
        $empresa          = $request->input('empresa');
        $resultado        = Control::where('concesionaria_id','=',$this->getConsecionariaActual())->orderBy('fecha','desc');
        if($fechainicio!=""){
            $resultado = $resultado->where('fecha','>=',DB::raw("'".$fechainicio." 00:00:00'"));
        }
        if($fechafin!=""){
            $resultado = $resultado->where('fecha','<=',DB::raw("'".$fechafin." 23:59:59'"));
        }
        if($garita!="" && $garita!="0"){
            $resultado = $resultado->where('garita_id','=',$garita);
        }
        if($tipo!=""){
            $resultado = $resultado->where('tipo','like',$tipo);
        }
        if($turno!=""){
            $resultado = $resultado->where('turno_id','=',$turno);
        }
        if($id!=""){
            $resultado = $resultado->where('id','=',$id);
        }
        if($agente_id!=""){
            $resultado = $resultado->where('agente_id','=',$agente_id);
        }
        if($destino!=""){
            $resultado = $resultado->where('destino','like','%'.$destino.'%');
        }
        if($color!=""){
            $resultado = $resultado->where(function ($sql) {
                $sql = $sql->where('origen_id','=',0)
                        ->orWhere('destino_id','=',0)
                        ->orWhere(function ($sql2){
                            $sql2 = $sql2->whereRaw('id in (select control_id from detallecontrol where persona_id=0 and personawp_id=0 and deleted_at is null)');
                        });
            });
        }
        if($placa!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where vehiculo like \'%'.$placa.'%\' and deleted_at is null)');
        }
        if($personal!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where persona like \'%'.$personal.'%\' and deleted_at is null and conductor like \'N\')');
        }
        if($conductor!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where persona like \'%'.$conductor.'%\' and deleted_at is null and conductor like \'S\')');
        }
        if($empresa!=""){
            $resultado = $resultado->whereRaw('id in (select control_id from detallecontrol where empresa like \'%'.$empresa.'%\' and deleted_at is null)');
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Número', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Garita', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Turno', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Agente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Vehiculo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'KM', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Conductor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Pasajeros', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Observ.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fec. Reg.', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        }
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Control';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboGarita =array(''=>'Todos') +  Garita::where('concesionaria_id',$this->getConsecionariaActual())->where('mostrar','like','SI')->orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        $cboTipo = array(''=>'Todos','Entrada'=>'Entrada','Salida'=>'Salida');
        $cboTurno = array(''=>'Todos') + Turno::pluck('nombre','id')->toArray();
        $cboAgente = array(''=>'Todos') + Agente::where('concesionaria_id',$this->getConsecionariaActual())->orderBy('apellidos','asc')->pluck('nombres','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboGarita','cboTurno','cboTipo','cboAgente'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Control';
        $control = null;
        $formData = array('control.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboGarita =array(''=>'Seleccione') +  Garita::where('concesionaria_id',$this->getConsecionariaActual())->where('mostrar','like','SI')->orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        $cboTipo = array(''=>'Seleccione','Entrada'=>'Entrada','Salida'=>'Salida');
        $cboTurno = array(''=>'Seleccione') + Turno::pluck('nombre','id')->toArray();
        if(auth()->user()->tipouser_id==16){//AGENTE
            $cboAgente = array();
            $lista = Agente::where('user_id','=',auth()->user()->id)->where('concesionaria_id',$this->getConsecionariaActual())->orderBy('apellidos','asc')->get();
            $dat = json_decode($lista[0]->garita_id);
            $cboGarita =array(''=>'Seleccione') +  Garita::where('concesionaria_id',$this->getConsecionariaActual())->whereIn('id',$dat)->where('mostrar','like','SI')->orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        }else{
            $cboAgente = array(''=>'Seleccione');
            $lista = Agente::where('concesionaria_id',$this->getConsecionariaActual())->orderBy('apellidos','asc')->get();
        }
        foreach ($lista as $key => $value) {
            $cboAgente += array($value->id => $value->apellidos." ".$value->nombres);
        }
        $detalle = array();
        return view($this->folderview.'.mant')->with(compact('control', 'formData', 'entidad', 'boton', 'listar','cboTipo','cboTurno','cboGarita','cboAgente','detalle'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('fecha' => 'required',
                        'hora' => 'required',
                        'tipo' => 'required',
                        'turno_id' => 'required',
                        'garita_id' => 'required',
                        'agente_id' => 'required',
                        'origen' => 'required',
                        'destino' => 'required',
                        'cuantasdescripciones'=>'required',
                        );
        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            'hora.required' => 'Debe ingresar una hora',
            'destino.required' => 'Debe ingresar un destino',
            'origen.required' => 'Debe ingresar un origen',
            'tipo.required' => 'Debe seleccionar un tipo',
            'turno_id.required' => 'Debe seleccionar un turno',
            'garita_id.required' => 'Debe seleccionar una garita',
            'agente_id.required' => 'Debe seleccionar un agente',
            'cuantasdescripciones.required' => 'Ingrese al menos una persona',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        if($request->input('origen_id')==$request->input('destino_id')){
            return json_encode(['origen' => ['Origen y Destino no deben ser iguales']]);
        }
        $kminicial = $request->input('kmList')[0];
        $vehiculoinicial = $request->input('vehiculoId')[0];
        if($kminicial!="" && $vehiculoinicial!="" && trim($vehiculoinicial)!="0"){
            $detalleInicial = Detallecontrol::join('control','control.id','=','detallecontrol.control_id')
                                ->where('detallecontrol.vehiculo_id','=',$vehiculoinicial)
                                ->where('detallecontrol.km','<',$kminicial)
                                ->where('control.fecha','<',$request->input('fecha')." ".$request->input('hora'))
                                ->first();
            if(!is_null($detalleInicial)){
                //quitado temporalmente return json_encode(['fecha' => ['KM no puede ser menor a '.$detalleInicial->km]]);
            }
        }
        /*if($request->input('cboCaminando')=="S" && $request->input('cuantasdescripciones')==""){
            return json_encode(['persona' => ['Debe registrar pasajeros']]);
        }
        if($request->input('cboCaminando')=="S" && trim($request->input('conductor'))!=""){
            return json_encode(['conductor' => ['No debe tener un conductor registrado']]);
        }
        if($request->input('cboCaminando')=="N" && trim($request->input('conductor'))==""){
            return json_encode(['conductor' => ['Debe registrar un conductor']]);
        }*/
        $id=0;
        $error = DB::transaction(function() use($request,&$id){
            $obj = new Control();
            $obj->fecha= $request->input('fecha')." ".$request->input('hora');
            $obj->tipo= $request->input('tipo');
            $obj->turno_id= $request->input('turno_id');
            $obj->garita_id= $request->input('garita_id');
            $obj->agente_id= $request->input('agente_id');
            $obj->destino_id= $request->input('destino_id')+0;
            $obj->destino= $request->input('destino');
            $obj->origen_id= $request->input('origen_id')+0;
            $obj->origen= $request->input('origen');
            $obj->caminando= $request->input('cboCaminando');
            $obj->observacion= mb_strtoupper($request->input('observacion'), 'utf-8');
            $obj->conductor= $request->input('conductor');
            if(trim($obj->conductor)==""){
                $obj->conductor_id = 0;
                $obj->personaconductor_id = 0;
            }else{
                $obj->conductor_id= $request->input('conductor_id')+0;
                $obj->personaconductor_id= $request->input('personaconductor_id')+0;
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->save();

            $persona = $request->input('personaList');
            $dni = $request->input('dniList');
            $vehiculo = $request->input('vehiculoList');
            $km = $request->input('kmList');
            $conductorList = $request->input('conductorList');
            $personaId = $request->input('personaId');
            $personaWPId = $request->input('personaWPId');
            $contratistaId = $request->input('contratistaId');
            $contratistaList = $request->input('contratistaList');
            $vehiculoId = $request->input('vehiculoId');
            $equipoId = $request->input('equipoId');

            if($persona!=""){
                for ($i = 0; $i < count($persona); $i++) {
                    $detalle = new Detallecontrol();
                    $detalle -> control_id =$obj->id;
                    $detalle -> persona  = $persona[$i];
                    $detalle -> dni  = $dni[$i];
                    $detalle -> conductor  = $conductorList[$i];
                    $detalle -> persona_id  = $personaId[$i];
                    $detalle -> personawp_id  = $personaWPId[$i];
                    $detalle -> contratista_id  = $contratistaId[$i];
                    $detalle -> empresa  = $contratistaList[$i];
                    $detalle -> vehiculo  = $vehiculo[$i];
                    $detalle -> vehiculo_id  = $vehiculoId[$i];
                    $detalle -> equipo_id  = $equipoId[$i];
                    $detalle -> km  = $km[$i];
                    $detalle -> observacion = "";
                    $detalle -> save();
                }
            }
            $id = $obj->id;
            event( new UserHasCreatedOrDeleted($obj->id,'control', auth()->user()->id,'crear'));
            
        });
        
        /*$cc = new Curl(false,'https://h2olmos.pe/cev/PHPMailer/notificacionControl.php');
        $url = 'https://h2olmos.pe/cev/PHPMailer/notificacionControl.php?id='.$id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);*/

        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'control');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $control = Control::find($id);
        $entidad  = 'Control';
        $formData = array('control.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboGarita =array(''=>'Seleccione') +  Garita::where('concesionaria_id',$this->getConsecionariaActual())->where('mostrar','like','SI')->orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        $cboTipo = array(''=>'Seleccione','Entrada'=>'Entrada','Salida'=>'Salida');
        $cboTurno = array(''=>'Seleccione') + Turno::pluck('nombre','id')->toArray();
        if(auth()->user()->tipouser_id==16){//AGENTE
            $cboAgente = array();
            $lista = Agente::where('user_id','=',auth()->user()->id)->where('concesionaria_id',$this->getConsecionariaActual())->orderBy('apellidos','asc')->get();
        }else{
            $cboAgente = array(''=>'Seleccione');
            $lista = Agente::where('concesionaria_id',$this->getConsecionariaActual())->orderBy('apellidos','asc')->get();
        }
        foreach ($lista as $key => $value) {
            $cboAgente += array($value->id => $value->apellidos." ".$value->nombres);
        }
        $detalle = Detallecontrol::where('control_id','=',$id)->get();
        return view($this->folderview.'.mant')->with(compact('control', 'formData', 'entidad', 'boton', 'listar','cboGarita','cboTipo','cboAgente','cboTurno','detalle'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'control');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas     = array('fecha' => 'required',
                        'hora' => 'required',
                        'tipo' => 'required',
                        'turno_id' => 'required',
                        'garita_id' => 'required',
                        'agente_id' => 'required',
                        'origen' => 'required',
                        'destino' => 'required',
                        'cuantasdescripciones'=>'required',
                        );
        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            'hora.required' => 'Debe ingresar una hora',
            'tipo.required' => 'Debe seleccionar un tipo',
            'turno_id.required' => 'Debe seleccionar un turno',
            'garita_id.required' => 'Debe seleccionar una garita',
            'agente_id.required' => 'Debe seleccionar un agente',
            'destino.required' => 'Debe ingresar un destino',
            'origen.required' => 'Debe ingresar un origen',
            'cuantasdescripciones.required' => 'Ingrese al menos una persona',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        if($request->input('origen_id')==$request->input('destino_id')){
            return json_encode(['origen' => ['Origen y Destino no deben ser iguales']]);
        }
        /*if($request->input('cboCaminando')=="S" && $request->input('cuantasdescripciones')==""){
            return json_encode(['persona' => ['Debe registrar pasajeros']]);
        }
        if($request->input('cboCaminando')=="S" && trim($request->input('conductor'))!=""){
            return json_encode(['conductor' => ['No debe tener un conductor registrado']]);
        }
        if($request->input('cboCaminando')=="N" && trim($request->input('conductor'))==""){
            return json_encode(['conductor' => ['Debe registrar un conductor']]);
        }*/
        /*$garita = Lugar::where('id',$request->input('destino_id'))->where('garita_id','like','%"'.$request->input('garita_id').'"%')->first();
        if(!is_null($garita)){
            return json_encode(['origen' => ['Destino no puede ser '.$garita->nombre]]);
        }*/

        $error = DB::transaction(function() use($request, $id){
            $obj = Control::find($id);
            $objCop = json_encode($obj);

            $obj->fecha= $request->input('fecha')." ".$request->input('hora');
            $obj->tipo= $request->input('tipo');
            $obj->turno_id= $request->input('turno_id');
            $obj->garita_id= $request->input('garita_id');
            $obj->agente_id= $request->input('agente_id');
            $obj->destino_id= $request->input('destino_id')+0;
            $obj->destino= $request->input('destino');
            $obj->origen_id= $request->input('origen_id')+0;
            $obj->origen= $request->input('origen');
            $obj->caminando= $request->input('cboCaminando');
            $obj->observacion= mb_strtoupper($request->input('observacion'), 'utf-8');
            $obj->conductor= $request->input('conductor');
            if(trim($obj->conductor)==""){
                $obj->conductor_id = 0;
                $obj->personaconductor_id = 0;
            }else{
                $obj->conductor_id= $request->input('conductor_id')+0;
                $obj->personaconductor_id= $request->input('personaconductor_id')+0;
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->sincronizado = 'N';
            $obj->save();

            $persona = $request->input('personaList');
            $dni = $request->input('dniList');
            $vehiculo = $request->input('vehiculoList');
            $km = $request->input('kmList');
            $conductorList = $request->input('conductorList');
            $personaId = $request->input('personaId');
            $personaWPId = $request->input('personaWPId');
            $contratistaId = $request->input('contratistaId');
            $contratistaList = $request->input('contratistaList');
            $vehiculoId = $request->input('vehiculoId');
            $equipoId = $request->input('equipoId');
            $ids = $request->input('iddetalle');

            if($persona!=""){
                for ($i = 0; $i < count($persona); $i++) {
                    if($ids[$i]>=0){
                        $detalle = Detallecontrol::find($ids[$i]);
                    }else{
                        $detalle = new Detallecontrol();
                    }
                    $detalle -> control_id =$obj->id;
                    $detalle -> persona  = $persona[$i];
                    $detalle -> dni  = $dni[$i];
                    $detalle -> conductor  = $conductorList[$i];
                    $detalle -> persona_id  = $personaId[$i];
                    $detalle -> personawp_id  = $personaWPId[$i];
                    $detalle -> contratista_id  = $contratistaId[$i];
                    $detalle -> empresa  = $contratistaList[$i];
                    $detalle -> vehiculo  = $vehiculo[$i];
                    $detalle -> vehiculo_id  = $vehiculoId[$i];
                    $detalle -> equipo_id  = $equipoId[$i];
                    $detalle -> km  = $km[$i];
                    $detalle -> observacion = "";
                    $detalle -> save();
                }
            }

            $idseliminados=$request->idseliminados;

            if($idseliminados!=null){
                foreach($idseliminados as $k=>$idd){
                    $error = DB::transaction(function() use($idd){
                        if($idd>0){
                            $detalle = Detallecontrol::find($idd);
                            $detalle->delete();
                        }
                    }); 
                }
            }   


            event(new UserHasEdited($objCop,$obj,'control',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'control');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Control::find($id);
        $entidad  = 'Control';
        $formData = array('route' => array('control.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'control');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Control::find($id);
            $obj->sincronizado = 'N';
            event( new UserHasCreatedOrDeleted($obj->id,'control', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function searchAutocompleteContratista($query){
    
        $consulta = "select concat(ruc,' ',razonsocial) as descripcion,0 as empresa_id,id as contratista_id,razonsocial from contratista where
            deleted_at IS NULL AND
            (ruc LIKE '%{$query}%' or razonsocial like '%".strtoupper($query)."%') 
             union all
            select concat(ruc,' ',razonsocial) as descripcion,id as empresa_id,0 as contratista_id,razonsocial from empresa
            where deleted_at is NULL and 
            (ruc LIKE '%{$query}%' or razonsocial like '%".strtoupper($query)."%') ";
        //echo $consulta;
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    public function searchAutocompletePersona($query){
    
        $consulta = "select id, concat(dni,otros,' - ',nombres,' ',apellidos) as descripcion,concat(nombres,' ',apellidos) as persona,contratista,contratista_id,dni,otros from persona where
            deleted_at IS NULL AND
            (dni LIKE '%{$query}%' or concat(nombres,' ',apellidos) like '%".strtoupper($query)."%')";
        $res = DB::select($consulta);
        $data = array();
        $listaData = array();
        foreach($res as $k=>$v){
            if(!isset($listaData[$v->id."-0"])){
                $data[] = array("persona_id"=>$v->id,
                        "personawp_id"=>0,
                        "descripcion"=>$v->descripcion,
                        "persona"=>$v->persona,
                        "contratista"=>$v->contratista,
                        "contratista_id"=>$v->contratista_id,
                        "dni"=>$v->dni."".$v->otros,
                    );
                $listaData[$v->id."-0"] = $v->id."-0";
            }
        }

        $lista = DB::connection('mysql2')->select("call SP_CARGO_LISTAR(?,?,?,?,?,?,?)",['3',0,0,'','','','0']);
        foreach($lista as $k=>$v){
            if(!isset($listaData["0-".$v->codpersonal])){
                $data[] = array("persona_id"=>0,
                        "personawp_id"=>$v->codpersonal,
                        "descripcion"=>$v->docid." - ".$v->personal,
                        "persona"=>$v->personal,
                        "contratista"=>$v->contratista_id=="12"?"H2OLMOS S.A.":"CONCESIONARIA TRASVASE OLMOS S.A.",
                        "contratista_id"=>$v->contratista_id,
                        "dni"=>$v->docid,
                    );
                $listaData["0-".$v->codpersonal]="0-".$v->codpersonal;
            }
        }

        return response() -> json($data);
    }

    public function searchAutocompleteConductor($query){
    
        $consulta = "select id, concat(dni,' - ',nombres,' ',apellidos) as descripcion,concat(nombres,' ',apellidos) as persona,dni from conductor where
            deleted_at IS NULL AND
            (dni LIKE '%{$query}%' or concat(nombres,' ',apellidos) like '%".strtoupper($query)."%')";
        $res = DB::select($consulta);
        $data = array();
        $listaData = array();
        foreach($res as $k=>$v){
            if(!isset($listaData[$v->id."-0"])){
                $data[] = array("conductor_id"=>$v->id,
                        "personaconductor_id" => 0,
                        "descripcion"=>$v->descripcion,
                        "persona"=>$v->persona,
                        "dni"=>$v->dni,
                    );
                $listaData[$v->id."-0"] = $v->id."-0";
            }
        }

        $consulta = "select id, concat(dni,otros,' - ',nombres,' ',apellidos) as descripcion,concat(nombres,' ',apellidos) as persona,contratista,contratista_id,dni,otros from persona where
            deleted_at IS NULL AND
            (dni LIKE '%{$query}%' or concat(nombres,' ',apellidos) like '%".strtoupper($query)."%')";
        $res = DB::select($consulta);
        foreach($res as $k=>$v){
            $data[] = array("personaconductor_id"=>$v->id,
                    "conductor_id"=>0,
                    "descripcion"=>$v->descripcion,
                    "persona"=>$v->persona,
                    "dni"=>$v->dni."".$v->otros,
                );
        }

        return response() -> json($data);
    }

    public function searchAutocompleteLugar($query){
    
        $consulta = "select id, nombre as descripcion from lugar where
            deleted_at IS NULL AND
            (nombre like '%".strtoupper($query)."%') ";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    public function searchUnidad(Request $request) {
        $filter = $request->filter;

        $equipos = Equipo::join('ua','ua.id','=','equipo.ua_id')
                        ->select('placa', 'equipo.descripcion','ua.codigo as codigoua','ua.descripcion as ua','decimales','equipo.id','equipo.id as equipo_id',DB::raw('0 as vehiculo_id'))
                        ->where(function ($query) use ($filter) {
                            $query->where('placa', 'like', '%'.$filter.'%')
                                ->orWhere('equipo.descripcion', 'like', '%'.$filter.'%')
                                ->orWhere('ua.codigo', 'like', '%'.$filter.'%');
                        })->get();

        $vehiculos = Vehiculo::join('ua','ua.id','=','vehiculo.ua_id')
                            ->select('placa', 'modelo AS descripcion','ua.codigo as codigoua','ua.descripcion as ua','decimales','vehiculo.id',DB::raw('0 as equipo_id'),'vehiculo.id as vehiculo_id')
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

    public function wp(Request $request){
        $lista = DB::connection('mysql2')->select("call SP_CARGO_LISTAR(?,?,?,?,?,?,?)",['3',0,0,'','','','0']);
        $entidad          = 'Persona';
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $clsLibreria     = new Libreria();
        $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
        $paginacion      = $paramPaginacion['cadenapaginacion'];
        $inicio          = $paramPaginacion['inicio'];
        $fin             = $paramPaginacion['fin'];
        $paginaactual    = $paramPaginacion['nuevapagina'];
        //$lista           = $lista->paginate($filas);
        $request->replace(array('page' => $paginaactual));
        
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = "Persona";
        $persona             = null;
        $formData            = array('persona.update');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton               = 'Listar';
        return view($this->folderview . '.listWP')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar','lista','paginacion', 'inicio', 'fin'));
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportar(Request $request){

        return Excel::download(new ControlExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('tipo'),$request->input('garita'),$request->input('turno'),$request->input('placa'),$request->input('destino'),$request->input('personal'),$request->input('empresa'),$request->input('agente_id'),$request->input('numero'),$request->input('color')), 'control.xlsx');
    }
}
