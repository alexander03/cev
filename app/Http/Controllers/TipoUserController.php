<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Tipouser;
use App\Acceso;
use Validator;
use App\Grupomenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class TipoUserController extends Controller
{
    protected $folderview      = 'app.tipouser';
    protected $tituloAdmin     = 'Tipo de usuario';
    protected $tituloRegistrar = 'Registrar Tipo de usuario';
    protected $tituloModificar = 'Modificar Tipo de usuario';
    protected $tituloEliminar  = 'Eliminar Tipo de usuario';
    protected $rutas           = array('create' => 'tipouser.create', 
            'edit'   => 'tipouser.edit', 
            'delete' => 'tipouser.eliminar',
            'search' => 'tipouser.buscar',
            'index'  => 'tipouser.index',
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
        $entidad          = 'Tipouser';

        $descripcion      = Libreria::getParam($request->input('descripcion'));

        $filtro           = array();
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];

/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/
        $resultado        = Tipouser::where($filtro)->orderBy('descripcion', 'ASC');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
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
        $entidad          = 'Tipouser';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta' ));
    }

	public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Tipouser';
        $tipouser = null;

        $Gruposmenu = Grupomenu::orderBy('descripcion','asc')->get();
        

        $formData = array('tipouser.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('tipouser', 'formData', 'entidad', 'boton', 'listar',  'Gruposmenu'));
    }


    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
    						'descripcion' 			=> 'required|max:22'
                        );
        $mensajes = array(
            'descripcion.required' 		      => 'Debe ingresar una descripcion',
            'descripcion.max'                 => 'La descripcion sobrepasa los 22 carácteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request){
            $tipouser = new Tipouser();
            $tipouser->descripcion = strtoupper($request->input('descripcion'));
            $tipouser->save();

            event( new UserHasCreatedOrDeleted($tipouser->id,'tipouser', auth()->user()->id,'crear'));

            foreach ($request->input('opcionmenu') as $opcionmenu) {
            	$acceso = new Acceso();
            	$acceso->opcionmenu_id = (int) $opcionmenu;
            	$acceso->tipouser_id = $tipouser->id;
            	$acceso->save();
            }

        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
    	$existe = Libreria::verificarExistencia($id, 'tipouser');
        if ($existe !== true) {
            return $existe;
        }

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Tipouser';
        $tipouser = Tipouser::find($id);

        $Gruposmenu = Grupomenu::orderBy('descripcion','asc')->get();
        

        $formData = array('tipouser.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar'; 
        return view($this->folderview.'.mant')->with(compact('tipouser', 'formData', 'entidad', 'boton', 'listar',  'Gruposmenu'));
    }


    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipouser');
        if ($existe !== true) {
            return $existe;
        }

        $reglas     = array(
    						'descripcion' 			=> 'required|max:22'
                        );
        $mensajes = array(
            'descripcion.required' 		      => 'Debe ingresar una descripcion',
            'descripcion.max'                 => 'La descripcion sobrepasa los 22 carácteres'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request, $id){
            $tipouser = Tipouser::find($id);

            $tipouserOrg = json_encode(Tipouser::with('accesos')->find($id));

            $tipouser->descripcion = strtoupper($request->input('descripcion'));
            
            $OpcionMenuOriginal = $tipouser->accesos()->get();

            $OpcionMenuNuevo = $request->input('opcionmenu');


            $tipouser->save();

            event( new UserHasEdited($tipouserOrg,$tipouser,'tipouser', auth()->user()->id));

            if($OpcionMenuNuevo!=""){
                foreach ($OpcionMenuNuevo as $checkbox => $value) {
                	if($OpcionMenuOriginal->count() == 0 || empty($OpcionMenuNuevo)){
                		break;
                	}
                	foreach ($OpcionMenuOriginal as $key => $OpcionOriginal) {
                			if($OpcionMenuOriginal->count() == 0 || empty($OpcionMenuNuevo)){
                					break;
                				}
    	            		if( $OpcionMenuOriginal[$key]->opcionmenu_id == (int) $OpcionMenuNuevo[$checkbox]){
    	            			$OpcionMenuOriginal->forget($key);
    	            			unset($OpcionMenuNuevo[$checkbox]);
    	            			break;
    	            		}
    	        	}
                }

                foreach ( $OpcionMenuNuevo as $nuevo) {

    	            	$acceso = new Acceso();
    	            	$acceso->opcionmenu_id = (int) $nuevo;
    	            	$acceso->tipouser_id = $tipouser->id;
                        $acceso->save();

                }
            }

            foreach ($OpcionMenuOriginal as $OpcionEliminada) {
	            		$OpcionEliminada->delete();
	        }

            event( new UserHasEdited($tipouserOrg, Tipouser::with('accesos')->find($id) ,'tipouser', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'tipouser');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Tipouser::find($id);
        $entidad  = 'Tipouser';
        $formData = array('route' => array('tipouser.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipouser');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $tipouser = Tipouser::find($id);
            $tipouser->delete();

            event( new UserHasCreatedOrDeleted($tipouser->id,'tipouser', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }
}
