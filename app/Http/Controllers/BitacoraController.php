<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bitacora;
use App\Librerias\Libreria;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class BitacoraController extends Controller
{
    protected $folderview      = 'app.bitacora';
    protected $tituloAdmin     = 'Bitacora';
    protected $tituloRegistrar = 'Registrar carroceria';
    protected $tituloModificar = 'Modificar carroceria';
    protected $tituloEliminar  = 'Eliminar carroceria';
    protected $rutas           = array('create' => 'bitacora.create', 
            'edit'   => 'carroceria.edit', 
            'delete' => 'carroceria.eliminar',
            'search' => 'bitacora.buscar',
            'index'  => 'bitacora.index',
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
        $entidad          = 'Bitacora';

        $descripcion      = Libreria::getParam($request->input('persona'));
        $tabla      = Libreria::getParam($request->input('tabla'));
        $accion      = Libreria::getParam($request->input('accion'));

        $filtro           = array();
        $filtro[]         = ['users.name', 'LIKE', '%'.strtoupper($descripcion).'%'];
        $filtro[]         = ['bitacora.tabla', 'LIKE', '%'.strtoupper($tabla).'%'];
        $filtro[]         = ['bitacora.accion', 'LIKE', '%'.strtoupper($accion).'%'];

        $resultado        = Bitacora::join('users','users.id','=','bitacora.user_id')
                                ->where($filtro)
                                ->where('bitacora.created_at','>=',$request->input('fechainicial'))
                                ->where('bitacora.created_at','<=',$request->input('fechafinal')." 23:59:59")
                                ->select('bitacora.*','users.name as persona')
                                ->orderBy('bitacora.created_at','desc');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tabla', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Accion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Registro', 'numero' => '2');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        
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
        $entidad          = 'Bitacora';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboAccion = array(''=>"Todos","crear"=>"Crear","Editar"=>"Editar","eliminar"=>"Eliminar");
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboAccion' ));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Bitacora';
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

            $carroceriaCop = $carroceria;

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
