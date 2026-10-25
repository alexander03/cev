<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Piepagina;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class PiepaginaController extends Controller
{
    protected $folderview      = 'app.piepagina';
    protected $tituloAdmin     = 'Pie de Pagina';
    protected $tituloRegistrar = 'Registrar Pie de Pagina';
    protected $tituloModificar = 'Modificar Pie de Pagina';
    protected $tituloEliminar  = 'Eliminar Pie de Pagina';
    protected $rutas           = array('create' => 'piepagina.create', 
            'edit'   => 'piepagina.edit', 
            'delete' => 'piepagina.eliminar',
            'search' => 'piepagina.buscar',
            'index'  => 'piepagina.index',
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
        $entidad          = 'Piepagina';
        $nombre             = Libreria::getParam($request->input('nombre'));
        $resultado        = Piepagina::where('nombre', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('formato','asc')->orderBy('orden', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cargo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Formato', 'numero' => '1');
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
        $entidad          = 'Piepagina';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $cboFormato = array('Combustible'=>'Combustible','Medicion Equipo Detallado'=>'Medicion Equipo Detallado','Medicion Equipo Consolidado'=>'Medicion Equipo Consolidado','Medicion Vehiculo'=>'Medicion Vehiculo');
        $entidad  = 'Piepagina';
        $piepagina = null;
        $formData = array('piepagina.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('piepagina', 'formData', 'entidad', 'boton', 'listar', 'cboFormato'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100');
        $mensajes = array(
            'nombre.required' => 'Debe ingresar un nombre',
            'nombre.max' => 'El nombre debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $piepagina = new Piepagina();
            $piepagina->cargo= strtoupper($request->input('cargo'));
            $piepagina->nombre= strtoupper($request->input('nombre'));
            $piepagina->orden= $request->input('orden');
            $piepagina->formato= $request->input('formato');
            $piepagina->save();

            event( new UserHasCreatedOrDeleted($piepagina->id,'piepagina', auth()->user()->id,'crear'));

        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'piepagina');
        if ($existe !== true) {
            return $existe;
        }
        $cboFormato = array('Combustible'=>'Combustible','Medicion Equipo Detallado'=>'Medicion Equipo Detallado','Medicion Equipo Consolidado'=>'Medicion Equipo Consolidado','Medicion Vehiculo'=>'Medicion Vehiculo');
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $piepagina = Piepagina::find($id);
        $entidad  = 'Piepagina';
        $formData = array('piepagina.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('piepagina', 'formData', 'entidad', 'boton', 'listar', 'cboFormato'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'piepagina');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('nombre' => 'required|max:100');
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre',
            'nombre.max' => 'El nombre debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $piepagina = Piepagina::find($id);

            $piepaginaOrg = json_encode($piepagina);

            $piepagina->cargo= strtoupper($request->input('cargo'));
            $piepagina->nombre= strtoupper($request->input('nombre'));
            $piepagina->orden= $request->input('orden');
            $piepagina->formato= $request->input('formato');
            $piepagina->save();


            event( new UserHasEdited($piepaginaOrg,$piepagina,'piepagina', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'piepagina');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $piepagina = Piepagina::find($id);
            event( new UserHasCreatedOrDeleted($piepagina->id,'piepagina', auth()->user()->id,'eliminar'));
            $piepagina->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'piepagina');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Piepagina::find($id);
        $entidad  = 'Piepagina';
        $formData = array('route' => array('piepagina.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
