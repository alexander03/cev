<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Garita;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use App\Concesionaria;

class GaritaController extends Controller
{
    protected $folderview      = 'app.garita';
    protected $tituloAdmin     = 'Garita';
    protected $tituloRegistrar = 'Registrar garita';
    protected $tituloModificar = 'Modificar garita';
    protected $tituloEliminar  = 'Eliminar garita';
    protected $tituloActivar  = 'Activar garita';
    protected $rutas           = array('create' => 'garita.create', 
            'edit'   => 'garita.edit', 
            'delete' => 'garita.eliminar',
            'search' => 'garita.buscar',
            'index'  => 'garita.index',
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
        $entidad          = 'Garita';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('nombre'));
        $resultado        = Garita::where('nombre','like', '%'.$filter.'%')
                            ->where('concesionaria_id','=',$this->getConsecionariaActual())
                            ->orderBy('nombre','asc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ubicacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Mostrar', 'numero' => '1');
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
        $entidad          = 'Garita';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Garita';
        $garita = null;
        $formData = array('garita.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('garita', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100|unique:garita,nombre,NULL,id,deleted_at,NULL,concesionaria_id,'.Concesionaria::getConcesionariaActual()->id);
        $mensajes = array(
            'nombre.required' => 'Debe ingresar una descripcion',
            'nombre.unique' => 'Esta garita ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $obj = new Garita();
            $obj->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $obj->ubicacion= mb_strtoupper($request->input('ubicacion'), 'utf-8');
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->mostrar = $request->input('mostrar');
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'garita', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'garita');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $garita = Garita::find($id);
        $entidad  = 'Garita';
        $formData = array('garita.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('garita', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'garita');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas = array('nombre' => ['required','max:100',Rule::unique('garita')->ignore($id)]);

        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.unique' => 'Esta garita ya existe',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Garita::find($id);
            $objCop = json_encode($obj);

            $obj->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $obj->ubicacion= mb_strtoupper($request->input('ubicacion'), 'utf-8');
            $obj->mostrar = $request->input('mostrar');
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'garita',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'garita');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Garita::find($id);
        $entidad  = 'Garita';
        $formData = array('route' => array('garita.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'garita');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Garita::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'garita', auth()->user()->id,'eliminar'));
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
