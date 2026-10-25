<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Area;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class AreaController extends Controller
{
    protected $folderview      = 'app.areas';
    protected $tituloAdmin     = 'Areas';
    protected $tituloRegistrar = 'Registrar area';
    protected $tituloModificar = 'Modificar area';
    protected $tituloEliminar  = 'Eliminar area';
    protected $rutas           = array('create' => 'areas.create', 
            'edit'   => 'areas.edit', 
            'delete' => 'areas.eliminar',
            'search' => 'areas.buscar',
            'index'  => 'areas.index',
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
        $entidad          = 'Area';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = DB::table('area')->where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%')
        ->select('id','descripcion','areapadre_id as areapadre_id', 'areapadre_id as areapadre_descripcion','nivel','responsable','correo');
        $lista=$resultado->get();

        

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nivel', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Pertenece a...', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Responsable', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Correo', 'numero' => '1');
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

            $listapadres=array();
            foreach ($lista as $key => $value) {
                if($value->areapadre_id!=null){
               $listapadres[]= Area::find($value->areapadre_id)->descripcion;}
                else{
                $listapadres[]="Ninguno";}
            }

            return view($this->folderview.'.list')->with(compact('lista','listapadres', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Area';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Area';
        $area = null;
        $formData = array('areas.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        $areas = Area::where('nivel','<=','2')->orderBy('descripcion','asc')->get();
        $cboArea = array();
        $cboArea += array('0' => 'Selecione área');
        foreach($areas as $k=>$v){
            $cboArea += array($v->id=>$v->descripcion);
        }

        return view($this->folderview.'.mant')->with(compact('area', 'formData', 'entidad','cboArea', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('descripcion' => 'required|max:100',
                            'responsable' => 'required',
                            'correo' => 'required');
        $mensajes = array(
            'descripcion.required' => 'Debe ingresar una descripcion',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
            'responsable.required' => 'Debe ingresar un responsable',
            'correo.required' => 'Debe ingresar un correo'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $area = new Area();
            $area->descripcion= strtoupper($request->input('descripcion'));
            $area->responsable= strtoupper($request->input('responsable'));
            $area->correo= trim($request->input('correo'));
            if($request->input('areapadre_id')!=0){
                $area->areapadre_id= strtoupper($request->input('areapadre_id'));
                $area->nivel= (Area::find($request->input('areapadre_id'))->get())->nivel+1;
            }
            else{
                $area->nivel=1;
            }
            event( new UserHasCreatedOrDeleted($area->id,'area', auth()->user()->id,'crear'));
            $area->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
         $areas = Area::orderBy('descripcion','asc')->get();
        $cboArea = array();
        $cboArea += array('0' => 'Selecione área');
        foreach($areas as $k=>$v){
            if($v->id!=$id)
            $cboArea += array($v->id=>$v->descripcion);
        }
        $area = Area::find($id);
        $entidad  = 'Area';
        $formData = array('areas.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('area', 'formData','cboArea', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('descripcion' => 'required|max:100',       
                            'responsable' => 'required',
                            'correo' => 'required');
        $mensajes = array(
            'descripcion.required'         => 'Debe ingresar una descripción',
            'descripcion.max' => 'La descripcion debe tener max. 100 caracteres',
            'responsable.required' => 'Debe ingresar un responsable',
            'correo.required' => 'Debe ingresar un correo'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $area = Area::find($id);

            $areaCop = json_encode($area);

            $area->descripcion= strtoupper($request->input('descripcion'));
            $area->responsable= strtoupper($request->input('responsable'));
            $area->correo= trim($request->input('correo'));
            if($request->input('areapadre_id')!=0){
                $area->areapadre_id= strtoupper($request->input('areapadre_id'));
                $area->nivel= (Area::find($request->input('areapadre_id'))->get())->nivel+1;
            }
            else{
                $area->nivel=1;
                $area->areapadre_id=null;
            }
            $area->save();

            event(new UserHasEdited($areaCop,$area,'area',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $area = Area::find($id);
            event( new UserHasCreatedOrDeleted($area->id,'area', auth()->user()->id,'eliminar'));
            $area->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Area::find($id);
        $entidad  = 'Area';
        $formData = array('route' => array('areas.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
