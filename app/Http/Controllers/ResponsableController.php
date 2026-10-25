<?php

namespace App\Http\Controllers;

use App\Librerias\Libreria;
use App\Responsable;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class ResponsableController extends Controller
{
    protected $folderview      = 'app.responsable';
    protected $tituloAdmin     = 'Responsable';
    protected $tituloRegistrar = 'Registrar responsable';
    protected $tituloModificar = 'Modificar responsable';
    protected $tituloEliminar  = 'Eliminar responsable';
    protected $rutas           = array('create' => 'responsable.create', 
            'edit'   => 'responsable.edit', 
            'delete' => 'responsable.eliminar',
            'search' => 'responsable.buscar',
            'index'  => 'responsable.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Responsable';
        $nombre           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Responsable::where('nombre', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('nombre', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cargo', 'numero' => '1');
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
    
    public function index(){
        
        $entidad          = 'Responsable';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Responsable';
        $responsable = null;
        $formData = array('responsable.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        return view($this->folderview.'.mant')->with(compact('responsable', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request){

        $reglas     = [
            'nombre' => 'required',
            'cargo' => 'required'
        ];
        $mensajes = [
            'nombre.required' => 'Sus nombres son requeridos',
            'cargo.required' => 'Su cargo es requerido',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request){
            $responsable = new Responsable();
            $responsable -> nombre = strtoupper($request->input('nombre'));
            $responsable -> cargo = strtoupper($request->input('cargo'));
            $responsable -> save();

            event( new UserHasCreatedOrDeleted($responsable->id,'responsable', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request){

        $existe = Libreria::verificarExistencia($id, 'responsable');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $responsable = Responsable::find($id);
        $entidad  = 'Responsable';
        $formData = array('responsable.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant')->with(compact('responsable', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id){

        $reglas     = [
            'nombre' => 'required',
            'cargo' => 'required'
        ];
        $mensajes = [
            'nombre.required' => 'Sus nombres son requeridos',
            'cargo.required' => 'Su cargo es requerido',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $existe = Libreria::verificarExistencia($id, 'responsable');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $responsable = Responsable::find($id);

            $responsableOrg = json_encode($responsable);

            $responsable -> nombre = strtoupper($request->input('nombre'));
            $responsable -> cargo = strtoupper($request->input('cargo'));
            $responsable -> save();

            event( new UserHasEdited($responsableOrg,$responsable,'responsable', auth()->user()->id));

        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego){

        $existe = Libreria::verificarExistencia($id, 'responsable');
        if ($existe !== true) {
            return $existe;
        }

        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Responsable::find($id);
        $entidad  = 'Responsable';
        $formData = array('route' => array('responsable.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'responsable');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $responsable = Responsable::find($id);

            event( new UserHasCreatedOrDeleted($responsable->id,'responsable', auth()->user()->id,'eliminar'));

            $responsable->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
