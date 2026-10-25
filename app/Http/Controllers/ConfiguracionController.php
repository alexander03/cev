<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Configuracion;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class ConfiguracionController extends Controller
{
    protected $folderview      = 'app.configuracion';
    protected $tituloAdmin     = 'VIGENCIA DE ';
    protected $tituloRegistrar = 'Registrar area';
    protected $tituloModificar = 'Modificar vigencia';
    protected $tituloEliminar  = 'Eliminar area';
    protected $rutas           = array('create' => 'configuracion.create', 
            'edit'   => 'configuracion.edit', 
            'delete' => 'configuracion.eliminar',
            'search' => 'configuracion.buscar',
            'index'  => 'configuracion.index',
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
        $entidad          = 'Configuracion';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Configuracion::where('id','=',$request->input('id'));
        $lista=$resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripci&oacute;n', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Valor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
        if($request->input('id')<7){
            $titulo_modificar = $this->tituloModificar;
        }else{
            $titulo_modificar = "Modificar";
        }
        
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

    public function index(Request $request)
    {
        $entidad          = 'Configuracion';
        $id = $request->input('id');
        $configuracion = Configuracion::find($id);
        if($id<7){
            $title            = $this->tituloAdmin. " ".$configuracion->nombre;
        }else{
            $title            = $configuracion->nombre;
        }
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','id'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Configuracion';
        $configuracion = null;
        $formData = array('configuracion.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        return view($this->folderview.'.mant')->with(compact('configuracion', 'formData', 'entidad','cboArea', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('valor' => 'required');
        $mensajes = array(
            'valor.required' => 'Debe ingresar un valor',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $area = new Configuracion();
            $area->descripcion= strtoupper($request->input('descripcion'));
            if($request->input('areapadre_id')!=0){
                $area->areapadre_id= strtoupper($request->input('areapadre_id'));
                $area->nivel= (Area::find($request->input('areapadre_id'))->get())->nivel+1;
            }
            else{
                $area->nivel=1;
            }
            event( new UserHasCreatedOrDeleted($area->id,'area', auth()->user()->id,'crear'));
            $area->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracion');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $configuracion = Configuracion::find($id);
        $entidad  = 'Configuracion';
        $formData = array('configuracion.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('configuracion', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracion');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('valor' => 'required');
        $mensajes = array(
            'valor.required' => 'Debe ingresar un valor',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $obj = Configuracion::find($id);
            $obj->valor = $request->input('valor');
            $obj->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $area = Area::find($id);
            event( new UserHasCreatedOrDeleted($area->id,'area', auth()->user()->id,'eliminar'));
            $area->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Area::find($id);
        $entidad  = 'Area';
        $formData = array('route' => array('areas.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
