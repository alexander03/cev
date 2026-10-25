<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Conductor;
use App\Conductordocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

use App\Rules\RuleImgFirmaExist;

class ConductordocumentController extends Controller
{
    protected $folderview      = 'app.conductordocument';
    protected $tituloAdmin     = 'Documentos del conductor';
    protected $tituloRegistrar = 'Registrar documento';
    protected $tituloModificar = 'Modificar documento';
    protected $tituloEliminar  = 'Eliminar documento';
    protected $rutas           = array('create' => 'conductordocument.create', 
            'edit'   => 'conductordocument.edit', 
            'delete' => 'conductordocument.eliminar',
            'search' => 'conductordocument.buscar',
            'index'  => 'conductordocument.index',
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
        $entidad          = 'conductordocument';

        $conductor_id      = Libreria::getParam($request->input('conductor_id'));
        $tipo             = Libreria::getParam($request->input('tipo'));

        $resultado        = Conductordocument::getlist($conductor_id, $tipo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'F. Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Archivo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '1');
        
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

    public function index( Request $request ){
        
        $entidad          = 'conductordocument';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $conductor_id	  = $request->input('conductor_id');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'conductor_id'));
    }

    public function store(Request $request){
        $reglas = array(
            'archivo' => 'required',
            'tipo' => [
                'required',
                function ($attribute, $value, $fail) use($request){
                    $conductor_id = $request->input('conductor_id');

                    $conductordocument = Conductordocument::where('conductor_id', $conductor_id)->where('tipo', $value)->first();
                    if($conductordocument != null) $fail('Este tipo de documento ya está registrado');
                },
            ]
        );
        $mensajes = array(
            'archivo.required' => 'Debe ingresar archivo',
            'tipo.required' => 'Debe elegir un tipo de archivo'
        );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);

        if ($validacion->fails()) return $validacion->messages()->toJson();

        $error = DB::transaction(function() use($request){

     		$conductordocument = new Conductordocument();
            $conductordocument->tipo =  $request->input('tipo');


            $conductor = Conductor::find($request->input('conductor_id'));
             
            $archivo  = $request->file('archivo');
            $nameArchivo = 'dni_' . $conductor->dni . '_' . $archivo->getClientOriginalName();
            $conductordocument->archivo =  $nameArchivo;

            switch ($request->input('tipo')) {
                case 'imagen-firma':
                    $archivo->move(public_path('files/documento_conductor/imagenes_firmas'), $nameArchivo);       
                    break;
                case 'conformidad-firma':
                    $archivo->move(public_path('files/documento_conductor/documentos_conformidad_firmas'), $nameArchivo);       
                    break;
                case 'licencia_conducir':
                    $archivo->move(public_path('files/documento_conductor/licencia_conducir'), $nameArchivo);       
                    break;
            }

     		$conductordocument->conductor_id = $request->input('conductor_id');
            $conductordocument->save();
            event( new UserHasCreatedOrDeleted($conductordocument->id,'conductordocument', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'conductordocument');
        if ($existe !== true) return $existe;
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;
        $mensaje = true;
        $modelo   = Conductordocument::find($id);
        $entidad  = 'conductordocument';
        $formData = array('route' => array('conductordocument.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id) {
        $existe = Libreria::verificarExistencia($id, 'conductordocument');
        if ($existe !== true) return $existe;
        $error = DB::transaction(function() use($id){
            $conductordocument = Conductordocument::find($id);
            event( new UserHasCreatedOrDeleted($conductordocument->id,'conductordocument', auth()->user()->id,'eliminar'));
            $conductordocument->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
