<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Opcionmenu;
use App\Grupomenu;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class OpcionmenuController extends Controller
{

    protected $folderview      = 'app.opcionmenu';
    protected $tituloAdmin     = 'Opcion Menu';
    protected $tituloRegistrar = 'Registrar opcion menu';
    protected $tituloModificar = 'Modificar opcion menu';
    protected $tituloEliminar  = 'Eliminar opcion menu';
    protected $rutas           = array('create' => 'opcionmenu.create', 
            'edit'   => 'opcionmenu.edit', 
            'delete' => 'opcionmenu.eliminar',
            'search' => 'opcionmenu.buscar',
            'index'  => 'opcionmenu.index',
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
        $entidad          = 'Opcionmenu';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Opcionmenu::where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%');
        if($request->input('grupomenu')!="0"){
            $resultado = $resultado->where('grupomenu_id','=',$request->input('grupomenu'));
        }
        $lista            = $resultado->orderBy('orden','asc')->orderBy('descripcion', 'ASC')->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Grupo Menu', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Link', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Icono', 'numero' => '1');
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
        $entidad          = 'Opcionmenu';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $grupomenu = Grupomenu::orderBy('descripcion','asc')->get();
        $cboGrupo = array("0"=>"--Todos--");
        foreach($grupomenu as $k=>$v){
            $cboGrupo += array($v->id=>$v->descripcion);
        }
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboGrupo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Opcionmenu';
        $grupomenu = Grupomenu::orderBy('descripcion','asc')->get();
        $cboGrupo = array();
        foreach($grupomenu as $k=>$v){
            $cboGrupo += array($v->id=>$v->descripcion);
        }
        $opcionmenu = null;
        $formData = array('opcionmenu.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('opcionmenu', 'formData', 'entidad', 'boton', 'listar', 'cboGrupo'));
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
        $reglas     = array('descripcion' => 'required|max:50');
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripcion'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $opcionmenu = new Opcionmenu();
            $opcionmenu->descripcion= strtoupper($request->input('descripcion'));
            $opcionmenu->icono = $request->input('icono');
            $opcionmenu->orden = $request->input('orden');
            $opcionmenu->link = $request->input('link');
            $opcionmenu->grupomenu_id = $request->input('grupomenu_id');
            $opcionmenu->save();

            event( new UserHasCreatedOrDeleted($opcionmenu->id,'opcionmenu', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'opcionmenu');
        if ($existe !== true) {
            return $existe;
        }
        $grupomenu = Grupomenu::orderBy('descripcion','asc')->get();
        $cboGrupo = array();
        foreach($grupomenu as $k=>$v){
            $cboGrupo += array($v->id=>$v->descripcion);
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $opcionmenu = Opcionmenu::find($id);
        $entidad  = 'Opcionmenu';
        $formData = array('opcionmenu.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('opcionmenu', 'formData', 'entidad', 'boton', 'listar', 'cboGrupo'));
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
        $existe = Libreria::verificarExistencia($id, 'opcionmenu');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('descripcion' => 'required|max:50');
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar un nombre'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $opcionmenu = Opcionmenu::find($id);

            $opcionmenuOrg = json_encode($opcionmenu);

            $opcionmenu->descripcion= strtoupper($request->input('descripcion'));
            $opcionmenu->icono = $request->input('icono');
            $opcionmenu->orden = $request->input('orden');
            $opcionmenu->link = $request->input('link');
            $opcionmenu->grupomenu_id = $request->input('grupomenu_id');
            $opcionmenu->save();
        
            event( new UserHasEdited($opcionmenuOrg,$opcionmenu,'opcionmenu', auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'opcionmenu');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $opcionmenu = Opcionmenu::find($id);
            event( new UserHasCreatedOrDeleted($opcionmenu->id,'opcionmenu', auth()->user()->id,'eliminar'));
            $opcionmenu->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'opcionmenu');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Opcionmenu::find($id);
        $entidad  = 'Opcionmenu';
        $formData = array('route' => array('opcionmenu.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
}
