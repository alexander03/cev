<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Lugar;
use App\Garita;
use App\Concesionaria;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class LugarController extends Controller
{
    protected $folderview      = 'app.lugar';
    protected $tituloAdmin     = 'Lugar';
    protected $tituloRegistrar = 'Registrar lugar';
    protected $tituloModificar = 'Modificar lugar';
    protected $tituloEliminar  = 'Eliminar lugar';
    protected $tituloActivar  = 'Activar lugar';
    protected $rutas           = array('create' => 'lugar.create', 
            'edit'   => 'lugar.edit', 
            'delete' => 'lugar.eliminar',
            'search' => 'lugar.buscar',
            'index'  => 'lugar.index',
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
        $entidad          = 'Lugar';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('nombre'));
        $resultado        = Lugar::where('nombre','like', '%'.$filter.'%')->orderBy('nombre','asc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Garita', 'numero' => '1');
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
        $entidad          = 'Lugar';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Lugar';
        $lugar = null;
        $formData = array('lugar.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboGarita = Garita::orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('lugar', 'formData', 'entidad', 'boton', 'listar','cboGarita'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100|unique:lugar,nombre');
        $mensajes = array(
            'nombre.required' => 'Debe ingresar una descripcion',
            'nombre.unique' => 'Esta lugar ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $obj = new Lugar();
            $obj->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            if($request->input('garita_id')!=""){
                $obj->garita_id = json_encode($request->input('garita_id'));
            }
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'lugar', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'lugar');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $lugar = Lugar::find($id);
        $entidad  = 'Lugar';
        $formData = array('lugar.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboGarita = Garita::orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('lugar', 'formData', 'entidad', 'boton', 'listar','cboGarita'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'lugar');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas = array('nombre' => ['required','max:100',Rule::unique('lugar')->ignore($id)]);

        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.unique' => 'Esta lugar ya existe',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Lugar::find($id);
            $objCop = json_encode($obj);

            $obj->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            if($request->input('garita_id')!=""){
                $obj->garita_id = json_encode($request->input('garita_id'));
            }
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'lugar',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'lugar');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Lugar::find($id);
        $entidad  = 'Lugar';
        $formData = array('route' => array('lugar.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'lugar');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Lugar::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'lugar', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
