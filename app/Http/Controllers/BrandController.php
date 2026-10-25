<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Brand;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class BrandController extends Controller
{
    protected $folderview      = 'app.brands';
    protected $tituloAdmin     = 'Marcas';
    protected $tituloRegistrar = 'Registrar marca';
    protected $tituloModificar = 'Modificar marca';
    protected $tituloEliminar  = 'Eliminar marca';
    protected $tituloActivar  = 'Activar marca';
    protected $rutas           = array('create' => 'marcas.create', 
            'edit'   => 'marcas.edit', 
            'delete' => 'marcas.eliminar',
            'activar' => 'marcas.activar',
            'search' => 'marcas.buscar',
            'index'  => 'marcas.index',
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
        $entidad          = 'Brand';
        $estado           = $request->input('estado');
        $filter           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Brand::getFilter($estado, $filter);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Brand';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Brand';
        $brand = null;
        $formData = array('marcas.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('brand', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('descripcion' => 'required|max:100|unique:marca,descripcion');
        $mensajes = array(
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.unique' => 'Esta marca ya existe',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $brand = new Brand();
            $brand->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $brand->save();
            event( new UserHasCreatedOrDeleted($brand->id,'marca', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $brand = Brand::find($id);
        $entidad  = 'Brand';
        $formData = array('marcas.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('brand', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la marca tiene la misma descripcion que la proporcionada
        $reglas = array('descripcion' => ['required','max:100',Rule::unique('marca')->ignore($id)]);

        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.unique' => 'Esta marca ya existe',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $brand = Brand::find($id);
            $brandCop = json_encode($brand);

            $brand->descripcion= mb_strtoupper($request->input('descripcion'), 'utf-8');
            $brand->save();

            event(new UserHasEdited($brandCop,$brand,'marca',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Brand::find($id);
        $entidad  = 'Brand';
        $formData = array('route' => array('marcas.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'marca');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $brand = Brand::find($id);
            event( new UserHasCreatedOrDeleted($brand->id,'marca', auth()->user()->id,'eliminar'));
            $brand->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego){
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Brand::find($id);
        $entidad  = 'Brand';
        $formData = array('route' => array('marcas.reactivar', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reactivar($id){
        $error = DB::transaction(function() use($id){
            Brand::onlyTrashed()->where('id', $id)->restore();
        });
        return is_null($error) ? "OK" : $error;
    }
}
