<?php

namespace App\Http\Controllers;

use Validator;
use App\Mensaje;
use App\Tipouser;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class MensajeController extends Controller
{
   
    protected $folderview      = 'app.mensaje';
    protected $tituloAdmin     = 'Mensaje';
    protected $tituloRegistrar = 'Registrar mensaje';
    protected $tituloModificar = 'Modificar mensaje';
    protected $tituloEliminar  = 'Eliminar mensaje';
    protected $rutas           = array('create' => 'mensaje.create', 
            'edit'   => 'mensaje.edit', 
            'delete' => 'mensaje.eliminar',
            'search' => 'mensaje.buscar',
            'index'  => 'mensaje.index',
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
        $entidad          = 'Mensaje';
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        $fechainicio      = $request->input('fechainicio');
        $fechafin      = $request->input('fechafin');
        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];


        $resultado        = Mensaje::where($filtro)
                            ->where(function($sql) use($fechainicio){
                                if($fechainicio!=""){
                                    $sql = $sql->where('fechainicio','>=',$fechainicio);
                                }
                            })
                            ->where(function($sql) use($fechafin){
                                if($fechafin!=""){
                                    $sql = $sql->where('fechainicio','<=',$fechafin);
                                }
                            })
                            ->orderBy('fechainicio', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Usuario', 'numero' => '1');
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Mensaje';
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
        $entidad  = 'Mensaje';
        $mensaje = null;
        $formData = array('mensaje.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboTipouser = Tipouser::orderBy('descripcion','asc')->pluck('descripcion','id');
        return view($this->folderview.'.mant')->with(compact('mensaje', 'formData', 'entidad', 'boton', 'listar','cboTipouser'));
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
            'descripcion' => 'required|max:1000',
            'fechainicio' => 'required',
            'fechafin' => 'required',
        );
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max'              => 'La descripción supera los 1000 caracteres',
            'fechainicio.required'           => 'Debe ingresar una fecha de inicio',
            'fechafin.required'           => 'Debe ingresar una fecha de fin',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $obj = new Mensaje();
            $obj->descripcion = strtoupper($request->input('descripcion'));
            $obj->fechainicio = $request->input('fechainicio');
            $obj->fechafin = $request->input('fechafin');
            $obj->tipouser_id = $request->input('tipouser_id');
            $obj->save();

            event( new UserHasCreatedOrDeleted($obj->id,'mensaje', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'mensaje');
        if ($existe !== true) {
            return $existe;
        }
        
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $mensaje = Mensaje::find($id);
        $entidad  = 'Mensaje';
        $formData = array('mensaje.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboTipouser = Tipouser::orderBy('descripcion','asc')->pluck('descripcion','id');
        return view($this->folderview.'.mant')->with(compact('mensaje', 'formData' ,'entidad', 'boton', 'listar', 'cboTipouser'));
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
        $existe = Libreria::verificarExistencia($id, 'mensaje');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'descripcion' => 'required|max:1000',
            'fechainicio' => 'required',
            'fechafin' => 'required',
        );
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max'              => 'La descripción supera los 1000 caracteres',
            'fechainicio.required'           => 'Debe ingresar una fecha de inicio',
            'fechafin.required'           => 'Debe ingresar una fecha de fin',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Mensaje::find($id);

            $objOrg = json_encode($obj);

            $obj->descripcion = strtoupper($request->input('descripcion'));
            $obj->fechainicio = $request->input('fechainicio');
            $obj->fechafin = $request->input('fechafin');
            $obj->tipouser_id = $request->input('tipouser_id');
            $obj->save();


            event( new UserHasEdited($objOrg,$obj,'mensaje', auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'mensaje');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Mensaje::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'mensaje', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'mensaje');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Mensaje::find($id);
        $entidad  = 'Mensaje';
        $formData = array('route' => array('mensaje.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

}
