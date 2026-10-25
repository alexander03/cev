<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Periodo;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class PeriodoController extends Controller
{
    protected $folderview      = 'app.periodo';
    protected $tituloAdmin     = 'Periodos';
    protected $tituloRegistrar = 'Registrar periodo';
    protected $tituloModificar = 'Modificar periodo';
    protected $tituloEliminar  = 'Eliminar periodo';
    protected $tituloActivar  = 'Activar periodo';
    protected $rutas           = array('create' => 'periodo.create', 
            'edit'   => 'periodo.edit', 
            'delete' => 'periodo.eliminar',
            'search' => 'periodo.buscar',
            'index'  => 'periodo.index',
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
        $entidad          = 'Periodo';
        $filter           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Periodo::where('nombre','like','%'.$filter.'%');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Periodo';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Periodo';
        $periodo = null;
        $formData = array('periodo.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('periodo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100|unique:periodo,nombre');
        $mensajes = array(
            'nombre.required' => 'Debe ingresar una descripcion',
            'nombre.unique' => 'Esta periodo ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $Periodo = new Periodo();
            $Periodo->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $Periodo->fechainicio = $request->input('fechainicio');
            $Periodo->fechafin = $request->input('fechafin');
            $Periodo->save();
            event( new UserHasCreatedOrDeleted($Periodo->id,'periodo', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $periodo = Periodo::find($id);
        $entidad  = 'Periodo';
        $formData = array('periodo.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('periodo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la periodo tiene la misma descripcion que la proporcionada
        $reglas = array('nombre' => ['required','max:100',Rule::unique('periodo')->ignore($id)]);

        $mensajes = array(
            'nombre.required'         => 'Debe ingresar una descripción',
            'nombre.unique' => 'Esta periodo ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $Periodo = Periodo::find($id);
            $PeriodoCop = json_encode($Periodo);

            $Periodo->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $Periodo->fechainicio = $request->input('fechainicio');
            $Periodo->fechafin = $request->input('fechafin');
            $Periodo->save();

            event(new UserHasEdited($PeriodoCop,$Periodo,'periodo',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Periodo::find($id);
        $entidad  = 'Periodo';
        $formData = array('route' => array('periodo.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Periodo = Periodo::find($id);
            event( new UserHasCreatedOrDeleted($Periodo->id,'periodo', auth()->user()->id,'eliminar'));
            $Periodo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

}
