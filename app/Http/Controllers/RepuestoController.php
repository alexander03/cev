<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Repuesto;
use App\Categoriarepuesto;
use App\Unidad;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class RepuestoController extends Controller
{
    protected $folderview      = 'app.repuestos';
    protected $tituloAdmin     = 'Repuestos';
    protected $tituloRegistrar = 'Registrar repuesto';
    protected $tituloModificar = 'Modificar repuesto';
    protected $tituloEliminar  = 'Eliminar repuesto';
    protected $tituloActivar  = 'Activar repuesto';
    protected $rutas           = array('create' => 'repuestos.create', 
            'edit'   => 'repuestos.edit', 
            'delete' => 'repuestos.eliminar',
            'activar' => 'repuestos.activar',
            'search' => 'repuestos.buscar',
            'index'  => 'repuestos.index',
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
        $entidad          = 'Repuesto';
        $filter           = Libreria::getParam($request->input('filter'));
        $estado           = $request->input('estado');
        $unidad           = $request->input('unidad');
        $categoria        = $request->input('categoria');
        $resultado        = Repuesto::getFilter($estado, $filter, $unidad, $categoria);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Unidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Categoria', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        }

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_activar  = $this->tituloActivar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Repuesto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $arrUnidades      = Unidad::getAll();
        $cboUnidades = array('all' => 'TODOS');
        foreach($arrUnidades as $k=>$v){
            $cboUnidades += array($v->id=>$v->descripcion);
        }
        $cboCategoria = array('all'=>'TODOS') + Categoriarepuesto::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'cboUnidades', 'ruta','cboCategoria'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Repuesto';
        $repuesto = null;
        $formData = array('repuestos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        $arrUnidades = Unidad::getAll();
        $cboUnidades = array('' => 'Seleccione');
        foreach($arrUnidades as $k=>$v){
            $cboUnidades += array($v->id=>$v->descripcion);
        }
        $cboCategoria = array(''=>'Seleccione') + Categoriarepuesto::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('repuesto', 'formData', 'entidad', 'boton', 'cboUnidades', 'listar','cboCategoria'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'codigo' => 'required|digits:7|unique:repuesto,codigo',
            'descripcion' => 'required|max:100|unique:repuesto,descripcion',
            'unidad_id' => 'required',
            'categoriarepuesto_id' => 'required'
        );
        $mensajes = array(
            'codigo.required' => 'Debe ingresar un código',
            'codigo.unique' => 'Este código ya existe',
            'codigo.digits' => 'El código debe tener 7 cifras',
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.unique' => 'Esta descripción ya existe',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
            'unidad_id.required' => 'Debe seleccionar una unidad',
            'categoriarepuesto_id.required' => 'Debe seleccionar una categoria'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $repuesto = new Repuesto();
            $repuesto->codigo= mb_strtoupper($request->input('codigo'), 'utf-8');
            $repuesto->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $repuesto->unidad_id= $request->input('unidad_id');
            $repuesto->categoriarepuesto_id= $request->input('categoriarepuesto_id');
            $repuesto->save();

            event( new UserHasCreatedOrDeleted($repuesto->id,'repuesto', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'repuesto');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $repuesto = Repuesto::find($id);
        $entidad  = 'Repuesto';
        $formData = array('repuestos.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $arrUnidades = Unidad::getAll();
        $cboUnidades = array('' => 'Seleccione');
        foreach($arrUnidades as $k=>$v){
            $cboUnidades += array($v->id=>$v->descripcion);
        }
        $cboCategoria = array(''=>'Seleccione') + Categoriarepuesto::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('repuesto', 'formData', 'entidad', 'boton', 'cboUnidades', 'listar','cboCategoria'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'repuesto');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'unidad_id' => 'required',
            'codigo' => ['required', 'digits:7',Rule::unique('repuesto')->ignore($id)],
            'descripcion' => ['required','max:100',Rule::unique('repuesto')->ignore($id)],
            'categoriarepuesto_id' => 'required'
        );
        $mensajes = array(
            'codigo.required' => 'Debe ingresar un código',
            'codigo.digits' => 'El código debe tener 7 cifras',
            'codigo.unique' => 'El código ya existe',
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
            'descripcion.unique' => 'La descripción ya existe',
            'unidad_id.required' => 'Debe seleccionar una unidad',
            'categoriarepuesto_id.required' => 'Debe seleccionar una categoria'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $repuesto = Repuesto::find($id);

            $repuestoOrg = json_encode($repuesto);

            $repuesto->codigo= mb_strtoupper($request->input('codigo'), 'utf-8');
            $repuesto->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $repuesto->unidad_id= $request->input('unidad_id');
            $repuesto->categoriarepuesto_id= $request->input('categoriarepuesto_id');
            $repuesto->save();

            event( new UserHasEdited($repuestoOrg,$repuesto,'repuesto', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'repuesto');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Repuesto::find($id);
        $entidad  = 'Repuesto';
        $formData = array('route' => array('repuestos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'repuesto');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $repuesto = Repuesto::find($id);

            event( new UserHasCreatedOrDeleted($repuesto->id,'repuesto', auth()->user()->id,'eliminar'));

            $repuesto->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego){
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Repuesto::find($id);
        $entidad  = 'Repuesto';
        $formData = array('route' => array('respuestos.reactivar', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reactivar($id){
        $error = DB::transaction(function() use($id){
            Repuesto::onlyTrashed()->where('id', $id)->restore();
        });
        return is_null($error) ? "OK" : $error;
    }
}