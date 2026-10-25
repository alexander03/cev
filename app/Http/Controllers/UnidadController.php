<?php

namespace App\Http\Controllers;

use App\Librerias\Libreria;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class UnidadController extends Controller
{
    protected $folderview      = 'app.unidad';
    protected $tituloAdmin     = 'Unidad';
    protected $tituloRegistrar = 'Registrar unidad';
    protected $tituloModificar = 'Modificar unidad';
    protected $tituloEliminar  = 'Eliminar unidad';
    protected $rutas           = array('create' => 'unidad.create', 
            'edit'   => 'unidad.edit', 
            'delete' => 'unidad.eliminar',
            'search' => 'unidad.buscar',
            'index'  => 'unidad.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Unidad';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Unidad::where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('descripcion', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
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
    
    public function index(){
        
        $entidad          = 'Unidad';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Unidad';
        $unidad = null;
        $formData = array('unidad.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        return view($this->folderview.'.mant')->with(compact('unidad', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request){

        $reglas     = [
            'descripcion' => 'required',
        ];
        $mensajes = [
            'descripcion.required' => 'Su descripción es requerida',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request){
            $unidad = new Unidad();
            $unidad -> descripcion = strtoupper($request->input('descripcion'));
            $unidad -> save();

            event( new UserHasCreatedOrDeleted($unidad->id,'unidad', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request){

        $existe = Libreria::verificarExistencia($id, 'unidad');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $unidad = Unidad::find($id);
        $entidad  = 'Unidad';
        $formData = array('unidad.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant')->with(compact('unidad', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id){

        $reglas     = [
            'descripcion' => 'required'
        ];
        $mensajes = [
            'descripcion.required' => 'Su descripción es requerida',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $existe = Libreria::verificarExistencia($id, 'unidad');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $unidad = Unidad::find($id);

            $unidadOrg = json_encode($unidad);

            $unidad -> descripcion = strtoupper($request->input('descripcion'));
            $unidad -> save();

            event( new UserHasEdited($unidadOrg,$unidad,'unidad', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego, Unidad $unidadModel){

        $existe = Libreria::verificarExistencia($id, 'unidad');
        if ($existe !== true) {
            return $existe;
        }

        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Unidad::find($id);
        $entidad  = 'Unidad';
        $formData = array('route' => array('unidad.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'unidad');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $unidad = Unidad::find($id);
            event( new UserHasCreatedOrDeleted($unidad->id,'unidad', auth()->user()->id,'eliminar'));
            $unidad->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
