<?php

namespace App\Http\Controllers;

use Validator;
use App\Tipocombustible;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class TipocombustibleController extends Controller
{
    protected $folderview      = 'app.tipocombustible';
    protected $tituloAdmin     = 'Tipo de combustible';
    protected $tituloRegistrar = 'Registrar tipo de combustible';
    protected $tituloModificar = 'Modificar tipo de combustible';
    protected $tituloEliminar  = 'Eliminar tipo de combustible';
    protected $rutas           = array('create' => 'tipocombustible.create', 
            'edit'   => 'tipocombustible.edit', 
            'delete' => 'tipocombustible.eliminar',
            'search' => 'tipocombustible.buscar',
            'index'  => 'tipocombustible.index',
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
        $entidad          = 'Tipocombustible';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Tipocombustible::where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('descripcion', 'ASC');
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
        $entidad          = 'Tipocombustible';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Tipocombustible';
        $tipocombustible = null;
        $formData = array('tipocombustible.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('tipocombustible', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('descripcion' => 'required|max:35');
        $mensajes = array(
        	'descripcion.required'         => 'Debe ingresar una descripción',
        	'descripcion.max'         	   => 'La descripción supera los 35 caracteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $tipocombustible = new Tipocombustible();
            $tipocombustible->descripcion = strtoupper($request->input('descripcion'));
            $tipocombustible->save();

            event( new UserHasCreatedOrDeleted($tipocombustible->id,'tipocombustible', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }



    public function show($id)
    {
        //
    }


    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'tipocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $tipocombustible = Tipocombustible::find($id);
        $entidad  = 'Tipocombustible';
        $formData = array('tipocombustible.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('tipocombustible', 'formData', 'entidad', 'boton', 'listar'));
    }

     public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('descripcion' => 'required|max:35');
        $mensajes = array(
        	'descripcion.required'         => 'Debe ingresar una descripción',
        	'descripcion.max'         	   => 'La descripción supera los 35 caracteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $tipocombustible = Tipocombustible::find($id);

            $tipocombustibleOrg = json_encode($tipocombustible);

            $tipocombustible->descripcion = strtoupper($request->input('descripcion'));
            $tipocombustible->save();

            event( new UserHasEdited($tipocombustibleOrg,$tipocombustible,'tipocombustible', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $tipocombustible = Tipocombustible::find($id);
            event( new UserHasCreatedOrDeleted($tipocombustible->id,'tipocombustible', auth()->user()->id,'eliminar'));    
            $tipocombustible->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'tipocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Tipocombustible::find($id);
        $entidad  = 'Tipocombustible';
        $formData = array('route' => array('tipocombustible.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

}
