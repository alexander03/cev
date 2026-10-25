<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Equipo;
use App\Concesionaria;
use App\Librerias\Libreria;
use App\Equipodocument;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VencimientoDucumentequipo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use ZipArchive;

class EquipoDocumentController extends Controller
{
    protected $folderview      = 'app.equipodocument';
    protected $tituloAdmin     = 'Documentos de equipo';
    protected $tituloRegistrar = 'Registrar documento';
    protected $tituloModificar = 'Modificar documento';
    protected $tituloEliminar  = 'Eliminar documento';
    protected $rutas           = array('create' => 'equipodocument.create', 
            'edit'   => 'equipodocument.edit', 
            'delete' => 'equipodocument.eliminar',
            'search' => 'equipodocument.buscar',
            'index'  => 'equipodocument.index',
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
        $entidad          = 'equipodocument';

        $equipo_id      = Libreria::getParam($request->input('equipo_id'));
        $tipo             = Libreria::getParam($request->input('tipo'));
  

        $filtro           = array();
        $filtro[]         = ['equipo_id',$equipo_id];
        $filtro[]         = ['tipo', $tipo];

        $resultado        = Equipodocument::where($filtro)->orderBy('fecha');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de V', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Archivo', 'numero' => '1');
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

    public function index( Request $request ){
        
        $entidad          = 'equipodocument';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $equipo_id	  = $request->input('equipo_id');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'equipo_id'));
    }

    public  function create(){

    }
    public function store(Request $request){


        $reglas     = array(
    						'fecha' 				=> 'required',
                            'archivo'               => 'required'
                        );
        $mensajes = array(
        	'fecha.required'         		  => 'Debe ingresar una fecha',  
            'archivo.required'                => 'Debe ingresar archivo'
            );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
     		$equipodocument = new Equipodocument();

     		$equipodocument->fecha =  $request->input('fecha');
     		$equipodocument->tipo =  $request->input('tipo');
     		$equipodocument->precio =  $request->input('precio');

            $archivo  = $request->file('archivo');
            $fileName =   date("Y_m_d") . '_'. $archivo->getClientOriginalName();
            $equipodocument->archivo =  $fileName;
            $archivo->move(public_path('files/documento_equipo/'), $fileName);       
            

     		$equipodocument->equipo_id = $request->input('equipo_id');

            $equipodocument->save();

            event( new UserHasCreatedOrDeleted($equipodocument->id,'equipodocument', auth()->user()->id,'crear'));

        });
        return is_null($error) ? "OK" : $error;
    }
    public function edit(){

    }
    public function update($id, Request $request){
    	$existe = Libreria::verificarExistencia($id, 'equipodocument');
        if ($existe !== true) {
            return $existe;
        }
    	$reglas     = array(
    						'fecha' 				=> 'required'
                            
                        );
        $mensajes = array(
        	'fecha.required'         		  => 'Debe ingresar una fecha' 
            );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
     		$equipodocument =  Equipodocument::find($id);

            $equipodocumentoOrg = json_encode($equipodocument);

     		$equipodocument->fecha =  $request->input('fecha');
     		$equipodocument->tipo =  $request->input('tipo');
     		$equipodocument->precio =  $request->input('precio');
     		$equipodocument->equipo_id = $request->input('equipo_id');
            
            
            if($request->file('archivo')){
                $archivo  = $request->file('archivo');
                $fileName =  date("Y_m_d") . '_'.  $archivo->getClientOriginalName();
                $equipodocument->archivo =   $fileName;
                $archivo->move(public_path('files/documento_equipo/'), $fileName);       
            }
            
            $equipodocument->save();

            event( new UserHasEdited($equipodocumentoOrg,$equipodocument,'equipodocument', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'equipodocument');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $equipo = Equipodocument::find($id);
            event( new UserHasCreatedOrDeleted($equipo->id,'equipodocument', auth()->user()->id,'eliminar'));
            $equipo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'equipodocument');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = equipo::find($id);
        $entidad  = 'equipodocument';
        $formData = array('route' => array('equipodocument.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function notifiacionList(){
        
        $fecha_actual = date("Y-m-d");
        $fecha_limite =  date("Y-m-d",strtotime($fecha_actual."+ 1 month"));

        $concesionariaActual = $this->concesionariaActual();
        
        $resultado = Equipodocument::select(DB::raw('equipo_id , tipo , id  , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', false)
                    ->whereHas('equipo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->groupBy(DB::raw('equipo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->with(['equipo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                    }])
                    ->get();


/*
        $resultado = equipodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                        $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite]);
                    })
                     ->where('notificacion','=',false)
                     ->select('id','fecha','tipo','equipo_id')
                     ->with(['equipo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                     }])
                     ->orderBy('fecha')->get();
*/
        $vistos = Equipodocument::select(DB::raw('equipo_id , tipo , id , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', true)
                    ->whereHas('equipo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->groupBy(DB::raw('equipo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->with(['equipo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                    }])
                    ->get();
/*
        $vistos = equipodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                        $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite]);
                    })
                  ->where('notificacion','=',true)
                  ->select('id','fecha','tipo','equipo_id')
                  ->with(['equipo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                  }])
                  ->orderBy('fecha')->get();
*/
        $respuesta = ['notfy_new' => $resultado, 'notify_vist' => $vistos];

        return response() -> json($respuesta);
    }


    public function notifiacionCount(){
        
        $fecha_actual = date("Y-m-d");
        $fecha_limite = date("Y-m-d",strtotime($fecha_actual."+ 1 month"));

        $concesionariaActual = $this->concesionariaActual();

        $resultado = Equipodocument::select(DB::raw('equipo_id , tipo , id , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', false)
                    ->groupBy(DB::raw('equipo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->whereHas('equipo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->get();

/*
        $resultado = equipodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                        $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite])
                            ->orWhere('fecha','<=',$fecha_actual);
                    })
                    ->where('notificacion','=',false)
                    ->whereHas('equipo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                            })
                    ->get();
*/
        $resultado = $resultado->count();

        return response() -> json(array('numero' => $resultado));
    }

    public function notificacionExel($id){
        $document = Equipodocument::find($id);
        $document->notificacion = true;
        $document->save();
        return Excel::download(new VencimientoDucumentequipo($id), 'vencimiento-documento-equipo.xlsx');
    }

    private function concesionariaActual(){
        $ConcesionariaActual = Concesionaria::join('userconcesionaria','userconcesionaria.concesionaria_id','=','concesionaria.id')
        ->join('users','users.id','=','userconcesionaria.user_id')
        ->where('userconcesionaria.estado','=',true)->where('userconcesionaria.user_id','=',auth()->user()->id)
        ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function downloadMultiple(Request $request)
    {
        $id_concecionaria = $this->concesionariaActual();
        $zip = new \ZipArchive;

        $zipFileName = public_path('files/documento_equipo/').'equipodocumento-multiple.zip';
        $filesToZip = array();
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $lista =  Equipodocument::join('equipo as e','e.id','=','equipodocument.equipo_id')
                    ->whereNull('e.deleted_at')
                    ->where('equipodocument.tipo','like',$request->input('tipo'))
                    ->where('e.concesionaria_id','=',$id_concecionaria)
                    ->select('equipodocument.*')
                    ->get();
            foreach ($lista as $key => $value) {
                $filePath = public_path('files/documento_equipo/').$value->archivo;
                $filesToZip[] = $filePath;
            }

            foreach ($filesToZip as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();
        }
        if(count($filesToZip)>0){
            return response()->download($zipFileName)->deleteFileAfterSend(true);
        }else{
            return response()-> json(array('respuesta'=>'No hay documentos registrados'));;
        }

    }

}
