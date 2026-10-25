<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Horometro;
use App\Grifo;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class HorometroController extends Controller
{
    protected $folderview      = 'app.horometro';
    protected $tituloAdmin     = 'Horometro';
    protected $tituloRegistrar = 'Registrar horometro';
    protected $tituloModificar = 'Modificar horometro';
    protected $tituloEliminar  = 'Eliminar horometro';
    protected $rutas           = array('create' => 'horometro.create', 
            'edit'   => 'horometro.edit', 
            'delete' => 'horometro.eliminar',
            'search' => 'horometro.buscar',
            'index'  => 'horometro.index',
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
        $entidad          = 'Horometro';
        $fechainicio      = $request->input('fechainicio');
        $fechafin         = $request->input('fechafin');
        $grifo            = $request->input('grifo');
        $resultado        = Horometro::getFilter($fechainicio, $fechafin, $grifo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Grifo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha/Hora Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $usuario = auth()->user();
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','usuario'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','usuario'));
    }

    public function index()
    {
        $entidad          = 'Horometro';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboGrifo = array("0" => "Todos") + Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboGrifo'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Horometro';
        $horometro = null;
        $formData = array('horometro.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboGrifo = Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('horometro', 'formData', 'entidad', 'boton', 'listar','cboGrifo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('fecha' => 'required|unique:horometro,fecha,NULL,id,deleted_at,NULL,grifo_id,NULL',
                        'inicio' => 'required',
                        'fin' => 'required'
                    );
        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            'fecha.unique' => 'Esta fecha ya existe',
            'inicio.required' => 'Debe registrar un inicio',
            'fin.required' => 'Debe registrar un fin'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $horo = Horometro::where('fecha','<',$request->input('fecha'))->where('grifo_id','=',$request->input('grifo_id'))->orderBy('fecha','desc')->first();
        if(!is_null($horo) && ($request->input('inicio')+0)!=($horo->fin+0)){
            $dat = array("inicio" => ["Horometro inicio es diferente al ultimo registrado, el ultimo horometro es:  ".$horo->fin." del ".$horo->fecha],"id"=>$horo->id);
            return json_encode($dat);
        }
        $error = DB::transaction(function() use($request){
            $obj = new Horometro();
            $obj->fecha= $request->input('fecha');
            $obj->inicio= $request->input('inicio');
            $obj->fin= $request->input('fin');
            $obj->grifo_id= $request->input('grifo_id');
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'horometro', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'horometro');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $horometro = Horometro::find($id);
        $entidad  = 'Horometro';
        $formData = array('horometro.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboGrifo = Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('horometro', 'formData', 'entidad', 'boton', 'listar','cboGrifo'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'horometro');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array('fecha' => ['required',Rule::unique('horometro','grifo_id')->ignore($id)],
                        'inicio' => 'required',
                        'fin' => 'required'
                    );

        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            'fecha.unique' => 'Esta fecha ya existe',
            'inicio.required' => 'Debe registrar un inicio',
            'fin.required' => 'Debe registrar un fin'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Horometro::find($id);
            $objCop = json_encode($obj);

            $obj->fecha= $request->input('fecha');
            $obj->inicio= $request->input('inicio');
            $obj->fin= $request->input('fin');
            $obj->grifo_id= $request->input('grifo_id');
            $obj->save();

            event(new UserHasEdited($objCop,$obj,'horometro',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'horometro');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Horometro::find($id);
        $entidad  = 'Horometro';
        $formData = array('route' => array('horometro.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'horometro');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Horometro::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'horometro', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

}
