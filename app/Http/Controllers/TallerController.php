<?php

namespace App\Http\Controllers;

use App\Librerias\Libreria;
use App\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class TallerController extends Controller
{
    protected $folderview      = 'app.taller';
    protected $tituloAdmin     = 'Taller';
    protected $tituloRegistrar = 'Registrar taller';
    protected $tituloModificar = 'Modificar taller';
    protected $tituloEliminar  = 'Eliminar taller';
    protected $rutas           = array('create' => 'taller.create', 
            'edit'   => 'taller.edit', 
            'delete' => 'taller.eliminar',
            'search' => 'taller.buscar',
            'index'  => 'taller.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Taller';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Taller::where('ruc', 'LIKE', '%'.strtoupper($nombre).'%')->orWhere('razonsocial','like', '%'.strtoupper($nombre).'%')->orderBy('ruc', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'RUC', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Razon Social', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Contacto', 'numero' => '1');
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
        
        $entidad          = 'Taller';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Taller';
        $taller = null;
        $formData = array('taller.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        return view($this->folderview.'.mant')->with(compact('taller', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request){

        $reglas     = [
            'ruc' => 'required',
            'razonsocial' => 'required',
            'contacto' => 'required',
        ];
        $mensajes = [
            'ruc.required' => 'RUC es requerida',
            'razonsocial.required' => 'Razon Social es requerida',
            'contacto.required' => 'Contacto es requerida',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request){
            $obj = new Taller();
            $obj -> ruc = strtoupper($request->input('ruc'));
            $obj -> razonsocial = strtoupper($request->input('razonsocial'));
            $obj -> contacto = strtoupper($request->input('contacto'));
            $obj -> save();

            event( new UserHasCreatedOrDeleted($obj->id,'taller', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request){

        $existe = Libreria::verificarExistencia($id, 'taller');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $taller = Taller::find($id);
        $entidad  = 'Taller';
        $formData = array('taller.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant')->with(compact('taller', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id){

        $reglas     = [
            'ruc' => 'required',
            'razonsocial' => 'required',
            'contacto' => 'required',
        ];
        $mensajes = [
            'ruc.required' => 'RUC es requerida',
            'razonsocial.required' => 'Razon Social es requerida',
            'contacto.required' => 'Contacto es requerida',
        ];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $existe = Libreria::verificarExistencia($id, 'taller');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $obj = Taller::find($id);

            $objOrg = json_encode($obj);

            $obj -> ruc = strtoupper($request->input('ruc'));
            $obj -> razonsocial = strtoupper($request->input('razonsocial'));
            $obj -> contacto = strtoupper($request->input('contacto'));
            $obj -> save();

            event( new UserHasEdited($objOrg,$obj,'taller', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego, Taller $tallerModel){

        $existe = Libreria::verificarExistencia($id, 'taller');
        if ($existe !== true) {
            return $existe;
        }

        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Taller::find($id);
        $entidad  = 'Taller';
        $formData = array('route' => array('taller.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'taller');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Taller::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'taller', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
