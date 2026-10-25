<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Abastecimiento;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;


class AbastecimientoController extends Controller
{
    protected $folderview      = 'app.abastecimiento';
    protected $tituloAdmin     = 'Abastecimiento';
    protected $tituloRegistrar = 'Registrar abastecimiento';
    protected $tituloModificar = 'Modificar abastecimiento';
    protected $tituloEliminar  = 'Eliminar abastecimiento';
    protected $rutas           = array('create' => 'abastecimientolugar.create', 
            'edit'   => 'abastecimientolugar.edit', 
            'delete' => 'abastecimientolugar.eliminar',
            'search' => 'abastecimientolugar.buscar',
            'index'  => 'abastecimientolugar.index',
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
        $entidad          = 'Abastecimiento';
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        
        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];


        $resultado        = Abastecimiento::where($filtro)->orderBy('descripcion', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
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
        $entidad          = 'Abastecimiento';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Abastecimiento';
        $abastecimiento = null;
        $formData = array('abastecimientolugar.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('abastecimiento', 'formData', 'entidad', 'boton', 'listar'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'descripcion' => 'required|max:45'
        );
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max'              => 'La descripción supera los 45 caracteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $abastecimiento = new Abastecimiento();
            $abastecimiento->descripcion = strtoupper($request->input('descripcion'));
            $abastecimiento->save();
            event( new UserHasCreatedOrDeleted($abastecimiento->id,'abastecimiento', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'abastecimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $abastecimiento = Abastecimiento::find($id);
        $entidad  = 'Abastecimiento';
        $formData = array('abastecimientolugar.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('abastecimiento', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'abastecimiento');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'descripcion' => 'required|max:45'
        );
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max'              => 'La descripción supera los 45 caracteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $abastecimiento = Abastecimiento::find($id);

            $abastecimientoCop = json_encode($abastecimiento);

            $abastecimiento->descripcion = strtoupper($request->input('descripcion'));
            $abastecimiento->save();

            event(new UserHasEdited($abastecimientoCop,$abastecimiento,'abastecimiento',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'abastecimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $abastecimiento = Abastecimiento::find($id);
            event( new UserHasCreatedOrDeleted($abastecimiento->id,'abastecimiento', auth()->user()->id,'eliminar'));
            $abastecimiento->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Abastecimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Abastecimiento::find($id);
        $entidad  = 'Abastecimiento';
        $formData = array('route' => array('abastecimientolugar.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
