<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Pasaje;
use App\LugarPasaje;
use App\Concesionaria;
use App\Empresa;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use App\Curl;
use App\Ua;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PasajeExport;

class PasajeController extends Controller
{
    protected $folderview      = 'app.pasaje';
    protected $tituloAdmin     = 'Pasajes';
    protected $tituloRegistrar = 'Registrar Pasaje';
    protected $tituloModificar = 'Modificar Pasaje';
    protected $tituloEliminar  = 'Eliminar Pasaje';
    protected $rutas           = array('create' => 'pasaje.create', 
            'edit'   => 'pasaje.edit', 
            'delete' => 'pasaje.eliminar',
            'search' => 'pasaje.buscar',
            'index'  => 'pasaje.index',
        );

         /**
     * Create a new Pasajeler instance.
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
        $entidad          = 'Pasaje';
        $fechafin         = $request->input('fechafin');
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $reserva          = $request->input('reserva');
        $tipofecha        = $request->input('tipofecha');
        $personal         = $request->input('personal');
        $origen           = $request->input('origen');
        $destino          = $request->input('destino');
        $empresa          = $request->input('empresa');
        $tipo             = $request->input('tipo');
        $concesionaria    = $request->input('concesionaria');
        $resultado        = Pasaje::join('lugarpasaje as origen','origen.id','=','pasaje.origen_id')
                            ->join('lugarpasaje as destino','destino.id','=','pasaje.destino_id')
                            ->leftjoin('ua','ua.id','=','pasaje.ua_id')
                            ->orderBy('fechaorigen','desc');
        if($fechainicio!=""){
            $resultado = $resultado->where('fecha'.$tipofecha,'>=',DB::raw("'".$fechainicio." 00:00:00'"));
        }
        if($fechafin!=""){
            $resultado = $resultado->where('fecha'.$tipofecha,'<=',DB::raw("'".$fechafin." 23:59:59'"));
        }
        if($reserva!=""){
            $resultado = $resultado->where('reserva','like','%'.$reserva.'%');
        }
        if($origen!=""){
            $resultado = $resultado->where('origen.nombre','like','%'.$origen.'%');
        }
        if($destino!=""){
            $resultado = $resultado->where('destino.nombre','like','%'.$destino.'%');
        }
        if($personal!=""){
            $resultado = $resultado->where('pasaje.persona','like','%'.$personal.'%');
        }
        if($empresa!=""){
            $resultado = $resultado->where('pasaje.empresa','like','%'.$empresa.'%');
        }
        if($tipo!=""){
            $resultado = $resultado->where('pasaje.tipo','like','%'.$tipo.'%');
        }
        if($concesionaria!=""){
            $resultado = $resultado->where('pasaje.concesionaria_id','=',$concesionaria);
        }
        $lista            = $resultado->select('pasaje.*')->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora Origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Destino', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Fecha Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro. Vuelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cod. Reserva', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Aerolinea', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concesionaria', 'numero' => '1');
        //if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        //}
        
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
        $entidad          = 'Pasaje';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipoFecha = array('origen'=>'Origen');//'destino'=>'Destino'
        $cboTipo = array(''=>'Todos','IDA'=>'IDA','VUELTA'=>'VUELTA');
        $cboConcesionaria = array(''=>'Todos') + Concesionaria::orderBy('abreviatura','asc')->pluck('abreviatura','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboTipoFecha','cboTipo','cboConcesionaria'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Pasaje';
        $pasaje = null;
        $formData = array('pasaje.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboTipo = array(''=>'Seleccione','IDA'=>'IDA','VUELTA'=>'VUELTA');
        return view($this->folderview.'.mant')->with(compact('pasaje', 'formData', 'entidad', 'boton', 'listar','cboTipo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('fechaorigen' => 'required',
                        'horaorigen' => 'required',
                        //'fechadestino' => 'required',
                        'horadestino' => 'required',
                        'numvuelo' => 'required',
                        'reserva' => 'required',
                        'aerolinea' => 'required',
                        'origen' => 'required',
                        'destino' => 'required',
                        'persona'=>'required',
                        'contratista'=>'required',
                        'tipo'=>'required',
                        'ua_id_trabajo' => 'required',
                        );
        $mensajes = array(
            'fechaorigen.required' => 'Debe ingresar una fecha origen',
            'horaorigen.required' => 'Debe ingresar una hora origen',
            //'fechadestino.required' => 'Debe ingresar una fecha destino',
            'horadestino.required' => 'Debe ingresar una hora destino',
            'numvuelo.required' => 'Debe ingresar un Nro de Vuelo',
            'aerolinea.required' => 'Debe ingresar una Aerolinea',
            'destino.required' => 'Debe ingresar un destino',
            'origen.required' => 'Debe ingresar un origen',
            'persona.required' => 'Debe ingresar una persona',
            'contratista.required' => 'Debe ingresar una empresa',
            'tipo.required' => 'Debe seleccionar un tipo',
            'ua_id_trabajo.required' => 'Debe registrar una ua',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        if($request->input('origen_id')==$request->input('destino_id')){
            return json_encode(['origen' => ['Origen y Destino no deben ser iguales']]);
        }
        $id=0;
        $error = DB::transaction(function() use($request,&$id){
            $obj = new Pasaje();
            $obj->fechaorigen= $request->input('fechaorigen');
            $obj->horaorigen= $request->input('horaorigen');
            $obj->fechadestino= $request->input('fechadestino');
            $obj->horadestino= $request->input('horadestino');
            $obj->numvuelo= $request->input('numvuelo');
            $obj->reserva= $request->input('reserva');
            $obj->aerolinea= $request->input('aerolinea');
            $obj->destino_id= $request->input('destino_id')+0;
            $obj->origen_id= $request->input('origen_id')+0;
            $obj->persona  = $request->input('persona');
            $obj->dni  = $request->input('dni');
            $obj->persona_id  = $request->input('persona_id');
            $obj->personawp_id  = $request->input('personawp_id');
            $obj->contratista_id  = $request->input('contratista_id');
            $obj->empresa  = $request->input('contratista');
            $obj->tipo  = $request->input('tipo');
            if($request -> input('ua_id_trabajo')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id_trabajo')) -> get();
                $obj->ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->save();

            $id = $obj->id;
            event( new UserHasCreatedOrDeleted($obj->id,'pasaje', auth()->user()->id,'crear'));
            
        });
        
        /*$cc = new Curl(false,'https://h2olmos.pe/cev/PHPMailer/notificacionPasaje.php');
        $url = 'https://h2olmos.pe/cev/PHPMailer/notificacionPasaje.php?id='.$id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);*/

        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'pasaje');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pasaje = Pasaje::find($id);
        $entidad  = 'Pasaje';
        $formData = array('pasaje.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboTipo = array(''=>'Seleccione','IDA'=>'IDA','VUELTA'=>'VUELTA');
        return view($this->folderview.'.mant')->with(compact('pasaje', 'formData', 'entidad', 'boton', 'listar','cboTipo'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'pasaje');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas     = array('fechaorigen' => 'required',
                        'horaorigen' => 'required',
                        //'fechadestino' => 'required',
                        'horadestino' => 'required',
                        'numvuelo' => 'required',
                        'reserva' => 'required',
                        'aerolinea' => 'required',
                        'origen' => 'required',
                        'destino' => 'required',
                        'persona'=>'required',
                        'contratista'=>'required',
                        'tipo'=>'required',
                        'ua_id_trabajo' => 'required',
                        );
        $mensajes = array(
            'fechaorigen.required' => 'Debe ingresar una fecha origen',
            'horaorigen.required' => 'Debe ingresar una hora origen',
            //'fechadestino.required' => 'Debe ingresar una fecha destino',
            'horadestino.required' => 'Debe ingresar una hora destino',
            'numvuelo.required' => 'Debe ingresar un Nro de Vuelo',
            'aerolinea.required' => 'Debe ingresar una Aerolinea',
            'destino.required' => 'Debe ingresar un destino',
            'origen.required' => 'Debe ingresar un origen',
            'persona.required' => 'Debe ingresar una persona',
            'contratista.required' => 'Debe ingresar una empresa',
            'tipo.required' => 'Debe seleccionar un tipo',
            'ua_id_trabajo.required' => 'Debe registrar una ua',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        if($request->input('origen_id')==$request->input('destino_id')){
            return json_encode(['origen' => ['Origen y Destino no deben ser iguales']]);
        }

        $error = DB::transaction(function() use($request, $id){
            $obj = Pasaje::find($id);
            $objCop = json_encode($obj);

            $obj->fechaorigen= $request->input('fechaorigen');
            $obj->horaorigen= $request->input('horaorigen');
            $obj->fechadestino= $request->input('fechadestino');
            $obj->horadestino= $request->input('horadestino');
            $obj->numvuelo= $request->input('numvuelo');
            $obj->reserva= $request->input('reserva');
            $obj->aerolinea= $request->input('aerolinea');
            $obj->destino_id= $request->input('destino_id')+0;
            $obj->origen_id= $request->input('origen_id')+0;
            $obj->persona  = $request->input('persona');
            $obj->dni  = $request->input('dni');
            $obj->persona_id  = $request->input('persona_id');
            $obj->personawp_id  = $request->input('personawp_id');
            $obj->contratista_id  = $request->input('contratista_id');
            $obj->empresa  = $request->input('contratista');
            $obj->tipo  = $request->input('tipo');
            if($request -> input('ua_id_trabajo')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id_trabajo')) -> get();
                $obj->ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'Pasaje',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'pasaje');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Pasaje::find($id);
        $entidad  = 'Pasaje';
        $formData = array('route' => array('pasaje.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'pasaje');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Pasaje::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'Pasaje', auth()->user()->id,'eliminar'));
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
            if(!isset($listaData["0-".$v->codusuario])){
                $data[] = array("persona_id"=>0,
                        "personawp_id"=>$v->codusuario,
                        "descripcion"=>$v->docid." - ".$v->personal,
                        "persona"=>$v->personal,
                        "contratista"=>$v->contratista_id=="12"?"H2OLMOS S.A.":"CONCESIONARIA TRASVASE OLMOS S.A.",
                        "contratista_id"=>$v->contratista_id,
                        "dni"=>$v->docid,
                    );
                $listaData["0-".$v->codusuario]="0-".$v->codusuario;
            }
        }

        return response() -> json($data);
    }

    public function searchAutocompleteLugar($query){
    
        $consulta = "select id, nombre as descripcion from lugarpasaje where
            deleted_at IS NULL AND
            (nombre like '%".strtoupper($query)."%') ";
        $res = DB::select($consulta);
        
        return response() -> json($res);
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

        return Excel::download(new PasajeExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('tipofecha'),$request->input('reserva'),$request->input('origen'),$request->input('destino'),$request->input('personal'),$request->input('empresa'), $request->input('concesionaria')), 'Pasaje.xlsx');
    }
}
