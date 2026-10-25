<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Conductor;
use App\Contratista;
use App\Concesionaria;
use App\Conductorconcesionaria;
use App\Conductordocument;
use App\Tipouser;
use App\Lider;
use App\User;
use App\UserConcesionaria;
use Illuminate\Support\Facades\Hash;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use GuzzleHttp\Client;

class ConductorController extends Controller
{
    protected $folderview      = 'app.conductores';
    protected $tituloAdmin     = 'Conductores/Operadores';
    protected $tituloRegistrar = 'Registrar conductor/operador';
    protected $tituloModificar = 'Modificar conductor/operador';
    protected $tituloEliminar  = 'Eliminar conductor/operador';
    protected $tituloActivar  = 'Activar conductor';
    protected $tituloDocumentos  = 'Documentos';
    protected $rutas           = array('create' => 'conductores.create', 
            'edit'   => 'conductores.edit', 
            'delete' => 'conductores.eliminar',
            'activar' => 'conductores.activar',
            'search' => 'conductores.buscar',
            'index'  => 'conductores.index',
            'documentos_index' => 'conductordocument.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request) {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Conductor';
        $filter           = Libreria::getParam($request->input('filter'));

        $estado           = $request->input('estado');
        $categoria_id        = $request->input('categoria_id');
        $contratista      = $request->input('contratista');
        $tipo             = $request->input('tipo');
        $resultado        = Conductor::getFilter($estado , $filter, $categoria_id, $contratista, $tipo);
        $lista            = $resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellidos y nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Categoria', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Licencia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'F.Vencimiento', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Contratista', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_activar  = $this->tituloActivar;
        $titulo_documentos  = $this->tituloDocumentos;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar', 'titulo_documentos', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index() {
        $entidad          = 'Conductor';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        $arrCategorias    = array(
            'all' => 'TODOS',
            'A-I' => 'A-I',
            'A-IIa' => 'A-IIa',
            'A-IIb' => 'A-IIb',
            'A-IIIa' => 'A-IIIa',
            'A-IIIb' => 'A-IIIb',
            'A-IIIc' => 'A-IIIc',
            'B-IIc' => 'B-IIc',
        );

        $arrContratista      = Contratista::getAll();
        $cboContratista = array('all' => 'TODOS');
        foreach($arrContratista as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        $cboTipo = array(''=>'Todos','Conductor'=>'Conductor','Operador'=>'Operador');
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'arrCategorias', 'cboContratista', 'ruta','cboTipo','user'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Conductor';
        $conductor = null;
        $licenciaLetra = null;
        $username_conductor = null;
        $formData = array('conductores.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', 'role' => 'form', 'enctype' => 'multipart/form-data');
        $boton    = 'Registrar';
        $arrCategorias    = array(
            '' => 'Seleccione Categoría',
            'A-I' => 'A-I',
            'A-IIa' => 'A-IIa',
            'A-IIb' => 'A-IIb',
            'A-IIIa' => 'A-IIIa',
            'A-IIIb' => 'A-IIIb',
            'A-IIIc' => 'A-IIIc',
            'B-IIc' => 'B-IIc',
        );
        $arrContratista      = Contratista::getAll();
        $cboContratista = array('' => 'Seleccione');
        foreach($arrContratista as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        $cboLider = array(''=>'Seleccione');
        $lider = Lider::orderBy('nombres','asc')->get();
        foreach($lider as $k=>$v){
            $cboLider += array($v->id=>$v->nombres);   
        }
        $cboTipo = array('Conductor'=>'Conductor','Operador'=>'Operador');
        return view($this->folderview.'.mant')->with(compact('conductor', 'formData', 'entidad', 'boton', 'licenciaLetra', 'username_conductor', 'arrCategorias', 'cboContratista', 'listar','cboLider','cboTipo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'dni' => 'required',//integer|
            //'licencia_letra' => 'required|alpha',
            //'categoria' => 'required',
            //'fechavencimiento' => 'required',
            //'contratista_id' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
            'lider_id' => 'required|integer'
        );
        $mensajes = array(
            'dni.required' => 'Debe ingresar un DNI',
            'username.required' => 'Debe ingresar un usuario',
            'username.unique' => 'Nombre de usuario ya existe, especifique otro',
            'password.required' => 'Debe ingresar un password',
            //'dni.integer' => 'DNI inválido',
            //'dni.digits' => 'DNI debe tener 8 cifras',
            //'licencia_letra.required' => 'Licencia incompleta',
            //'licencia_letra.alpha' => 'Licencia inválida',
            //'categoria.required' => 'Seleccione categoría',
            //'fechavencimiento.required' => 'Obligatorio fecha de vencimiento de lic. conducir',
            //'contratista_id.required' => 'Seleccione contratista',
            'lider_id.required' => 'Debe seleccionar un lider',
            'lider_id.integer' => 'Debe seleccionar un lider',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        if(($request->input('fechavencimiento')=="" || $request->input('categoria')=="") && $request->input('tipo')=="Conductor"){
            return json_encode(['fechavencimiento' => ['Obligatorio fecha de vencimiento de lic. conducir']]);
        }
        $error = DB::transaction(function() use($request){
            $concesionariaActual = Concesionaria::getConcesionariaActual();

            //Busco al conductor
            $conductorBuscado = Conductor::where('dni', $request->input('dni'))->first();
            if($conductorBuscado == null) {
                $tipo_conductor = Tipouser::where('descripcion', 'CONDUCTOR')->first();
                $user = new User();
                $user->tipouser_id= $tipo_conductor->id;
                $user->name = mb_strtoupper($request->input('nombres'), 'utf-8') .' ' . mb_strtoupper($request->input('apellidos'), 'utf-8');
                $user->username= $request->input('username');
                $user->password= Hash::make($request->input('password'));
                $user->concesionaria_id = $concesionariaActual->id;
                $user->save();

                event( new UserHasCreatedOrDeleted($user->id,'user', auth()->user()->id,'crear'));

                $userconcesionaria = new UserConcesionaria();
                $userconcesionaria->user_id = $user->id;
                $userconcesionaria->concesionaria_id = $concesionariaActual->id;
                $userconcesionaria->save();


                event( new UserHasCreatedOrDeleted($userconcesionaria->id,'userconcesionaria', auth()->user()->id,'crear'));


                $conductor = new Conductor();
                $conductor->dni= $request->input('dni');
                $conductor->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
                $conductor->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
                $conductor->licencia= mb_strtoupper($request->input('licencia_letra'), 'utf-8') . '-' . $request->input('dni') ;
                $conductor->categoria= $request->input('categoria');
                $conductor->fechavencimiento= mb_strtoupper($request->input('fechavencimiento'), 'utf-8');
                $conductor->contratista_id= mb_strtoupper($request->input('contratista_id'), 'utf-8');
                $conductor->user_id = $user->id;
                $conductor->lider_id = $request->input('lider_id');
                $conductor->tipo = $request->input('tipo');
                $conductor->save();

                event( new UserHasCreatedOrDeleted($conductor->id,'conductor', auth()->user()->id,'crear'));
    
                $conductorconcesionaria = new Conductorconcesionaria();
                $conductorconcesionaria->conductor_id = $conductor->id;
                $conductorconcesionaria->concesionaria_id = $concesionariaActual->id;
                $conductorconcesionaria->save();

                event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', auth()->user()->id,'crear'));

                $imgFirma  = $request->file('imagenfirma');
                if(!is_null($imgFirma)){
                    $nameImgFirma = 'dni_' . $conductor->dni . '_' . $imgFirma->getClientOriginalName();

                    $conductordocument = new Conductordocument();
                    $conductordocument->tipo = 'imagen-firma';
                    $conductordocument->archivo = $nameImgFirma;
                    $conductordocument->conductor_id = $conductor->id;
                    $conductordocument->save();
    
                    event( new UserHasCreatedOrDeleted($conductordocument->id,'conductordocument', auth()->user()->id,'crear'));
    
                    $imgFirma->move(public_path('files/documento_conductor/imagenes_firmas'), $nameImgFirma);
                }

                $docConformidadFirma  = $request->file('conformidadfirma');
                if(!is_null($docConformidadFirma)){                
                    $nameDocConformidadFirma = 'dni_' . $conductor->dni . '_' . $docConformidadFirma->getClientOriginalName();
    
                    $conductordocument = new Conductordocument();
                    $conductordocument->tipo = 'conformidad-firma';
                    $conductordocument->archivo = $nameDocConformidadFirma;
                    $conductordocument->conductor_id = $conductor->id;
                    $conductordocument->save();
    
                    event( new UserHasCreatedOrDeleted($conductordocument->id,'conductordocument', auth()->user()->id,'crear'));
    
                    $docConformidadFirma->move(public_path('files/documento_conductor/documentos_conformidad_firmas'), $nameDocConformidadFirma);
                }

            } else {
                $conductorconcesionaria = new Conductorconcesionaria();
                $conductorconcesionaria->conductor_id = $conductorBuscado->id;
                $conductorconcesionaria->concesionaria_id = $concesionariaActual->id;
                $conductorconcesionaria->save();

                event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', auth()->user()->id,'crear'));
            }


        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'conductor');
        if ($existe !== true) return $existe;
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $conductor = Conductor::find($id);
        $licenciaLetra = $conductor->licencia[0];
        // $username_conductor = $conductor->user->username;

        $entidad  = 'Conductor';
        $formData = array('conductores.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton    = 'Modificar';
        $arrCategorias    = array(
            '' => 'Seleccione Categoría',
            'A-I' => 'A-I',
            'A-IIa' => 'A-IIa',
            'A-IIb' => 'A-IIb',
            'A-IIIa' => 'A-IIIa',
            'A-IIIb' => 'A-IIIb',
            'A-IIIc' => 'A-IIIc',
            'B-IIc' => 'B-IIc',
        );
        $arrContratista      = Contratista::getAll();
        $cboContratista = array('' => 'Seleccione');
        foreach($arrContratista as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        $cboLider = array(''=>'Seleccione');
        $lider = Lider::orderBy('nombres','asc')->get();
        foreach($lider as $k=>$v){
            $cboLider += array($v->id=>$v->nombres);   
        }
        $cboTipo = array('Conductor'=>'Conductor','Operador'=>'Operador');
        return view($this->folderview.'.mant')->with(compact('conductor', 'formData', 'entidad', 'boton', 'licenciaLetra', 'arrCategorias', 'cboContratista', 'listar','cboLider','cboTipo'));
    }

    public function update(Request $request, $id) {
        $existe = Libreria::verificarExistencia($id, 'conductor');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'dni' => 'required',//integer|//|digits:8
            //'licencia_letra' => 'required|alpha',
            //'categoria' => 'required',
            //'fechavencimiento' => 'required',
            //'contratista_id' => 'required',
            'lider_id' => 'required|integer'
        );
        $mensajes = array(
            'dni.required' => 'Debe ingresar un DNI',
            //'dni.integer' => 'DNI inválido',
            //'dni.digits' => 'DNI debe tener 8 cifras',
            //'licencia_letra.required' => 'Licencia incompleta',
            //'licencia_letra.alpha' => 'Licencia inválida',
            //'categoria.required' => 'Seleccione categoría',
            //'fechavencimiento.required' => 'Obligatorio fecha de vencimiento de lic. conducir',
            //'contratista_id.required' => 'Seleccione contratista',
            'lider_id.required' => 'Debe seleccionar un lider',
            'lider_id.integer' => 'Debe seleccionar un lider',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        if(($request->input('fechavencimiento')=="0000-00-00" || $request->input('fechavencimiento')=="" || $request->input('categoria')=="") && $request->input('tipo')=="Conductor"){
            return json_encode(['fechavencimiento' => ['Obligatorio fecha de vencimiento de lic. conducir']]);
        }
        $error = DB::transaction(function() use($request, $id){
            $conductor = Conductor::find($id);

            $conductorCop = json_encode($conductor);

            $conductor->dni= $request->input('dni');
            $conductor->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $conductor->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $conductor->licencia= mb_strtoupper($request->input('licencia_letra'), 'utf-8') . '-' . $request->input('dni');
            $conductor->categoria= $request->input('categoria');
            $conductor->fechavencimiento= mb_strtoupper($request->input('fechavencimiento'), 'utf-8');
            $conductor->contratista_id= mb_strtoupper($request->input('contratista_id'), 'utf-8');
            $conductor->lider_id = $request->input('lider_id');
            $conductor->tipo = $request->input('tipo');
            $conductor->save();

            event(new UserHasEdited($conductorCop,$conductor,'abastecimiento',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'conductor');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Conductor::find($id);
        $entidad  = 'Conductor';
        $formData = array('route' => array('conductores.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $concesionariaActual = Concesionaria::getConcesionariaActual();

        $conductorBuscado = Conductor::where('conductor.id', $id)
                        ->join('conductorconcesionaria', 'conductorconcesionaria.conductor_id', '=', 'conductor.id')
                        ->where('conductorconcesionaria.concesionaria_id', $concesionariaActual->id)->first();

        if ($conductorBuscado == null) {
            $cadena = '<blockquote><p class="text-danger">Registro no existe en la base de datos. No manipular ID</p></blockquote>';
			$cadena .= '<button class="btn btn-warning btn-sm" id="btnCerrarexiste"><i class="fa fa-times fa-lg"></i> Cerrar</button>';
			$cadena .= "<script type=\"text/javascript\">
							$(document).ready(function() {
								$('#btnCerrarexiste').attr('onclick','cerrarModal(' + (contadorModal - 1) + ');').unbind('click');
							}); 
						</script>";
			return $cadena;
        }
        $error = DB::transaction(function() use($id, $concesionariaActual){
            $conductorconcesionaria = Conductorconcesionaria::where('conductor_id', $id)->where('concesionaria_id', $concesionariaActual->id)->first();
            $conductorconcesionaria->estado = false;
            $conductorconcesionaria->save();

            event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', auth()->user()->id,'eliminar'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego){
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Conductor::find($id);
        $entidad  = 'Conductor';
        $formData = array('route' => array('conductores.reactivar', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reactivar($id){

        $concesionariaActual = Concesionaria::getConcesionariaActual();

        $conductorBuscado = Conductor::where('conductor.id', $id)
                        ->join('conductorconcesionaria', 'conductorconcesionaria.conductor_id', '=', 'conductor.id')
                        ->where('conductorconcesionaria.concesionaria_id', $concesionariaActual->id)->first();

        if ($conductorBuscado == null) {
            $cadena = '<blockquote><p class="text-danger">Registro no existe en la base de datos. No manipular ID</p></blockquote>';
			$cadena .= '<button class="btn btn-warning btn-sm" id="btnCerrarexiste"><i class="fa fa-times fa-lg"></i> Cerrar</button>';
			$cadena .= "<script type=\"text/javascript\">
							$(document).ready(function() {
								$('#btnCerrarexiste').attr('onclick','cerrarModal(' + (contadorModal - 1) + ');').unbind('click');
							}); 
						</script>";
			return $cadena;
        }
        $error = DB::transaction(function() use($id, $concesionariaActual){
            $conductorconcesionaria = Conductorconcesionaria::where('conductor_id', $id)->where('concesionaria_id', $concesionariaActual->id)->first();
            $conductorconcesionaria->estado = true;
            $conductorconcesionaria->save();

            event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', auth()->user()->id,'reactivar'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function existeConductor(Request $request) {
        $concesionariaActual = Concesionaria::getConcesionariaActual();

        return $res = Conductor::withTrashed()
                ->join('conductorconcesionaria', 'conductorconcesionaria.conductor_id', '=', 'conductor.id')
                ->where(function($subquery) use ($concesionariaActual) {
                    $subquery->where('conductorconcesionaria.concesionaria_id', $concesionariaActual->id);
                })
                ->where('dni', $request->dni)->get();
    }
    
    public function buscarDNI(Request $request)
    {
        $respuesta = array();
        $dni = $request->input('dni');
        $client = new Client();
        $res = $client->get('http://facturae-garzasoft.com/facturacion/buscaCliente/BuscaCliente2.php?' . 'dni=' . $dni . '&fe=N&token=qusEj_w7aHEpX');
        if ($res->getStatusCode() == 200) { // 200 OK
            $response_data = $res->getBody()->getContents();
            $respuesta = json_decode($response_data);
        }
        return json_encode($respuesta);
    }
}
