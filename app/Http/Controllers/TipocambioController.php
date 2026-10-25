<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Tipocambio;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;


class TipocambioController extends Controller
{
    protected $folderview      = 'app.tipocambio';
    protected $tituloAdmin     = 'Tipo Cambio';
    protected $tituloRegistrar = 'Registrar tipo cambio';
    protected $tituloModificar = 'Modificar tipo cambio';
    protected $tituloEliminar  = 'Eliminar tipo cambio';
    protected $rutas           = array('create' => 'tipocambio.create', 
            'edit'   => 'tipocambio.edit', 
            'delete' => 'tipocambio.eliminar',
            'search' => 'tipocambio.buscar',
            'index'  => 'tipocambio.index',
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
        $entidad          = 'Tipocambio';

        $resultado        = Tipocambio::where('fecha','>=',$request->input('fechainicio'))
                                ->where('fecha','<=',$request->input('fechafin'))->orderBy('fecha', 'desc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio', 'numero' => '1');
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
        $entidad          = 'Tipocambio';
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
        $entidad  = 'Tipocambio';
        $tipocambio = null;
        $formData = array('tipocambio.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('tipocambio', 'formData', 'entidad', 'boton', 'listar'));
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
            'fecha' => 'required',
            'precio' => 'required',
        );
        $mensajes = array(
            'fecha.required'         => 'Debe ingresar una fecha',
            'precio.required'         => 'Debe ingresar un precio',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $precio2 = Tipocambio::where('fecha','>=',$request->input('fecha'))->first();
        /*if(!is_null($precio2)){
            $dat = array("fecha" => ["Precio ya registrado en la fecha"]);
            return json_encode($dat);
        }*/
        $error = DB::transaction(function() use($request){
            $obj = new Tipocambio();
            $obj->fecha = $request->input('fecha');
            $obj->precio = $request->input('precio');
            $obj->save();

            event( new UserHasCreatedOrDeleted($obj->id,'tipocambio', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'tipocambio');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $tipocambio = Tipocambio::find($id);
        $entidad  = 'Tipocambio';
        $formData = array('tipocambio.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('tipocambio', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'tipocambio');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'fecha' => 'required',
            'precio' => 'required',
        );
        $mensajes = array(
            'fecha.required'         => 'Debe ingresar una fecha',
            'precio.required'         => 'Debe ingresar un precio',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Tipocambio::find($id);
            
            $objCop = json_encode($obj);

            $obj->fecha = $request->input('fecha');
            $obj->precio = $request->input('precio');
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'tipocambio',auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'tipocambio');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Tipocambio::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'tipocambio', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'tipocambio');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Tipocambio::find($id);
        $entidad  = 'Tipocambio';
        $formData = array('route' => array('tipocambio.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
