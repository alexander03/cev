<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Horometraje;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class HorometrajeController extends Controller
{
    protected $folderview      = 'app.horometrajes';
    protected $tituloAdmin     = 'Horometro';
    protected $tituloRegistrar = 'Registrar horometraje';
    protected $tituloModificar = 'Modificar horometraje';
    protected $tituloEliminar  = 'Eliminar horometraje';
    protected $tituloActivar  = 'Activar horometraje';
    protected $rutas           = array(
        'create' => 'horometrajes.create', 
        'edit'   => 'horometrajes.edit', 
        'delete' => 'horometrajes.eliminar',
        'activar' => 'horometrajes.activar',
        'search' => 'horometrajes.buscar',
        'index'  => 'horometrajes.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Horometraje';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Horometraje::getFilter($estado, $filter);
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
        $entidad          = 'Horometraje';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Horometraje';
        $horometraje = null;
        $formData = array('horometrajes.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('horometraje', 'formData', 'entidad', 'boton', 'listar'));
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
            $horometraje = new Horometraje();
            $horometraje->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $horometraje->limite_inf = $request->input('limite_inf');
            $horometraje->limite_sup = $request->input('limite_sup');
            $horometraje->save();

            event( new UserHasCreatedOrDeleted($horometraje->id,'horometraje', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) return $existe;
        
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $horometraje = Horometraje::find($id);
        $entidad  = 'Horometraje';
        $formData = array('horometrajes.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('horometraje', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id) {
        $existe = Libreria::verificarExistencia($id, 'horometraje');
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
            $horometraje = Horometraje::find($id);

            $horometrajeOrg = json_encode($horometraje);

            $horometraje->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $horometraje->limite_inf = $request->input('limite_inf');
            $horometraje->limite_sup = $request->input('limite_sup');
            $horometraje->save();

            event( new UserHasEdited($horometrajeOrg,$horometraje,'horometraje', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'horometraje');
        if ($existe !== true) return $existe;

        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;

        $mensaje=true;
        $modelo   = Horometraje::find($id);
        $entidad  = 'Horometraje';
        $formData = array('route' => array('horometrajes.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'horometraje');
        if ($existe !== true) return $existe;

        $error = DB::transaction(function() use($id){
            $horometraje = Horometraje::find($id);

            event( new UserHasCreatedOrDeleted($horometraje->id,'horometraje', auth()->user()->id,'eliminar'));
            $horometraje->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego){
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;

        $modelo   = Horometraje::find($id);
        $entidad  = 'Horometraje';
        $formData = array('route' => array('horometrajes.reactivar', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reactivar($id){
        $error = DB::transaction(function() use($id){
            Horometraje::onlyTrashed()->where('id', $id)->restore();
            event( new UserHasCreatedOrDeleted($id,'horometraje', auth()->user()->id,'reactivar'));
        });
        return is_null($error) ? "OK" : $error;
    }
}
