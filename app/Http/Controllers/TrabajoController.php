<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Trabajo;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class TrabajoController extends Controller
{
    protected $folderview      = 'app.trabajos';
    protected $tituloAdmin     = 'Trabajos';
    protected $tituloRegistrar = 'Registrar trabajo';
    protected $tituloModificar = 'Modificar trabajo';
    protected $tituloEliminar  = 'Eliminar trabajo';
    protected $rutas           = array('create' => 'trabajos.create', 
            'edit'   => 'trabajos.edit', 
            'delete' => 'trabajos.eliminar',
            'search' => 'trabajos.buscar',
            'index'  => 'trabajos.index',
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
        $entidad          = 'Trabajo';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Trabajo::where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('descripcion', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
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
        $entidad          = 'Trabajo';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Trabajo';
        $trabajo = null;
        $formData = array('trabajos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('trabajo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('descripcion' => 'required|max:100');
        $mensajes = array(
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $trabajo = new Trabajo();
            $trabajo->descripcion= strtoupper($request->input('descripcion'));
            $trabajo->save();

            event( new UserHasCreatedOrDeleted($trabajo->id,'trabajo', auth()->user()->id,'crear'));

        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'trabajo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $trabajo = Trabajo::find($id);
        $entidad  = 'Trabajo';
        $formData = array('trabajos.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('trabajo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'trabajo');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('descripcion' => 'required|max:100');
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $trabajo = Trabajo::find($id);

            $trabajoOrg = json_encode($trabajo);

            $trabajo->descripcion= strtoupper($request->input('descripcion'));
            $trabajo->save();


            event( new UserHasEdited($trabajoOrg,$trabajo,'trabajo', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'trabajo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $trabajo = Trabajo::find($id);
            event( new UserHasCreatedOrDeleted($trabajo->id,'trabajo', auth()->user()->id,'eliminar'));
            $trabajo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'trabajo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Trabajo::find($id);
        $entidad  = 'Trabajo';
        $formData = array('route' => array('trabajos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
