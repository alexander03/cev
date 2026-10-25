<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoriarepuesto;
use App\Librerias\Libreria;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class CategoriarepuestoController extends Controller
{
    protected $folderview      = 'app.categoriarepuesto';
    protected $tituloAdmin     = 'Categoria Repuesto';
    protected $entidad         = 'Categoriarepuesto';
    protected $tituloRegistrar = 'Registrar Categoria repuesto';
    protected $tituloModificar = 'Modificar Categoria repuesto';
    protected $tituloEliminar  = 'Eliminar Categoria repuesto';
    protected $rutas           = array('create' => 'categoriarepuesto.create', 
            'edit'   => 'categoriarepuesto.edit', 
            'delete' => 'categoriarepuesto.eliminar',
            'search' => 'categoriarepuesto.buscar',
            'index'  => 'categoriarepuesto.index',
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
        $entidad          = 'Categoriarepuesto';

        $descripcion      = Libreria::getParam($request->input('descripcion'));

        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/
        $resultado        = Categoriarepuesto::where($filtro)->orderBy('descripcion', 'ASC');

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

    public function index()
    {
        $entidad          = 'Categoriarepuesto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta' ));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Categoriarepuesto';
        $categoriarepuesto = null;
        $formData = array('categoriarepuesto.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('categoriarepuesto', 'formData', 'entidad', 'boton', 'listar'));
    }


    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('descripcion' => 'required|max:100');
        $mensajes = array('descripcion.required'         => 'Debe ingresar una descripcion'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $Categoriarepuesto = new Categoriarepuesto();
            $Categoriarepuesto->descripcion = strtoupper($request->input('descripcion'));
            $Categoriarepuesto->save();
            event( new UserHasCreatedOrDeleted($Categoriarepuesto->id,'categoriarepuesto', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }



    public function show($id)
    {
        //
    }


    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'categoriarepuesto');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $categoriarepuesto = Categoriarepuesto::find($id);
        $entidad  = 'Categoriarepuesto';
        $formData = array('categoriarepuesto.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('categoriarepuesto', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'categoriarepuesto');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('descripcion' => 'required|max:100');
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripcion'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $Categoriarepuesto = Categoriarepuesto::find($id);

            $CategoriarepuestoCop = json_encode($Categoriarepuesto);

            $Categoriarepuesto->descripcion = strtoupper($request->input('descripcion'));
            $Categoriarepuesto->save();

            event(new UserHasEdited($CategoriarepuestoCop,$Categoriarepuesto,'categoriarepuesto',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'categoriarepuesto');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Categoriarepuesto = Categoriarepuesto::find($id);
            event( new UserHasCreatedOrDeleted($Categoriarepuesto->id,'Categoriarepuesto', auth()->user()->id,'eliminar'));
            $Categoriarepuesto->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'categoriarepuesto');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Categoriarepuesto::find($id);
        $entidad  = 'Categoriarepuesto';
        $formData = array('route' => array('categoriarepuesto.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
