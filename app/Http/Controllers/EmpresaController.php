<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Empresa;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class EmpresaController extends Controller
{
    protected $folderview      = 'app.empresa';
    protected $tituloAdmin     = 'Empresa';
    protected $tituloRegistrar = 'Registrar empresa';
    protected $tituloModificar = 'Modificar empresa';
    protected $tituloEliminar  = 'Eliminar empresa';
    protected $rutas           = array('create' => 'empresa.create', 
            'edit'   => 'empresa.edit', 
            'delete' => 'empresa.eliminar',
            'search' => 'empresa.buscar',
            'index'  => 'empresa.index',
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
        $entidad          = 'Empresa';
        $nombre           = Libreria::getParam($request->input('razonsocial'));
        $ruc              = Libreria::getParam($request->input('ruc'));
        $resultado        = Empresa::where('razonsocial', 'LIKE', '%'.strtoupper($nombre).'%')->where('ruc','like','%'.$ruc.'%');
        $lista=$resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'RUC', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Razón Social', 'numero' => '1');
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
        $entidad          = 'Empresa';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Empresa';
        $empresa = null;
        $formData = array('empresa.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        return view($this->folderview.'.mant')->with(compact('empresa', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('razonsocial' => 'required|unique:empresa,razonsocial,NULL,id,deleted_at,NULL|max:200');
        $mensajes = array(
            'razonsocial.required' => 'Debe ingresar una razón social',
            'razonsocial.unique' => 'Su razón social ya existe, especifique otro',
            'razonsocial.max' => 'La razón social debe tener max. 200 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $obj = new Empresa();
            $obj->razonsocial= strtoupper($request->input('razonsocial'));
            $obj->ruc= strtoupper($request->input('ruc'));
            $obj->user_id = auth()->user()->id;
            
            event( new UserHasCreatedOrDeleted($obj->id,'empresa', auth()->user()->id,'crear'));
            $obj->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'empresa');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $empresa = Empresa::find($id);
        $entidad  = 'Empresa';
        $formData = array('empresa.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('empresa', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'empresa');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('razonsocial' => 'required|unique:empresa,razonsocial,'.$id.',id,deleted_at,NULL|max:200');
        $mensajes = array(
            'razonsocial.required' => 'Debe ingresar una razón social',
            'razonsocial.max' => 'La razón social debe tener max. 200 caracteres',
            'razonsocial.unique' => 'Su razón social ya existe, especifique otro',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Empresa::find($id);

            $objCop = json_encode($obj);

            $obj->razonsocial= strtoupper($request->input('razonsocial'));
            $obj->ruc= strtoupper($request->input('ruc'));
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'empresa',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'empresa');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Empresa::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'empresa', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'empresa');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Empresa::find($id);
        $entidad  = 'Empresa';
        $formData = array('route' => array('empresa.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
