<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Contratista;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContratistaExport;


class ContratistaController extends Controller
{
    protected $folderview      = 'app.contratistas';
    protected $tituloAdmin     = 'Subcontratistas';
    protected $tituloRegistrar = 'Registrar subcontratista';
    protected $tituloModificar = 'Modificar subcontratista';
    protected $tituloEliminar  = 'Eliminar subcontratista';
    protected $rutas           = array('create' => 'contratistas.create', 
            'edit'   => 'contratistas.edit', 
            'delete' => 'contratistas.eliminar',
            'search' => 'contratistas.buscar',
            'index'  => 'contratistas.index',
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
        $entidad          = 'Contratista';
        $nombre           = Libreria::getParam($request->input('razonsocial'));
        $resultado        = Contratista::where('razonsocial', 'LIKE', '%'.strtoupper($nombre).'%')
                            ->orWhere('ruc', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('razonsocial', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $cabecera[]       = array('valor' => 'RUC', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Razon Social', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Contacto1', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email1', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Teléfono1', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Abreviatura', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email2', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Teléfono2', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Contacto3', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email3', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Teléfono3', 'numero' => '1');
        
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
        $entidad          = 'Contratista';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Contratista';
        $contratista = null;
        $formData = array('contratistas.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('contratista', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('razonsocial' => 'required|max:100',
                    'ruc' => 'required|max:11|min:11|unique:contratista,ruc,NULL,id,deleted_at,NULL',
                    'email' => 'email');
        $mensajes = array(
            'razonsocial.required' => 'Debe ingresar una razonsocial',
            'razonsocial.max' => 'La razonsocial debe tener max. 100 caracteres',
            'ruc.required' => 'Debe ingresar un RUC',
            'ruc.unique' => 'RUC ya existe, especifique otro',
            'ruc.max' => 'El RUC debe tener 11 dígitos',
            'ruc.min' => 'El RUC debe tener 11 dígitos',
            'email.email' => 'Debe registrar un correo valido'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $contratista = new Contratista();
            $contratista->razonsocial= strtoupper($request->input('razonsocial'));
            $contratista->ruc= strtoupper($request->input('ruc'));
            $contratista->propietario= strtoupper($request->input('propietario'));
            $contratista->email= strtoupper($request->input('email'));
            $contratista->telefono= strtoupper($request->input('telefono'));
            $contratista->propietario2= strtoupper($request->input('propietario2'));
            $contratista->email2= strtoupper($request->input('email2'));
            $contratista->telefono2= strtoupper($request->input('telefono2'));
            $contratista->propietario3= strtoupper($request->input('propietario3'));
            $contratista->email3= strtoupper($request->input('email3'));
            $contratista->telefono3= strtoupper($request->input('telefono3'));
            $contratista->save();
            event( new UserHasCreatedOrDeleted($contratista->id,'contratista', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'contratista');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $contratista = Contratista::find($id);
        $entidad  = 'Contratista';
        $formData = array('contratistas.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('contratista', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'contratista');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('razonsocial' => 'required|max:100',
                        'email' => 'email');
        $mensajes = array(
            'razonsocial.required'         => 'Debe ingresar una descripción',
            'razonsocial.max' => 'La razonsocial debe tener max. 100 caracteres',
            'email.email' => 'Debe registrar un email1 valido'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $contratista = Contratista::find($id);

            $contratistaCop = json_encode($contratista);

            $contratista->razonsocial= strtoupper($request->input('razonsocial'));
            $contratista->ruc= strtoupper($request->input('ruc'));
            $contratista->propietario= strtoupper($request->input('propietario'));
            $contratista->email= strtoupper($request->input('email'));
            $contratista->telefono= strtoupper($request->input('telefono'));
            $contratista->propietario2= strtoupper($request->input('propietario2'));
            $contratista->email2= strtoupper($request->input('email2'));
            $contratista->telefono2= strtoupper($request->input('telefono2'));
            $contratista->propietario3= strtoupper($request->input('propietario3'));
            $contratista->email3= strtoupper($request->input('email3'));
            $contratista->telefono3= strtoupper($request->input('telefono3'));
            $contratista->save();

            event(new UserHasEdited($contratistaCop,$contratista,'contratista',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'contratista');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $contratista = Contratista::find($id);

            event( new UserHasCreatedOrDeleted($contratista->id,'contratista', auth()->user()->id,'eliminar'));
            $contratista->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'contratista');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Contratista::find($id);
        $entidad  = 'Contratista';
        $formData = array('route' => array('contratistas.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function exportExcel(){

        return Excel::download(new ContratistaExport, 'subcontratista.xlsx');
    }
}

