<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Concesionaria;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConcesionariaExport;


class ConcesionariaController extends Controller
{
    protected $folderview      = 'app.concesionarias';
    protected $tituloAdmin     = 'Concesionarias';
    protected $tituloRegistrar = 'Registrar concesionaria';
    protected $tituloModificar = 'Modificar concesionaria';
    protected $tituloEliminar  = 'Eliminar concesionaria';
    protected $rutas           = array('create' => 'concesionarias.create', 
            'edit'   => 'concesionarias.edit', 
            'delete' => 'concesionarias.eliminar',
            'search' => 'concesionarias.buscar',
            'index'  => 'concesionarias.index',
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
        $entidad          = 'Concesionaria';
        $nombre           = Libreria::getParam($request->input('razonsocial'));
        $resultado        = Concesionaria::where('razonsocial', 'LIKE', '%'.strtoupper($nombre).'%')
                            ->orWhere('ruc', 'LIKE', '%'.strtoupper($nombre).'%')
                            ->orWhere('abreviatura', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('razonsocial', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'RUC', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Razon Social', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre Corto', 'numero' => '1');
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
        $entidad          = 'Concesionaria';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Concesionaria';
        $concesionaria = null;
        $formData = array('concesionarias.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('concesionaria', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('razonsocial' => 'required|max:100','ruc' => 'required|max:11|min:11','abreviatura' => 'required|max:15|min:1');
        $mensajes = array(
            'razonsocial.required' => 'Debe ingresar una razonsocial',
            'razonsocial.max' => 'La razonsocial debe tener max. 100 caracteres',
            'ruc.required' => 'Debe ingresar un RUC',
            'abreviatura.required' => 'Abreviatura debe tener entre 1 y 15 caracteres',
            'ruc.max' => 'El RUC debe tener 11 dígitos',
            'ruc.min' => 'El RUC debe tener 11 dígitos'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $concesionaria = new Concesionaria();
            $concesionaria->razonsocial= strtoupper($request->input('razonsocial'));
            $concesionaria->ruc= strtoupper($request->input('ruc'));
            $concesionaria->abreviatura= strtoupper($request->input('abreviatura'));
            $concesionaria->save();
            event( new UserHasCreatedOrDeleted($concesionaria->id,'concesionaria', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'concesionaria');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $concesionaria = Concesionaria::find($id);
        $entidad  = 'Concesionaria';
        $formData = array('concesionarias.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('concesionaria', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'concesionaria');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('razonsocial' => 'required|max:100','ruc' => 'required|max:11|min:11','abreviatura' => 'required|max:15|min:1');
        $mensajes = array(
            'razonsocial.required' => 'Debe ingresar una razonsocial',
            'razonsocial.max' => 'La razonsocial debe tener max. 100 caracteres',
            'ruc.required' => 'Debe ingresar un RUC',
            'abreviatura.required' => 'Abreviatura debe tener entre 1 y 15 caracteres',
            'ruc.max' => 'El RUC debe tener 11 dígitos',
            'ruc.min' => 'El RUC debe tener 11 dígitos'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $concesionaria = Concesionaria::find($id);

            $concesionariaCop = json_encode($concesionaria);

            $concesionaria->razonsocial= strtoupper($request->input('razonsocial'));
            $concesionaria->ruc= strtoupper($request->input('ruc'));
            $concesionaria->abreviatura= strtoupper($request->input('abreviatura'));
            $concesionaria->save();

            event(new UserHasEdited($concesionariaCop,$concesionaria,'concesionaria',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'concesionaria');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $concesionaria = Concesionaria::find($id);
            event( new UserHasCreatedOrDeleted($concesionaria->id,'concesionaria', auth()->user()->id,'eliminar'));
            $concesionaria->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'concesionaria');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Concesionaria::find($id);
        $entidad  = 'Concesionaria';
        $formData = array('route' => array('concesionarias.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    
    public function exportExcel(){

        return Excel::download(new ConcesionariaExport, 'concesionaria.xlsx');
    }
}