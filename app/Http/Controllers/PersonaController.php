<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Persona;
use App\Ua;
use App\Contratista;
use App\Concesionaria;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class PersonaController extends Controller
{
    protected $folderview      = 'app.persona';
    protected $tituloAdmin     = 'Persona';
    protected $tituloRegistrar = 'Registrar persona';
    protected $tituloModificar = 'Modificar persona';
    protected $tituloEliminar  = 'Eliminar persona';
    protected $tituloActivar  = 'Activar persona';
    protected $rutas           = array('create' => 'persona.create', 
            'edit'   => 'persona.edit', 
            'delete' => 'persona.eliminar',
            'search' => 'persona.buscar',
            'index'  => 'persona.index',
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
        $entidad          = 'Persona';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('filter'));
        $empresa = $request->input('empresa');
        $dni = $request->input('dni');
        $ua = $request->input('ua');
        $resultado        = Persona::leftjoin('ua','ua.id','=','persona.ua_id')
                            ->where(function($sql) use($filter){
                                $sql = $sql->where(DB::raw('concat(nombres,\' \',apellidos)'),'like', '%'.$filter.'%');
                                        //->orWhere('apellidos','like', '%'.$filter.'%');
                                    })
                            ->orderBy('apellidos','asc');
        if($empresa!=""){
            $resultado = $resultado->where('contratista','like','%'.$empresa.'%');
        }
        if($ua!=""){
            $resultado = $resultado->where(DB::raw('concat(ua.codigo,\' \',ua.descripcion)'),'like','%'.$ua.'%');
        }
        if($dni!=""){
            $resultado = $resultado->where('dni','like','%'.$dni.'%');
        }
        $lista            = $resultado->select('persona.*',DB::raw('concat(ua.codigo,\' \',ua.descripcion) as ua2'))->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellidos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Extr.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
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
        $entidad          = 'Persona';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','user'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Persona';
        $persona = null;
        $formData = array('persona.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboContratista = array(""=>'Seleccione') + Contratista::orderBy('razonsocial','asc')->pluck('razonsocial','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar','cboContratista'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        if($request->input('otros')=="" && $request->input('mostrardni')=="SI"){
            $reglas     = array('nombres' => 'required',
                            'apellidos' => 'required',
                            'contratista' => 'required',
                            'dni' => 'required|unique:persona,dni,NULL,id,deleted_at,NULL',
                            );
            $mensajes = array(
                'nombres.required' => 'Debe ingresar un nombre',
                'apellidos.required' => 'Debe ingresar un apellido',
                'dni.required' => 'Debe ingresar un dni',
                'dni.unique' => 'DNI ya registrado',
                'contratista' => 'Debe ingresar contratista',
            );

            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
            $lista = DB::connection('mysql2')->select("call SP_CARGO_LISTAR(?,?,?,?,?,?,?)",['3',0,0,'','','','0']);
            foreach($lista as $k=>$v){
                if(trim($v->docid)==trim($request->input('dni'))){
                    return json_encode(['dni' => ['DNI ya registrado en workpeople']]);
                }
            }
            $lista = Persona::where('nombres','like',mb_strtoupper($request->input('nombres'), 'utf-8'))
                            ->where('apellidos','like',mb_strtoupper($request->input('nombres'), 'utf-8'))
                            ->first();
            if(!is_null($lista)){
                return json_encode(['nombres' => ['Nombres y apellidos ya registrados en otra persona']]);
            }
        }

        $error = DB::transaction(function() use($request){

            $obj = new Persona();
            $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
            $obj->otros= mb_strtoupper($request->input('otros'), 'utf-8');
            $obj->mostrardni= mb_strtoupper($request->input('mostrardni'), 'utf-8');
            $obj->contratista = $request->input('contratista');
            $obj->contratista_id = $request->input('contratista_id');
            $obj->empresa_id = $request->input('empresa_id');
            $obj->correo = $request->input('correo');
            if($request->input('ua_id')!=""){
                $obj->ua_id = Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            }else{
                $obj->ua_id = 0;
            }
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'persona', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $persona = Persona::find($id);
        $entidad  = 'Persona';
        $formData = array('persona.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboContratista = array("0"=>'Seleccione') + Contratista::orderBy('razonsocial','asc')->pluck('razonsocial','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar','cboContratista'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        if($request->input('otros')=="" && $request->input('mostrardni')=="SI"){
            $reglas     = array('nombres' => 'required',
                            'apellidos' => 'required',
                            'dni' => 'required|unique:persona,dni,'.$id.',id,deleted_at,NULL',
                            'contratista' => 'required',

                            );
            $mensajes = array(
                'nombres.required' => 'Debe ingresar un nombre',
                'apellidos.required' => 'Debe ingresar un apellido',
                'dni.required' => 'Debe ingresar un dni',
                'dni.unique' => 'DNI ya registrado',
                'contratista' => 'Debe ingresar contratista',
            );
            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            } 
            $lista = DB::connection('mysql2')->select("call SP_CARGO_LISTAR(?,?,?,?,?,?,?)",['3',0,0,'','','','0']);
            foreach($lista as $k=>$v){
                if(trim($v->docid)==trim($request->input('dni'))){
                    return json_encode(['dni' => ['DNI ya registrado en workpeople']]);
                }
            }
        }
        $error = DB::transaction(function() use($request, $id){
            $obj = Persona::find($id);
            $objCop = json_encode($obj);

            $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
            $obj->otros= mb_strtoupper($request->input('otros'), 'utf-8');
            $obj->contratista = $request->input('contratista');
            $obj->contratista_id = $request->input('contratista_id');
            $obj->empresa_id = $request->input('empresa_id');
            $obj->correo = $request->input('correo');
            if($request->input('ua_id')!=""){
                $obj->ua_id = Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            }else{
                $obj->ua_id = 0;
            }
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'persona',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Persona::find($id);
        $entidad  = 'Persona';
        $formData = array('route' => array('persona.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Persona::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'persona', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
