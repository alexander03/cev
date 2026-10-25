<?php

namespace App\Http\Controllers;

use App\Librerias\Libreria;
use App\Opcionmenu;
use App\Propietario;
use App\Rules\SearchUaPadre;
use App\Rules\SelectDifZero;
use App\Ua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class PropietarioController extends Controller
{
    
    protected $folderview      = 'app.propietario';
    protected $tituloAdmin     = 'Propietarios';
    protected $tituloRegistrar = 'Registrar propietario';
    protected $tituloModificar = 'Modificar propietario';
    protected $tituloEliminar  = 'Eliminar propietario';
    protected $rutas           = array('create' => 'propietario.create', 
            'edit'   => 'propietario.edit', 
            'delete' => 'propietario.eliminar',
            'search' => 'propietario.buscar',
            'index'  => 'propietario.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Propietario';
        $nombre             = Libreria::getParam($request->input('descripcion'));
        $resultado        = Propietario::where('descripcion', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('descripcion', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de llegada', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de salida', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de contrato', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Status', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hra', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hrb', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hrc', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Observacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ubicación', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua', 'numero' => '1');
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
    
    public function index(){
        
        $entidad          = 'Propietario';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request, Ua $uaModel){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Propietario';
        $propietario = null;
        $formData = array('propietario.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 

        $uaList = $uaModel -> all();
        return view($this->folderview.'.mant')->with(compact('propietario', 'formData', 'entidad', 'boton', 'listar', 'uaList'));
    }

    public function store(Request $request){

        $reglas     = [
            'descripcion' => 'required',
            'fecha_llegada' => 'required',
            'fecha_salida' => 'required|after_or_equal:fecha_llegada',
            'fecha_contrato' => 'required',
            'status' => 'required',
            'hra' => 'required',
            'hrb' => 'required',
            'hrc' => 'required',
            'km' => 'required',
            'observacion' => 'required',
            'ubicacion' => 'required',
            'ua_id' => ['required', new SelectDifZero(), new SearchUaPadre()]
        ];
        $mensajes = [
            'descripcion.required' => 'Su descripción es requerida',
            'fecha_llegada.required' => 'Su fecha de entrada es requerida',
            'fecha_salida.required' => 'Su fecha de salida es requerida',
            'fecha_salida.after_or_equal' => 'Su fecha de salida no puede ser menor que la de entrada',
            'fecha_contrato.required' => 'Su fecha de contrato es requerida',
            'status.required' => 'Su status es requerido',
            'hra.required' => 'Su hra es requerido',
            'hrb.required' => 'Su hrb es requerido',
            'hrc.required' => 'Su hrc es requerido',
            'km.required' => 'Su km es requerido',
            'observacion.required' => 'Su observación es requerida',
            'ubicacion.required' => 'Su ubicación es requerida',
            'ua_id.required' => 'Su ua es requerida',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request){
            $propietario = new Propietario();
            $propietario -> descripcion = strtoupper($request->input('descripcion'));
            //BUSCAR
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $propietario -> ua_id = (!( $uaDB -> isEmpty() )) ? $uaDB[0] -> id : null;
            }
            $propietario -> fecha_contrato = $request -> input('fecha_contrato');
            $propietario -> fecha_llegada = $request -> input('fecha_llegada');
            $propietario -> fecha_salida = $request -> input('fecha_salida');
            $propietario -> hra = $request -> input('hra');
            $propietario -> hrb = $request -> input('hrb');
            $propietario -> hrc = $request -> input('hrc');
            $propietario -> km = $request -> input('km');
            $propietario -> observacion = $request -> input('observacion');
            $propietario -> status = $request -> input('status');
            $propietario -> ubicacion = $request -> input('ubicacion');
            $propietario -> save();

            event( new UserHasCreatedOrDeleted($propietario->id,'propietario', auth()->user()->id,'crear'));

        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request, Ua $uaModel){

        $existe = Libreria::verificarExistencia($id, 'propietarios');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $propietario = Propietario::find($id);
        $entidad  = 'Propietario';
        $formData = array('propietario.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        $uaList = $uaModel -> all();
        return view($this->folderview.'.mant')->with(compact('propietario', 'formData', 'entidad', 'boton', 'listar', 'uaList'));
    }

    public function update(Request $request, $id){

        $reglas     = [
            'descripcion' => 'required',
            'fecha_llegada' => 'required',
            'fecha_salida' => 'required|after_or_equal:fecha_llegada',
            'fecha_contrato' => 'required',
            'status' => 'required',
            'hra' => 'required',
            'hrb' => 'required',
            'hrc' => 'required',
            'km' => 'required',
            'observacion' => 'required',
            'ubicacion' => 'required',
            'ua_id' => ['required', new SelectDifZero(), new SearchUaPadre()]
        ];
        $mensajes = [
            'descripcion.required' => 'Su descripción es requerida',
            'fecha_llegada.required' => 'Su fecha de entrada es requerida',
            'fecha_salida.required' => 'Su fecha de salida es requerida',
            'fecha_salida.after_or_equal' => 'Su fecha de salida no puede ser menor que la de entrada',
            'fecha_contrato.required' => 'Su fecha de contrato es requerida',
            'status.required' => 'Su status es requerido',
            'hra.required' => 'Su hra es requerido',
            'hrb.required' => 'Su hrb es requerido',
            'hrc.required' => 'Su hrc es requerido',
            'km.required' => 'Su km es requerido',
            'observacion.required' => 'Su observación es requerida',
            'ubicacion.required' => 'Su ubicación es requerida',
            'ua_id.required' => 'Su ua es requerida',
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $existe = Libreria::verificarExistencia($id, 'propietarios');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $propietario = Propietario::find($id);

            $propietarioOrg = json_encode($propietario);

            $propietario -> descripcion = strtoupper($request->input('descripcion'));
            //BUSCAR
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $propietario -> ua_id = (!( $uaDB -> isEmpty() )) ? $uaDB[0] -> id : null;
            }
            $propietario -> fecha_contrato = $request -> input('fecha_contrato');
            $propietario -> fecha_llegada = $request -> input('fecha_llegada');
            $propietario -> fecha_salida = $request -> input('fecha_salida');
            $propietario -> hra = $request -> input('hra');
            $propietario -> hrb = $request -> input('hrb');
            $propietario -> hrc = $request -> input('hrc');
            $propietario -> km = $request -> input('km');
            $propietario -> observacion = $request -> input('observacion');
            $propietario -> status = $request -> input('status');
            $propietario -> ubicacion = $request -> input('ubicacion');
            $propietario -> save();

            event( new UserHasEdited($propietarioOrg,$propietario,'propietario', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego){

        $existe = Libreria::verificarExistencia($id, 'propietarios');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Propietario::find($id);
        $entidad  = 'Propietario';
        $formData = array('route' => array('propietario.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'propietarios');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $propietario = Propietario::find($id);

            event( new UserHasCreatedOrDeleted($propietario->id,'propietario', auth()->user()->id,'eliminar'));
            $propietario->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
