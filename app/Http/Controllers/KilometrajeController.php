<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Kilometraje;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class KilometrajeController extends Controller
{
    protected $folderview      = 'app.kilometrajes';
    protected $tituloAdmin     = 'Kilometrajes';
    protected $tituloRegistrar = 'Registrar kilometraje';
    protected $tituloModificar = 'Modificar kilometraje';
    protected $tituloEliminar  = 'Eliminar kilometraje';
    protected $tituloActivar  = 'Activar kilometraje';
    protected $rutas           = array(
        'create' => 'kilometrajes.create', 
        'edit'   => 'kilometrajes.edit', 
        'delete' => 'kilometrajes.eliminar',
        'activar' => 'kilometrajes.activar',
        'search' => 'kilometrajes.buscar',
        'index'  => 'kilometrajes.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Kilometraje';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Kilometraje::getFilter($estado, $filter);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Lim. Inferior', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Lim. Superior', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        }
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Kilometraje';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Kilometraje';
        $kilometraje = null;
        $formData = array('kilometrajes.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('kilometraje', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'descripcion' => 'required|unique:marca,descripcion',
            'limite_inf' => 'required|lt:' . $request->input('limite_sup'),
            'limite_sup' => 'required',
        );
        $mensajes = array(
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.unique' => 'Esta marca ya existe',
            'limite_inf.required' => 'Debe ingresar un límite inferior',
            'limite_inf.lt' => 'El límite inferior debe ser menor que el mayor',
            'limite_sup.required' => 'Debe ingresar un límite superior'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) return $validacion->messages()->toJson();

        $error = DB::transaction(function() use($request){
            $kilometraje = new Kilometraje();
            $kilometraje->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $kilometraje->limite_inf = $request->input('limite_inf');
            $kilometraje->limite_sup = $request->input('limite_sup');
            $kilometraje->save();

            event( new UserHasCreatedOrDeleted($kilometraje->id,'kilometraje', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) return $existe;
        
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $kilometraje = Kilometraje::find($id);
        $entidad  = 'Kilometraje';
        $formData = array('kilometrajes.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('kilometraje', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id) {
        $existe = Libreria::verificarExistencia($id, 'kilometraje');
        if ($existe !== true) return $existe;
        
        $reglas = array(
            'descripcion' => ['required',Rule::unique('kilometraje')->ignore($id)],
            'limite_inf' => 'required|lt:' . $request->input('limite_sup'),
            'limite_sup' => 'required',
        );

        $mensajes = array(
            'descripcion.required' => 'Debe ingresar una descripción',
            'descripcion.unique' => 'Esta marca ya existe',
            'limite_inf.required' => 'Debe ingresar un límite inferior',
            'limite_inf.lt' => 'El límite inferior debe ser menor que el mayor',
            'limite_sup.required' => 'Debe ingresar un límite superior'
        );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) return $validacion->messages()->toJson();

        $error = DB::transaction(function() use($request, $id){
            $kilometraje = Kilometraje::find($id);

            $kilometrajeOrg = json_encode($kilometraje);

            $kilometraje->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $kilometraje->limite_inf = $request->input('limite_inf');
            $kilometraje->limite_sup = $request->input('limite_sup');
            $kilometraje->save();

            event( new UserHasEdited($kilometrajeOrg,$kilometraje,'kilometraje', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'kilometraje');
        if ($existe !== true) return $existe;

        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;

        $mensaje=true;
        $modelo   = Kilometraje::find($id);
        $entidad  = 'Kilometraje';
        $formData = array('route' => array('kilometrajes.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'kilometraje');
        if ($existe !== true) return $existe;

        $error = DB::transaction(function() use($id){
            $kilometraje = Kilometraje::find($id);

            event( new UserHasCreatedOrDeleted($kilometraje->id,'kilometraje', auth()->user()->id,'eliminar'));
            $kilometraje->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego){
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;

        $modelo   = Kilometraje::find($id);
        $entidad  = 'Kilometraje';
        $formData = array('route' => array('kilometrajes.reactivar', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reactivar($id){
        $error = DB::transaction(function() use($id){
            Kilometraje::onlyTrashed()->where('id', $id)->restore();
            event( new UserHasCreatedOrDeleted($id,'kilometraje', auth()->user()->id,'reactivar'));
        });
        return is_null($error) ? "OK" : $error;
    }
}
