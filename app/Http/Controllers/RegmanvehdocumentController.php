<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\RegManVeh;
use App\Regmanvehdocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

use App\Rules\RuleImgFirmaExist;

class RegmanvehdocumentController extends Controller
{
    protected $folderview      = 'app.regmanvehdocument';
    protected $tituloAdmin     = 'Documentos del Rep. Vehicular';
    protected $tituloRegistrar = 'Registrar documento';
    protected $tituloModificar = 'Modificar documento';
    protected $tituloEliminar  = 'Eliminar documento';
    protected $rutas           = array('create' => 'regmanvehdocument.create', 
            'edit'   => 'regmanvehdocument.edit', 
            'delete' => 'regmanvehdocument.eliminar',
            'search' => 'regmanvehdocument.buscar',
            'index'  => 'regmanvehdocument.index',
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
        $entidad          = 'regmanvehdocument';

        $regmanveh_id      = Libreria::getParam($request->input('regmanveh_id'));
        $tipo             = Libreria::getParam($request->input('tipo'));

        $resultado        = Regmanvehdocument::getlist($regmanveh_id, $tipo);
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
        
        $entidad          = 'regmanvehdocument';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $regmanveh_id	  = $request->input('regmanveh_id');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'regmanveh_id'));
    }

    public function store(Request $request){
        $reglas = array(
            'archivo' => 'required',
            'tipo' => [
                'required',
                function ($attribute, $value, $fail) use($request){
                    $regmanveh_id = $request->input('regmanveh_id');

                    $obj = Regmanvehdocument::where('regmanveh_id', $regmanveh_id)->where('tipo', $value)->first();
                    if($obj != null) $fail('Este tipo de documento ya está registrado');
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

     		$document = new Regmanvehdocument();
            $document->tipo =  $request->input('tipo');


            $obj = RegManVeh::find($request->input('regmanveh_id'));
             
            $archivo  = $request->file('archivo');
            $nameArchivo = $obj->id . '_' . $archivo->getClientOriginalName();
            $document->archivo =  $nameArchivo;

            $archivo->move(public_path('files/documento_regmanveh'), $nameArchivo);       
            
     		$document->regmanveh_id = $request->input('regmanveh_id');
            $document->save();
            event( new UserHasCreatedOrDeleted($document->id,'regmanvehdocument', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'regmanvehdocument');
        if ($existe !== true) return $existe;
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) $listar = $listarLuego;
        $mensaje = true;
        $modelo   = Regmanvehdocument::find($id);
        $entidad  = 'regmanvehdocument';
        $formData = array('route' => array('regmanvehdocument.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id) {
        $existe = Libreria::verificarExistencia($id, 'regmanvehdocument');
        if ($existe !== true) return $existe;
        $error = DB::transaction(function() use($id){
            $obj = Regmanvehdocument::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'regmanvehdocument', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
