<?php

namespace App\Http\Controllers;

use Validator;
use App\Tipohora;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class TipohoraController extends Controller
{
    protected $folderview      = 'app.tipohora';
    protected $tituloAdmin     = 'Tipo de Horas Paradas';
    protected $tituloRegistrar = 'Registrar tipo de horas paradas';
    protected $tituloModificar = 'Modificar tipo de horas paradas';
    protected $tituloEliminar  = 'Eliminar tipo de horas paradas';
    protected $rutas           = array('create' => 'tipohora.create', 
            'edit'   => 'tipohora.edit', 
            'delete' => 'tipohora.eliminar',
            'search' => 'tipohora.buscar',
            'index'  => 'tipohora.index',
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

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Tipohora';
        $codigo           = Libreria::getParam($request->input('codigo'));
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        
        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];

/*        if($codigo){
            $filtro[]        = ['codigo', '=', $codigo];
        }*/

        $resultado        = Tipohora::where($filtro)->orderBy('descripcion', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
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
        $entidad          = 'Tipohora';
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
        $entidad  = 'Tipohora';
        $tipohora = null;
        $formData = array('tipohora.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboTipo = array("Abastecimiento"=>"Abastecimiento","Refrigerio"=>"Refrigerio","Horas de Mantenimiento"=>"Horas de Mantenimiento","Horas Paradas"=>"Horas Paradas","Horas Trabajadas"=>"Horas Trabajadas","Fuera de Obra"=>"Fuera de Obra","Falta Equipo Auxiliar(D)"=>"Falta Equipo Auxiliar(D)");
        return view($this->folderview.'.mant')->with(compact('tipohora', 'formData', 'entidad', 'boton', 'listar','cboTipo'));
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
        $reglas     = array('codigo' => 'required|max:2' ,'descripcion' => 'required|max:50');
        $mensajes = array(
            'codigo.required'         => 'Debe ingresar una codigo',
            'codigo.max'              => 'El codigo supera los 2 digitos',
            'descripcion.required'         => 'Debe ingresar una descripcion'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $tipohora = new Tipohora();
            $tipohora->codigo = $request->input('codigo');
            $tipohora->prioridad = ($request->input('prioridad')) ? true : false;
            $tipohora->descripcion = strtoupper($request->input('descripcion'));
            $tipohora->tipo = $request->input('tipo');
            $tipohora->save();

            event( new UserHasCreatedOrDeleted($tipohora->id,'tipohora', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'tipohora');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $tipohora = Tipohora::find($id);
        $entidad  = 'Tipohora';
        $formData = array('tipohora.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboTipo = array("Abastecimiento"=>"Abastecimiento","Refrigerio"=>"Refrigerio","Horas de Mantenimiento"=>"Horas de Mantenimiento","Horas Paradas"=>"Horas Paradas","Horas Trabajadas"=>"Horas Trabajadas","Fuera de Obra"=>"Fuera de Obra","Falta Equipo Auxiliar(D)"=>"Falta Equipo Auxiliar(D)");
        return view($this->folderview.'.mant')->with(compact('tipohora', 'formData', 'entidad', 'boton', 'listar','cboTipo'));
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
        $existe = Libreria::verificarExistencia($id, 'tipohora');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('codigo' => 'required|max:2' ,'descripcion' => 'required|max:50');
        $mensajes = array(
            'codigo.required'         => 'Debe ingresar una codigo',
            'codigo.max'              => 'El codigo supera los 2 digitos',
            'descripcion.required'         => 'Debe ingresar una descripcion'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $tipohora = Tipohora::find($id);

            $tipohoraOrg = json_encode($tipohora);

            $tipohora->codigo = $request->input('codigo');
            $tipohora->prioridad = ($request->input('prioridad')) ? true : false;
            $tipohora->descripcion = strtoupper($request->input('descripcion'));
            $tipohora->tipo = $request->input('tipo');
            $tipohora->save();

            event( new UserHasEdited($tipohoraOrg,$tipohora,'tipohora', auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'tipohora');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $tipohora = Tipohora::find($id);

            event( new UserHasCreatedOrDeleted($tipohora->id,'tipohora', auth()->user()->id,'eliminar'));

            $tipohora->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'tipohora');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Tipohora::find($id);
        $entidad  = 'Tipohora';
        $formData = array('route' => array('tipohora.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
