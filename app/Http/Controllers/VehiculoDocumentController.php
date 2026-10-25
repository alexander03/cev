<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Vehiculo;
use App\Concesionaria;
use App\Librerias\Libreria;
use App\Vehiculodocument;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VencimientoDucumentVehiculo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use ZipArchive;

class VehiculoDocumentController extends Controller
{
    protected $folderview      = 'app.vehiculodocument';
    protected $tituloAdmin     = 'Documentos de vehículo';
    protected $tituloRegistrar = 'Registrar documento';
    protected $tituloModificar = 'Modificar documento';
    protected $tituloEliminar  = 'Eliminar documento';
    protected $rutas           = array('create' => 'vehiculodocument.create', 
            'edit'   => 'vehiculodocument.edit', 
            'delete' => 'vehiculodocument.eliminar',
            'search' => 'vehiculodocument.buscar',
            'index'  => 'vehiculodocument.index',
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
        $entidad          = 'vehiculodocument';

        $vehiculo_id      = Libreria::getParam($request->input('vehiculo_id'));
        $tipo             = Libreria::getParam($request->input('tipo'));
  

        $filtro           = array();
        $filtro[]         = ['vehiculo_id',$vehiculo_id];
        $filtro[]         = ['tipo', $tipo];

        $resultado        = Vehiculodocument::where($filtro)->orderBy('fecha');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
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
        
        $entidad          = 'vehiculodocument';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $vehiculo_id	  = $request->input('vehiculo_id');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'vehiculo_id'));
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

        $registro = Vehiculodocument::where('fecha','=',$request->input('fecha'))
                                    ->where('tipo','=',$request->input('tipo'))
                                    ->where('vehiculo_id','=',$request->input('vehiculo_id'))
                                    ->first();
        if(!is_null($registro)){
            $dat = array("fecha" => ["No puede registrar el documento con la misma fecha de vencimiento"],"id"=>0);
            return json_encode($dat);
        }

        $registro = Vehiculodocument::where('fecha','>=',$request->input('fecha'))
                                    ->where('tipo','=',$request->input('tipo'))
                                    ->where('vehiculo_id','=',$request->input('vehiculo_id'))
                                    ->first();
        if(!is_null($registro)){
            $dat = array("fecha" => ["No puede registrar el documento con la fecha menor de vencimiento"],"id"=>0);
            return json_encode($dat);
        }

        $error = DB::transaction(function() use($request){
     		$vehiculodocument = new Vehiculodocument();

     		$vehiculodocument->fecha =  $request->input('fecha');
     		$vehiculodocument->tipo =  $request->input('tipo');
     		$vehiculodocument->precio =  $request->input('precio');

            $archivo  = $request->file('archivo');
            $fileName =   date("Y_m_d") . '_'. $archivo->getClientOriginalName();
            $vehiculodocument->archivo =  $fileName;
            $archivo->move(public_path('files/documento_vehiculo/'), $fileName);       
            

     		$vehiculodocument->vehiculo_id = $request->input('vehiculo_id');

            $vehiculodocument->save();

            event( new UserHasCreatedOrDeleted($vehiculodocument->id,'vehiculodocument', auth()->user()->id,'crear'));

        });
        return is_null($error) ? "OK" : $error;
    }
    public function edit(){

    }
    public function update($id, Request $request){
    	$existe = Libreria::verificarExistencia($id, 'vehiculodocument');
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
     		$vehiculodocument =  Vehiculodocument::find($id);

            $vehiculodocumentoOrg = json_encode($vehiculodocument);

     		$vehiculodocument->fecha =  $request->input('fecha');
     		$vehiculodocument->tipo =  $request->input('tipo');
     		$vehiculodocument->precio =  $request->input('precio');
     		$vehiculodocument->vehiculo_id = $request->input('vehiculo_id');
            
            
            if($request->file('archivo')){
                $archivo  = $request->file('archivo');
                $fileName =  date("Y_m_d") . '_'.  $archivo->getClientOriginalName();
                $vehiculodocument->archivo =   $fileName;
                $archivo->move(public_path('files/documento_vehiculo/'), $fileName);       
            }
            
            $vehiculodocument->save();

            event( new UserHasEdited($vehiculodocumentoOrg,$vehiculodocument,'vehiculodocument', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'vehiculodocument');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $vehiculo = Vehiculodocument::find($id);
            event( new UserHasCreatedOrDeleted($vehiculo->id,'vehiculodocument', auth()->user()->id,'eliminar'));
            $vehiculo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'vehiculodocument');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Vehiculo::find($id);
        $entidad  = 'vehiculodocument';
        $formData = array('route' => array('vehiculodocument.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function notifiacionList(){
        
        $fecha_actual = date("Y-m-d");
        $fecha_limite =  date("Y-m-d",strtotime($fecha_actual."+ 1 month"));

        $concesionariaActual = $this->concesionariaActual();
        
        $resultado = Vehiculodocument::select(DB::raw('vehiculo_id , tipo , id  , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', false)
                    ->whereHas('vehiculo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->groupBy(DB::raw('vehiculo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->with(['vehiculo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                    }])
                    ->get();


        /*
                $resultado = Vehiculodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                                $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite]);
                            })
                             ->where('notificacion','=',false)
                             ->select('id','fecha','tipo','vehiculo_id')
                             ->with(['vehiculo' => function($q){
                                $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                             }])
                             ->orderBy('fecha')->get();
        */
        $vistos = Vehiculodocument::select(DB::raw('vehiculo_id , tipo , id , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', true)
                    ->whereHas('vehiculo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->groupBy(DB::raw('vehiculo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->with(['vehiculo' => function($q){
                        $q->select('id','modelo','placa','marca_id')->with('marca:id,descripcion');
                    }])
                    ->get();
        /*
                $vistos = Vehiculodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                                $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite]);
                            })
                          ->where('notificacion','=',true)
                          ->select('id','fecha','tipo','vehiculo_id')
                          ->with(['vehiculo' => function($q){
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

        $resultado = Vehiculodocument::select(DB::raw('vehiculo_id , tipo , id , notificacion , MAX(fecha) as fecha'))
                    ->where('notificacion', false)
                    ->groupBy(DB::raw('vehiculo_id , tipo'))
                    ->having('fecha','>=',$fecha_actual)
                    ->having('fecha','<=',$fecha_limite)
                    ->whereHas('vehiculo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                    })
                    ->get();

/*
        $resultado = Vehiculodocument::where(function ($sql) use($fecha_actual,$fecha_limite){
                        $sql->whereBetween('fecha',[$fecha_actual,$fecha_limite])
                            ->orWhere('fecha','<=',$fecha_actual);
                    })
                    ->where('notificacion','=',false)
                    ->whereHas('vehiculo',function($query) use($concesionariaActual){
                                $query->where('concesionaria_id',  $concesionariaActual);
                            })
                    ->get();
*/
        $resultado = $resultado->count();

        return response() -> json(array('numero' => $resultado));
    }

    public function notificacionExel($id){
        $document = Vehiculodocument::find($id);
        $document->notificacion = true;
        $document->save();
        return Excel::download(new VencimientoDucumentVehiculo($id), 'vencimiento-documento-vehiculo.xlsx');
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

        $zipFileName = public_path('files/documento_vehiculo/').'vehiculodocumento-multiple.zip';
        $filesToZip = array();
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $lista =  Vehiculodocument::join('vehiculo as v','v.id','=','vehiculodocument.vehiculo_id')
                    ->whereNull('v.deleted_at')
                    ->where('vehiculodocument.tipo','like',$request->input('tipo'))
                    ->where('v.concesionaria_id','=',$id_concecionaria)
                    ->select('vehiculodocument.*')
                    ->get();
            foreach ($lista as $key => $value) {
                $filePath = public_path('files/documento_vehiculo/').$value->archivo;
                $filesToZip[] = $filePath;
            }

            foreach ($filesToZip as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();
        }

        return response()->download($zipFileName)->deleteFileAfterSend(true);

    }

}
