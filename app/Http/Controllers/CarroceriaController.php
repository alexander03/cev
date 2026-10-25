<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carroceria;
use App\Librerias\Libreria;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class CarroceriaController extends Controller
{
    protected $folderview      = 'app.carroceria';
    protected $tituloAdmin     = 'Carrocerías';
    protected $tituloRegistrar = 'Registrar carroceria';
    protected $tituloModificar = 'Modificar carroceria';
    protected $tituloEliminar  = 'Eliminar carroceria';
    protected $rutas           = array('create' => 'carroceria.create', 
            'edit'   => 'carroceria.edit', 
            'delete' => 'carroceria.eliminar',
            'search' => 'carroceria.buscar',
            'index'  => 'carroceria.index',
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
        $entidad          = 'Carroceria';

        $descripcion      = Libreria::getParam($request->input('descripcion'));

        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/
        $resultado        = Carroceria::where($filtro)->orderBy('descripcion', 'ASC');

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
        $entidad          = 'Carroceria';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta' ));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Carroceria';
        $carroceria = null;
        $formData = array('carroceria.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('carroceria', 'formData', 'entidad', 'boton', 'listar'));
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
            $carroceria = new Carroceria();
            $carroceria->descripcion = strtoupper($request->input('descripcion'));
            $carroceria->save();
            event( new UserHasCreatedOrDeleted($carroceria->id,'carroceria', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }



    public function show($id)
    {
        //
    }


    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'carroceria');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $carroceria = Carroceria::find($id);
        $entidad  = 'Carroceria';
        $formData = array('carroceria.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('carroceria', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'carroceria');
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
            $carroceria = Carroceria::find($id);

            $carroceriaCop = json_encode($carroceria);

            $carroceria->descripcion = strtoupper($request->input('descripcion'));
            $carroceria->save();

            event(new UserHasEdited($carroceriaCop,$carroceria,'carroceria',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'carroceria');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $carroceria = Carroceria::find($id);
            event( new UserHasCreatedOrDeleted($carroceria->id,'carroceria', auth()->user()->id,'eliminar'));
            $carroceria->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'carroceria');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Carroceria::find($id);
        $entidad  = 'Carroceria';
        $formData = array('route' => array('carroceria.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
