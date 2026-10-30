<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Validator;
use App\Vehiculo;
use App\Area;
use App\Tipohora;
use App\Ua;
use App\Concesionaria;
use App\Controldiariovehiculo;
use App\Contratista;
use App\Configuracion;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre;
use App\Rules\SearchVehiculo;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use DateTime;
use App\Exports\ExcelReportDetalladoVehiculo;
use App\Exports\ExcelReportConsolidadoVehiculo;
use Exception;
use Mpdf\Mpdf;
use App\Exports\PdfReportDetalladoVehiculo;
use App\Exports\PdfReportConsolidadoVehiculo;

class ControlDiarioVehiculoController extends Controller
{
    protected $folderview      = 'app.controldiariovehiculo';
    protected $tituloAdmin     = 'Control diario de vehiculo';
    protected $tituloRegistrar = 'Registrar control diario de vehiculo';
    protected $tituloModificar = 'Modificar control diario de vehiculo';
    protected $tituloEliminar  = 'Eliminar control diario de vehiculo';
    protected $tituloGenerarReporteMedicionVehiculo  = 'Generar reporte de Medicion de Equipo Liviano';
    protected $rutas           = array('create' => 'controldiariovehiculo.create', 
            'edit'           => 'controldiariovehiculo.edit', 
            'delete'         => 'controldiariovehiculo.eliminar',
            'search'         => 'controldiariovehiculo.buscar',
            'index'          => 'controldiariovehiculo.index',
            'generateReportMedicionVehiculo' => 'controldiariovehiculo.generateReportMedicionVehiculo'
        );

       /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 6)->first()->valor));
    }


    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Controldiariovehiculo';

        $ua     	  = Libreria::getParam($request->input('ua'));
        $vehiculo_ua      = Libreria::getParam($request->input('vehiculo_ua'));
  		$descripcion = Libreria::getParam($request->input('descripcion'));
  		$concesionaria = $this->consecionariaActual();

        $fecha1 = Libreria::getParam($request->input('fecha_registro_inicial'));
        $fecha2 = Libreria::getParam($request->input('fecha_registro_final'));
        $filtro           = array();
        $filtro[]         = ['codigo', 'LIKE', '%'.strtoupper($ua).'%'];
        $filtro[]         = ['codigo', 'LIKE', '%'.strtoupper($vehiculo_ua).'%'];
        $filtro[]         = ['modelo', 'LIKE', '%'.strtoupper($descripcion).'%'];
        $filtro[]         = ['concesionaria_id','=',$this->consecionariaActual()];
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/       
        /*$resultado        = Controldiariovehiculo::where(function($subquery) use ($fecha1, $fecha2) {
                                            if ( $fecha1 !== null ) $subquery->where('fecha', '>=', $fecha1);
                                            if ( $fecha2 !== null ) $subquery->where('fecha', '<=', $fecha2);
                                        })->whereHas('ua', function($query) use($filtro){
        									$query->where($filtro[0][0],$filtro[0][1],$filtro[0][2])->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
        								})->WhereHas('vehiculo', function($query) use($filtro){
        									$query->whereHas('ua', function($query) use($filtro){
                                                $query->where($filtro[1][0],$filtro[1][1],$filtro[1][2]);
                                            })->orWhere($filtro[2][0],$filtro[2][1],$filtro[2][2]);
        								})
                                        ->WhereHas('vehiculo', function($query) use($filtro){
                                            $query->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
                                        })->orderBy('fecha', 'desc');*/
        $resultado = Controldiariovehiculo::join('vehiculo','vehiculo.id','=','controldiariovehiculo.vehiculo_id')
                                    ->where(function($subquery) use ($fecha1, $fecha2) {
                                            if ( $fecha1 !== null ) $subquery->where('fecha', '>=', $fecha1);
                                            if ( $fecha2 !== null ) $subquery->where('fecha', '<=', $fecha2);
                                        })
                                    ->where(function($query) use($ua,$concesionaria){
                                        if($ua!=""){
                                            $query->whereIn('controldiariovehiculo.ua_id',function($sql) use($ua,$concesionaria){
                                                $sql->select("id")
                                                    ->from("ua")
                                                    ->where("codigo","like","%".$ua."%")
                                                    ->where("concesionaria_id","=",$concesionaria);
                                            });
                                        }  
                                    })->where(function($query) use($vehiculo_ua,$concesionaria){
                                        if($vehiculo_ua!=""){
                                            $query->whereIn('vehiculo.ua_id',function($sql) use($vehiculo_ua,$concesionaria) {
                                                $sql->select("id")
                                                    ->from("ua")
                                                    ->where("codigo","like","%".$vehiculo_ua."%")
                                                    ->where("concesionaria_id","=",$concesionaria);
                                            });
                                        }  
                                    })->where(function ($query) use($descripcion){
                                        if($descripcion!=""){
                                            $query->where('vehiculo.modelo','like','%'.$descripcion.'%');
                                        }
                                    })
                                    /*->WhereHas('equipo', function($query) use($filtro){
                                            $query->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
                                        })*/->select('controldiariovehiculo.*')->orderBy('fecha', 'desc');
/*
        if($anio){
            $resultado->whereYear('fecha','=',$anio);
        }
        if($mes){
            $resultado->whereYear('fecha','=',$mes);   
        }
        if($dia){
            $resultado->whereYear('fecha','=',$dia);   
        }
*/
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua vehiculo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Desc vehiculo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Subcontratista', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA Trabajo', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Dias Trabajados', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo de Dia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Dias Parado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horómetro/Km inicial', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horómetro/Km final', 'numero' => '1');
        /*
        $cabecera[]       = array('valor' => 'Viajes', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Acceso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Desc origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Desc Destino', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Material', 'numero' => '1');
        */
		$cabecera[]       = array('valor' => 'Observaciones', 'numero' => '1');

        
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Controldiariovehiculo';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_generar_reporte_medicion_equipos   = $this->tituloGenerarReporteMedicionVehiculo;
        $ruta             = $this->rutas;

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'titulo_generar_reporte_medicion_equipos','ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiariovehiculo';
        $controldiario = null;

/*
        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
//        $cboUa += array('1' => '12345ua');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo );
        }
*/
        $thoras = Tipohora::orderBy('codigo','asc')->get();
        $cboThoras = array();
        $cboThoras += array('0' => 'Ninguno');
        foreach($thoras as $k=>$v){
            $cboThoras += array($v->id => $v->descripcion . ' - ' .$v->codigo);
        }

        $cboTurnos = array();
        $cboTurnos += array(1 => 'Diurno');
        $cboTurnos += array(0 => 'Nocturno');

        $formData = array('controldiariovehiculo.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('controldiario', 'cboTurnos' ,'formData', 'entidad', 'boton', 'listar','cboThoras'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(

                            'idVehiculo' 			=> [new SearchVehiculo()],
//                            'idEquipo'              => 'required',
    						//'tipohora_id.*' 			=> 'numeric',
                            //'ua_id.*'               =>  [ 'required', new SearchUaPadre() ],
                            //'horometro_inicial'     => 'required|numeric',
                            //'horometro_final'       => 'required|numeric|gt:horometro_inicial',
    						//'hora_inicio.*'			=> 'required',
                            //'hora_total.*'          => 'required|numeric',
    						//'hora_fin.*'		    => 'required', //|after:hora_inicio
    						'fecha'  				=> 'date'
                        );
        $mensajes = array(
            'tipohora_id.*.numeric'  	  		  => 'Tipo de hora inválido',
            //'hora_inicio.*.required'			  => 'Debe ingresar una hora de inicio válida',
            //'hora_total.*.required'               => 'Debe ingresar la hora total',
            //'hora_total.*.numeric'               => 'Debe ingresar la hora total valida',
            //'hora_fin.*.required'    			  => 'Debe ingresar una hora de finalzación válida',
            //'horometro_inicial.required'          => 'Ingrese el horómetro inicial',
            //'horometro_final.required'            => 'Ingrese el horómetro final',
            //'horometro_inicial.numeric'             => 'Ingrese el horómetro inicial',
            //'horometro_final.numeric'               => 'Ingrese el horómetro final',
            //'horometro_final.gt'               => 'El horómetro final es menor que el inicial',
        //    'hora_fin.*.after'    			  => 'Debe una hora de finalzación es menor que la hora de inicio',
            'fecha.date'					  => 'Debe ingresar una fecha válida'
            );


        $validacion = Validator::make($request->all(), $reglas, $mensajes);
/*
        $validacion->sometimes('ua_id.*',[ 'required', new SearchUaPadre() ], function($input){
        	return $input->tipohora_id == 0 ;
        });
*/
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            
//            $idEquipo = explode('--',$request->input('equipo_id'))[1];
           
//            $equipoDB = Equipo::where('codigo',$request->input('equipo_id')) -> get();
         
            foreach ($request -> input('hora_inicio') as $key => $value) {
                if(($request -> input('hora_total.'. $key)!="" && $request -> input('hora_total.'. $key)!="0") || ($request -> input('hora_parada.'. $key)!="" && $request -> input('hora_parada.'. $key)!="0")){
                    if($key=="0"){
                        $x=$request->input('fechaInicial1');
                        if($x!=$request->input('fechaFinal1')){
                            while(strtotime($x)<=strtotime($request->input('fechaFinal1'))){
                            
                                $controldiario = new Controldiariovehiculo();  
                                $controldiario->vehiculo_id           = $request -> input('idVehiculo');
                                /*if($request -> input('tipohora_id.'.$key) != 0){
                                    $tipohoraDB = Tipohora::where('id',$request -> input('tipohora_id.'.$key)) ->get();
                                    $controldiario->tipohora_id           = $tipohoraDB[0]->id;
                                    
                                }*/
                                $uaDB =  Ua::where('codigo', $request -> input('ua_id.'.$key)) -> get();
                                //$uaDB =  Ua::where('codigo', $request -> input('vehiculo_id')) -> get();
                                $controldiario->ua_id                 = $uaDB[0]->id;
                
                //                $controldiario->hora_inicio = $request -> input('hora_inicio.'. $key);
                                $controldiario->hora_total = 1;
                                $controldiario->hora_parada = 0;
                //              $controldiario->hora_fin    = $request -> input('hora_fin.'. $key);
                                $controldiario->fecha       = $x;
                                $controldiario->horometro_inicial       = $request -> input('horometro_inicial');
                                $controldiario->horometro_final       = $request -> input('horometro_final');
                                $controldiario->observaciones       = $request -> input('observaciones.'. $key);
                
                                $controldiario->save();  
            
                                event( new UserHasCreatedOrDeleted($controldiario->id,'controldiariovehiculo', auth()->user()->id,'crear'));
                                $x=date("Y-m-d",strtotime($x." +1 day"));
                            }
                        }
                    }else{
                        $x=$request->input('fechaInicial2');
                        while(strtotime($x)<=strtotime($request->input('fechaFinal2'))){
                            $controldiario = new Controldiariovehiculo();  
                            $controldiario->vehiculo_id           = $request -> input('idVehiculo');
                            if($request -> input('tipohora_id.'.$key) != 0){
                                $tipohoraDB = Tipohora::where('id',$request -> input('tipohora_id.'.$key)) ->get();
                                $controldiario->tipohora_id           = $tipohoraDB[0]->id;
                                
                            }
                            $uaDB =  Ua::where('codigo', $request -> input('ua_id.0')) -> get();
                            //$uaDB =  Ua::where('codigo', $request -> input('vehiculo_id')) -> get();
                            $controldiario->ua_id                 = $uaDB[0]->id;
            
            //                $controldiario->hora_inicio = $request -> input('hora_inicio.'. $key);
                            $controldiario->hora_total = 0;
                            //if($request -> input('hora_parada.'. $key)!=""){
                            //    $controldiario->hora_parada = $request -> input('hora_parada.'. $key);
                            //}else{
                                $controldiario->hora_parada = 1;
                            //}
            //              $controldiario->hora_fin    = $request -> input('hora_fin.'. $key);
                            $controldiario->fecha       = $x;
                            $controldiario->horometro_inicial       = $request -> input('horometro_inicial');
                            $controldiario->horometro_final       = $request -> input('horometro_final');
                            $controldiario->observaciones       = $request -> input('observaciones.'. $key);
            
                            $controldiario->save();  
        
                            event( new UserHasCreatedOrDeleted($controldiario->id,'controldiariovehiculo', auth()->user()->id,'crear'));
                            $x=date("Y-m-d",strtotime($x." +1 day"));
                        }
                    }
                }
            };              
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiariovehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $controldiario = Controldiariovehiculo::find($id);

     
/*        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
//        $cboUa += array('1' => '1543ua');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }        
*/		
        $thoras = Tipohora::orderBy('codigo','asc')->get();
        $cboThoras = array();
        $cboThoras += array('0' => 'Ninguno');
        foreach($thoras as $k=>$v){
            $cboThoras += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }

        $cboTurnos = array();
        $cboTurnos += array(1 => 'Diurno');
        $cboTurnos += array(0 => 'Nocturno');

        $entidad  = 'Controldiariovehiculo';
        $formData = array('controldiariovehiculo.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('controldiario', 'cboTurnos', 'formData', 'entidad', 'boton', 'listar','cboThoras'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiariovehiculo');
        if ($existe !== true) {
            return $existe;
        }
         $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                            'idEquipo'             => [new SearchVehiculo()],
//                            'idEquipo'              =>'required',
                            'tipohora_id.0'             => 'numeric',
                            'ua_id.0'               =>  [ 'required', new SearchUaPadre() ],
                            'turno'                 => 'boolean',
                            //'horometro_inicial'     => 'required|numeric',
                            //'horometro_final'       => 'required|numeric|gt:horometro_inicial',
                            //'hora_inicio.0'         => 'required',
                            'hora_total.0'          => 'required',
                            //'hora_fin.0'            => 'required', //|after:hora_inicio
                            'fecha'                 => 'date'
                        );
        $mensajes = array(
            'tipohora_id.0.numeric'               => 'Tipo de hora inválido',
            'turno.boolean'                   => 'Ingrese un turno válido',
            //'hora_inicio.0.required'              => 'Debe una hora de inicio válida',
            'hora_total.0.numeric'               => 'Debe ingresar la hora total valida',
            'hora_total.0.required'              => 'Debe ingresar la hora total',
            //'hora_fin.0.required'                 => 'Debe una hora de finalzación válida',
            //'horometro_inicial.required'          => 'Ingrese el horómetro inicial',
            //'horometro_final.required'            => 'Ingrese el horómetro final',
            //'horometro_inicial.numeric'           => 'Ingrese el horómetro inicial',
            //'horometro_final.numeric'             => 'Ingrese el horómetro final',
            //'horometro_final.gt'               => 'El horómetro final es menor que el inicial',
        //    'hora_fin.*.after'                  => 'Debe una hora de finalzación es menor que la hora de inicio',
            'fecha.date'                      => 'Debe ingresar una fecha válida'
            );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);

        $validacion->sometimes('ua_id',[ 'required' ,new SearchUaPadre() ], function($input){
        	return $input->tipohora_id == 0;
        });

        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){

           $controldiario =  Controldiariovehiculo::find($id);

           $controldiarioCop = json_encode($controldiario);

//            $equipoDB = Equipo::where('codigo',$request->input('equipo_id')) -> get();
//            $idEquipo = explode('--',$request->input('equipo_id'))[1];
//            $controldiario->equipo_id 	 		  = intval($idEquipo);
            $controldiario->vehiculo_id           = $request -> input('idVehiculo');
             if($request -> input('tipohora_id.0') != 0){
                    $tipohoraDB = Tipohora::where('id',$request -> input('tipohora_id.0')) ->get();
                    $controldiario->tipohora_id           = $tipohoraDB[0]->id;
                    
                }
            $uaDB =  Ua::where('codigo', $request -> input('ua_id.0')) -> get();
            $controldiario->ua_id                 = $uaDB[0]->id;

//	        $controldiario->hora_inicio = $request -> input('hora_inicio.0');
	        //$controldiario->hora_total  = $request -> input('hora_total.0');
	        //$controldiario->hora_parada  = $request -> input('hora_parada.0');
//            $controldiario->hora_fin 	= $request -> input('hora_fin.0');
            $controldiario->horometro_inicial       = $request -> input('horometro_inicial');
            $controldiario->horometro_final       = $request -> input('horometro_final');
	        $controldiario->fecha 		= $request ->input('fecha');
            $controldiario->observaciones       = $request -> input('observaciones.0');

            $controldiario->save();

            event(new UserHasEdited($controldiarioCop,$controldiario,'controldiariovehiculo',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiariovehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $controldiario = Controldiariovehiculo::find($id);

            event( new UserHasCreatedOrDeleted($controldiario->id,'controldiariovehiculo', auth()->user()->id,'crear'));
            $controldiario->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiariovehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Controldiariovehiculo::find($id);
        $entidad  = 'controldiariovehiculo';
        $formData = array('route' => array('controldiariovehiculo.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function autocomplete(){
        $uas = Ua::select('codigo','descripcion')->get();

        return response() -> json($uas);
    }

    private function consecionariaActual(){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function generateReport(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiario';
        $controldiario = null;

        $formData = array('controldiario.exportExcelReport');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $btnExcel    = 'Generar Reporte en Excel'; 
        $btnPdf    = 'Generar Reporte en Pdf'; 
        $cboContratista = array(''=>'Todos');
        $contratista = Contratista::orderBy('razonsocial','asc')->get();
        foreach($contratista as $k=>$v){
            $cboContratista = $cboContratista + array($v->id => $v->razonsocial);
        }
        
        return view($this->folderview.'.generate')->with(compact('controldiario' ,'formData', 'entidad', 'btnExcel', 'btnPdf', 'listar','cboContratista'));
    }

    public function generateReportMedicionVehiculo(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiariovehiculo';
        $controldiario = null;

        $formData = array(
            'route' => ['controldiariovehiculo.exportExcelReportMedicionVehiculo'], 
            'class' => 'form-horizontal', 
            'id' => 'formMantenimiento'.$entidad, 
            'autocomplete' => 'off'
        );
        
        $btnExcel    = 'Generar Reporte en Excel'; 
        $btnPdf    = 'Generar Reporte en Pdf'; 
        $cboContratista = array(''=>'Todos');
        $contratista = Contratista::orderBy('razonsocial','asc')->get();
        foreach($contratista as $k=>$v){
            $cboContratista = $cboContratista + array($v->id => $v->razonsocial);
        }
        
        return view($this->folderview.'.generate_report_medicion_vehiculo')->with(compact('controldiario' ,'formData', 'entidad', 'btnExcel', 'btnPdf', 'listar', 'cboContratista'));
    }

    public function getVehiculos(Request $request)
    {
        $concesionaria = Concesionaria::getConcesionariaActual()['id'];

        // $start_date = (new DateTime($request->start_date))->format('Y-m-d');
        // $end_date = (new DateTime($request->end_date))->format('Y-m-d');
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $contratista_id = $request->contratista_id;
        $ua = trim($request->ua_id);

        $dbEquipos = Vehiculo::join('controldiariovehiculo', 'vehiculo.id', '=', 'controldiariovehiculo.vehiculo_id')
                        ->join('ua','ua.id','=','vehiculo.ua_id')
                        ->join('contratista','contratista.id','=','vehiculo.contratista_id')
                        ->select('vehiculo.id AS id', DB::raw('concat(vehiculo.modelo,\' | \',vehiculo.placa,\' | \',contratista.razonsocial,\' (\',vehiculo.precio,\')\') AS descripcion'))->distinct()
                        ->where('controldiariovehiculo.fecha', '>=', $startDate)
                        ->where('controldiariovehiculo.fecha', '<=', $endDate)
                        ->where(function($sql) use($contratista_id){
                            if($contratista_id!=""){
                                $sql->where('vehiculo.contratista_id','=',$contratista_id);
                            }     
                        })
                        ->where(function($sql) use($ua){
                            if($ua!=""){
                                $sql->where('ua.codigo','like',$ua);
                            }
                        })
                        ->orderBy('vehiculo.modelo', 'asc')->get();
        
        return [
            'listEquipos' => $dbEquipos
        ];
    }


    public function exportExcelReportMedicionVehiculo(Request $request)
    {
        if ( is_null($request->input('start_date')) || is_null($request->input('end_date')) ) return;

        $start_date = new DateTime($request->input('start_date'));
        $end_date = new DateTime($request->input('end_date'));
        $periodo = $request->input('periodo');
        $equipo_ids = $request->input('equipo');
        $reporte = $request->input('reporte');
        $typeExport = $request->input('typeExport');

        if ( $typeExport === 'excel' ) {
            if ( $reporte === 'detallado' ) {
                return (new ExcelReportDetalladoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->download('report_detallado.xlsx');
            } else if ( $reporte === 'consolidado' ) {
                return (new ExcelReportConsolidadoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->download('report_consolidado.xlsx');
            } else if ( $reporte === 'todos' ) {
                return (new ExcelReportDetalladoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->download('report_detallado.xlsx');
            } else{
                throw new Exception("Value of (\$reporte = $reporte) don't found");
            }
        } else if ( $typeExport === 'pdf' ) {
            $this->exportPdfReport($reporte, $start_date, $end_date, $equipo_ids, $periodo);
        } else {
            throw new Exception("Value of (\$typeExport = $typeExport) don't found");
        }
    }
    
    public function exportPdfReport(string $reporte, DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo)
    {
        if ( $reporte === 'detallado' ) {
            $this->exportPdfReportDetallado($start_date, $end_date, $equipo_ids, $periodo);
        } else if ( $reporte === 'consolidado' ) {
            $this->exportPdfReportConsolidado($start_date, $end_date, $equipo_ids, $periodo);
        } else if( $reporte === 'todos'){
            $this->exportPdfReportTodos($start_date, $end_date, $equipo_ids, $periodo);
        } else{
            throw new Exception("Value of (\$reporte = $reporte) don't found");
        } 
    }

    public function exportPdfReportDetallado(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo) 
    {
        $data = (new PdfReportDetalladoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->getData();

        $data['namefile'] = "Reporte Detalladado Vehiculo - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Vehiculo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.detallado", $data)->render();

        $mpdf = new Mpdf(['orientation' => "L", 'tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data['namefile'], "I");
    }

    public function exportPdfReportConsolidado(DateTime $start_date, DateTime $end_date, array $equipo_ids, $periodo) 
    {
        $data = (new PdfReportConsolidadoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->getData();

        $data['namefile'] = "Reporte Consolidado Vehiculo - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Vehiculo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.consolidado", $data)->render();

        $mpdf = new Mpdf(['orientation' => "L", 'tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data["namefile"], "I");
    }
    
    public function exportPdfReportTodos(DateTime $start_date, DateTime $end_date, array $equipo_ids, string $periodo) 
    {
        $data = (new PdfReportConsolidadoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->getData();

        $data['namefile'] = "Reporte Consolidado Vehiculo - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Vehiculo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.consolidado", $data)->render();

        $mpdf = new Mpdf(['orientation' => "L", 'tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);
        $mpdf->AddPage();
        //
        $data = (new PdfReportDetalladoVehiculo($start_date, $end_date, $equipo_ids, $periodo))->getData();

        $data['namefile'] = "Reporte Detalladado Vehiculo - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Vehiculo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.detallado", $data)->render();
        $mpdf->WriteHTML($html);

        $mpdf->Output($data["namefile"], "I");
    }

    public function exportPdfReport_HorasTrabajadas(DateTime $start_date, DateTime $end_date,$contratista_id,$ua)
    {
        $data = (new PdfReport_HorasTrabajadas($start_date, $end_date, $contratista_id , $ua))->getData();

        $data['namefile'] = "Reporte Horas Trabajadas - ".time().".pdf";
        
        $html = view($this->folderview . ".exports.pdfReports.HorasTrabajadas", $data)->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data["namefile"], "I");
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
