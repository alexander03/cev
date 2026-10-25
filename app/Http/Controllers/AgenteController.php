<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Agente;
use App\Tipouser;
use App\User;
use App\Concesionaria;
use App\UserConcesionaria;
use App\Garita;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class AgenteController extends Controller
{
    protected $folderview      = 'app.agente';
    protected $tituloAdmin     = 'Agente';
    protected $tituloRegistrar = 'Registrar agente';
    protected $tituloModificar = 'Modificar agente';
    protected $tituloEliminar  = 'Eliminar agente';
    protected $tituloActivar  = 'Activar agente';
    protected $rutas           = array('create' => 'agente.create', 
            'edit'   => 'agente.edit', 
            'delete' => 'agente.eliminar',
            'search' => 'agente.buscar',
            'index'  => 'agente.index',
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
        $entidad          = 'Agente';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('nombre'));
        $dni              = Libreria::getParam($request->input('dni'));
        $resultado        = Agente::where('concesionaria_id','=',$this->getConsecionariaActual())
                            ->where(function($sql) use($filter){
                                $sql = $sql->where('nombres','like', '%'.$filter.'%')
                                            ->orWhere('apellidos','like', '%'.$filter.'%');
                                })
                            ->where(function($sql) use($dni){
                                if($dni!=""){
                                    $sql = $sql->where('dni','like','%'.$dni.'%');
                                }
                            })
                            ->orderBy('apellidos','asc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellidos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Telefono', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Garita', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
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
        $entidad          = 'Agente';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','user'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Agente';
        $agente = null;
        $formData = array('agente.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboGarita = array(''=>'Ninguno') + Garita::orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('agente', 'formData', 'entidad', 'boton', 'listar','cboGarita'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombres' => 'required',
                        'apellidos' => 'required',
                        'dni' => 'required|unique:agente,dni,NULL,id,deleted_at,NULL',
                        'telefono' => 'required',
                        'username' => 'required',
                        'password' => 'required',
                        );
        $mensajes = array(
            'nombres.required' => 'Debe ingresar un nombre',
            'apellidos.required' => 'Debe ingresar un apellido',
            'dni.required' => 'Debe ingresar un dni',
            'dni.unique' => 'DNI ya existe, ingrese otro',
            'telefono.required' => 'Debe ingresar un telefono',
            'username.required' => 'Debe ingresar un usuario',
            'password.required' => 'Debe ingresar un password',
            
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $concesionariaActual = Concesionaria::getConcesionariaActual();

            $tipo_conductor = Tipouser::where('descripcion', 'AGENTE')->first();

            $user = new User();
            $user->tipouser_id= $tipo_conductor->id;
            $user->name = mb_strtoupper($request->input('nombres'), 'utf-8') .' ' . mb_strtoupper($request->input('apellidos'), 'utf-8');
            $user->username= $request->input('username');
            $user->password= Hash::make($request->input('password'));
            $user->concesionaria_id = $concesionariaActual->id;
            $user->save();

            event( new UserHasCreatedOrDeleted($user->id,'user', auth()->user()->id,'crear'));

            $userconcesionaria = new UserConcesionaria();
            $userconcesionaria->user_id = $user->id;
            $userconcesionaria->concesionaria_id = $concesionariaActual->id;
            $userconcesionaria->save();

            event( new UserHasCreatedOrDeleted($userconcesionaria->id,'userconcesionaria', auth()->user()->id,'crear'));

            $obj = new Agente();
            $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $obj->telefono= mb_strtoupper($request->input('telefono'), 'utf-8');
            $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->user_id = $user->id;
            if($request->input('garita_id')!=""){
                $obj->garita_id = json_encode($request->input('garita_id'));
            }
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'agente', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'agente');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $agente = Agente::find($id);
        $entidad  = 'Agente';
        $formData = array('agente.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboConcesionaria = Concesionaria::pluck('razonsocial','id');
        $cboGarita = array(''=>'Ninguno') + Garita::orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('agente', 'formData', 'entidad', 'boton', 'listar','cboConcesionaria','cboGarita'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'agente');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas     = array('nombres' => 'required',
                        'apellidos' => 'required',
                        'dni' => 'required',
                        'telefono' => 'required',
                        );
        $mensajes = array(
            'nombres.required' => 'Debe ingresar un nombre',
            'apellidos.required' => 'Debe ingresar un apellido',
            'dni.required' => 'Debe ingresar un dni',
            'telefono.required' => 'Debe ingresar un telefono',
            
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Agente::find($id);
            $objCop = json_encode($obj);

            $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $obj->telefono= mb_strtoupper($request->input('telefono'), 'utf-8');
            $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
            $obj->concesionaria_id= $request->input('concesionaria_id');
            if($request->input('garita_id')!=""){
                $obj->garita_id = json_encode($request->input('garita_id'));
            }
            $obj->save();

            $user = User::find($obj->user_id);
            $user->concesionaria_id = $obj->concesionaria_id;
            $user->save();

            $userconcesionaria = UserConcesionaria::where('user_id','=',$user->id)->first();
            $userconcesionaria->concesionaria_id = $obj->concesionaria_id;
            $userconcesionaria->save();

            event(new UserHasEdited($objCop,$obj,'agente',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'agente');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Agente::find($id);
        $entidad  = 'Agente';
        $formData = array('route' => array('agente.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'agente');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Agente::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'agente', auth()->user()->id,'eliminar'));
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
