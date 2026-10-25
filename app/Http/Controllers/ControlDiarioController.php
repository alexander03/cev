<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Validator;
use App\Equipo;
use App\Area;
use App\Tipohora;
use App\Ua;
use App\Concesionaria;
use App\Controldiario;
use App\Configuracion;
use App\Contratista;
use App\Exports\ReportHrsEquiposUas;
use App\Exports\ReportHrsEquiposUasVertical;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre;
use App\Rules\SearchEquipo;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Exports\ExcelReport_HorasTrabajadas;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use DateTime;
use App\Exports\ExcelReport_ByA;
use App\Exports\ExcelReport_JyMP;
use App\Exports\TransporteExport;
use App\Exports\EquipodiaExport;
use Exception;
use Mpdf\Mpdf;
use App\Exports\PdfReport_ByA;
use App\Exports\PdfReport_JyMP;
use App\Exports\PdfReport_HorasTrabajadas;

class ControlDiarioController extends Controller
{
    protected $folderview      = 'app.controldiario';
    protected $tituloAdmin     = 'Control diario de equipos';
    protected $tituloRegistrar = 'Registrar Control Diario de Equipos';
    protected $tituloModificar = 'Modificar Control Diario de Equipos';
    protected $tituloEliminar  = 'Eliminar Control Diario de Equipos';
    protected $tituloGenerar  = 'Generar reporte de Horas Trabajadas';
    protected $tituloGenerarReporteMedicionEquipos  = 'Generar reporte de Medicion de Equipos';
    protected $rutas           = array('create' => 'controldiario.create', 
            'edit'           => 'controldiario.edit', 
            'detail'           => 'controldiario.detail', 
            'delete'         => 'controldiario.eliminar',
            'search'         => 'controldiario.buscar',
            'index'          => 'controldiario.index',
            'generateReport' => 'controldiario.generateReport',
            'generateReportMedicionEquipos' => 'controldiario.generateReportMedicionEquipos'
        );

       /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 1)->first()->valor));
    }


    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Controldiario';

        $ua     	  = Libreria::getParam($request->input('ua'));
        $equipo_ua      = Libreria::getParam($request->input('equipo_ua'));
  		$descripcion = Libreria::getParam($request->input('descripcion'));
  		$concesionaria = $this->consecionariaActual();

        $fecha1 = Libreria::getParam($request->input('fecha_registro_inicial'));
        $fecha2 = Libreria::getParam($request->input('fecha_registro_final'));
        $filtro           = array();
        $filtro[]         = ['codigo', 'LIKE', '%'.strtoupper($ua).'%'];
        $filtro[]         = ['codigo', 'LIKE', '%'.strtoupper($equipo_ua).'%'];
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];
        $filtro[]         = ['concesionaria_id','=',$this->consecionariaActual()];
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/       
        /*$resultado        = Controldiario::where(function($subquery) use ($fecha1, $fecha2) {
                                            if ( $fecha1 !== null ) $subquery->where('fecha', '>=', $fecha1);
                                            if ( $fecha2 !== null ) $subquery->where('fecha', '<=', $fecha2);
                                        })->whereHas('ua', function($query) use($filtro){
        									$query->where($filtro[0][0],$filtro[0][1],$filtro[0][2])->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
        								})->WhereHas('equipo', function($query) use($filtro){
        									$query->whereHas('ua', function($query) use($filtro){
                                                $query->where($filtro[1][0],$filtro[1][1],$filtro[1][2]);
                                            })->orWhere($filtro[2][0],$filtro[2][1],$filtro[2][2]);
        								})
                                        ->WhereHas('equipo', function($query) use($filtro){
                                            $query->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
                                        })->orderBy('fecha', 'desc');*/
        
        $resultado = Controldiario::join('equipo','equipo.id','=','controldiario.equipo_id')
                                    ->where(function($subquery) use ($fecha1, $fecha2) {
                                            if ( $fecha1 !== null ) $subquery->where('fecha', '>=', $fecha1);
                                            if ( $fecha2 !== null ) $subquery->where('fecha', '<=', $fecha2);
                                        })
                                    ->where(function($query) use($ua,$concesionaria){
                                        if($ua!=""){
                                            $query->whereIn('controldiario.ua_id',function($sql) use($ua,$concesionaria){
                                                $sql->select("id")
                                                    ->from("ua")
                                                    ->where("codigo","like","%".$ua."%")
                                                    ->where("concesionaria_id","=",$concesionaria);
                                            });
                                        }  
                                    })->where(function($query) use($equipo_ua,$concesionaria){
                                        if($equipo_ua!=""){
                                            $query->whereIn('equipo.ua_id',function($sql) use($equipo_ua,$concesionaria) {
                                                $sql->select("id")
                                                    ->from("ua")
                                                    ->where("codigo","like","%".$equipo_ua."%")
                                                    ->where("concesionaria_id","=",$concesionaria);
                                            });
                                        }  
                                    })->where(function ($query) use($descripcion){
                                        if($descripcion!=""){
                                            $query->where('equipo.descripcion','like','%'.$descripcion.'%');
                                        }
                                    })
                                    /*->WhereHas('equipo', function($query) use($filtro){
                                            $query->where($filtro[3][0],$filtro[3][1],$filtro[3][2]);
                                        })*/->select('controldiario.*')->orderBy('fecha', 'desc');
        
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua equipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Desc equipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Subcontratista', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horas Trabajadas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo de Hora Parada', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horas Parada', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Turno', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horómetro inicial', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horómetro final', 'numero' => '1');
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
		$cabecera[]       = array('valor' => 'Detalle', 'numero' => '1');
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Controldiario';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_generar   = $this->tituloGenerar;
        $titulo_generar_reporte_medicion_equipos   = $this->tituloGenerarReporteMedicionEquipos;
        $ruta             = $this->rutas;
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'titulo_generar', 'titulo_generar_reporte_medicion_equipos','ruta','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiario';
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
        $cboThoras += array('0' => 'Horas Paradas');
        foreach($thoras as $k=>$v){
            $cboThoras += array($v->id => $v->descripcion . ' - ' .$v->codigo);
        }

        $cboTurnos = array();
        $cboTurnos += array(1 => 'Diurno');
        $cboTurnos += array(0 => 'Nocturno');

        $formData = array('controldiario.store');
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

                            'idEquipo' 			=> [new SearchEquipo()],
//                            'idEquipo'              => 'required',
    						'tipohora_id.*' 			=> 'numeric',
                            //'ua_id.*'               =>  [ 'required', new SearchUaPadre() ],
                            'turno'                 => 'boolean',
                            'horometro_inicial'     => 'required|numeric',
                            'horometro_final'       => 'required|numeric',//|gt:horometro_inicial
    						//'hora_inicio.*'			=> 'required',
                            //'hora_total.*'          => 'required|numeric',
    						//'hora_fin.*'		    => 'required', //|after:hora_inicio
    						'fecha'  				=> 'date'
                        );
        $mensajes = array(
            'tipohora_id.*.numeric'  	  		  => 'Tipo de hora inválido',
            'turno.boolean'                       => 'Ingrese un turno válido',
            //'hora_inicio.*.required'			  => 'Debe ingresar una hora de inicio válida',
            //'hora_total.*.required'               => 'Debe ingresar la hora total',
            //'hora_total.*.numeric'               => 'Debe ingresar la hora total valida',
            //'hora_fin.*.required'    			  => 'Debe ingresar una hora de finalzación válida',
            'horometro_inicial.required'          => 'Ingrese el horómetro inicial',
            'horometro_final.required'            => 'Ingrese el horómetro final',
            'horometro_inicial.numeric'             => 'Ingrese el horómetro inicial',
            'horometro_final.numeric'               => 'Ingrese el horómetro final',
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
                    $controldiario = new Controldiario();  
                    $controldiario->equipo_id           = $request -> input('idEquipo');
                    if($request -> input('tipohora_id.'.$key) != 0){
                        $tipohoraDB = Tipohora::where('id',$request -> input('tipohora_id.'.$key)) ->get();
                        $controldiario->tipohora_id           = $tipohoraDB[0]->id;
                        
                    }
    
    //                $controldiario->hora_inicio = $request -> input('hora_inicio.'. $key);
                    if($request -> input('hora_total.'. $key)!=""){
                        $controldiario->hora_total = $request -> input('hora_total.'. $key);
                        $uaDB =  Ua::where('codigo', $request -> input('ua_id.'.$key)) -> get();
                        $controldiario->ua_id                 = $uaDB[0]->id;
                    }else{
                        $controldiario->hora_total = 0;
                    }
                    if($request -> input('hora_parada.'. $key)!=""){
                        $controldiario->hora_parada = $request -> input('hora_parada.'. $key);
                        $uaDB =  Ua::where('codigo', $request -> input('equipo_id')) -> get();//codigo ua equipo
                        $controldiario->ua_id                 = $uaDB[0]->id;
                    }else{
                        $controldiario->hora_parada = 0;
                    }
    //              $controldiario->hora_fin    = $request -> input('hora_fin.'. $key);
                    $controldiario->fecha       = $request -> input('fecha');
                    $controldiario->horometro_inicial       = $request -> input('horometro_inicial');
                    $controldiario->horometro_final       = $request -> input('horometro_final');
                    $controldiario->turno       = $request -> input('turno');
                    $controldiario->viajes       = $request -> input('viajes.'. $key);
                    $controldiario->km_inicial       = $request -> input('km_inicial.'. $key);
                    $controldiario->acceso_origen       = $request -> input('acceso_origen.'. $key);
                    $controldiario->km_destino       = $request -> input('km_destino.'. $key);
                    $controldiario->acceso_destino       = $request -> input('acceso_destino.'. $key);
                    $controldiario->tipo_material       = $request -> input('tipo_material.'. $key);
                    $controldiario->observaciones       = $request -> input('observaciones.'. $key);
    
                    if(!is_null($request -> input('uaorigen.'.$key))){
                        $uaDB =  Ua::where('codigo','like', $request -> input('uaorigen.'.$key)) -> get();
                        if(!is_null($uaDB)){
                            $controldiario->uaorigen_id = $uaDB[0]->id;
                        }else{
                            $controldiario->uaorigen_id = 0;
                        }
                    }else{
                        $controldiario->uaorigen_id = 0;
                    }
                    if(!is_null($request -> input('uadestino.'.$key))){
                        $uaDB =  Ua::where('codigo','like', $request -> input('uadestino.'.$key)) -> get();
                        if(!is_null($uaDB)){
                            $controldiario->uadestino_id = $uaDB[0]->id;
                        }else{
                            $controldiario->uadestino_id = 0;
                        }
                    }else{
                        $controldiario->uadestino_id = 0;
                    }
                    
                    $controldiario->save();

                    $equipo = Equipo::find($controldiario->equipo_id);
                    if(($equipo->horometroactual+0)<$controldiario->horometro_final){
                        $equipo->horometro_rec =  $controldiario->horometro_final - $equipo->horometroactual;
                        $equipo->horometroactual = $controldiario->horometro_final;
                        $equipo->save();
                    }
    
                    event( new UserHasCreatedOrDeleted($controldiario->id,'controldiario', auth()->user()->id,'crear'));
                    
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
    
    public function detail(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiario';
        $controldiario = Controldiario::find($request->input('id'));
        
        $detail = Controldiario::where('fecha','=',$controldiario->fecha)
                                ->where('equipo_id','=',$controldiario->equipo_id)
                                ->get();

        $thoras = Tipohora::orderBy('codigo','asc')->get();
        $cboThoras = array();
        $cboThoras += array('0' => 'Horas Paradas');
        foreach($thoras as $k=>$v){
            $cboThoras += array($v->id => $v->descripcion . ' - ' .$v->codigo);
        }

        $cboTurnos = array();
        $cboTurnos += array(1 => 'Diurno');
        $cboTurnos += array(0 => 'Nocturno');

        $formData = array('controldiario.store2',$request->input('id'));
        $formData = array('route' => $formData, 'method' => 'STORE2', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', "style"=>"background: #87ceeb4a;");
        $boton    = 'Actualizar'; 
        return view($this->folderview.'.detail')->with(compact('controldiario', 'cboTurnos' ,'formData', 'entidad', 'boton', 'listar','cboThoras','detail'));
    }
    
    public function store2(Request $request){
        $controldiario = Controldiario::find($request->input('id'));
        $detail = Controldiario::where('fecha','=',$controldiario->fecha)
                                ->where('equipo_id','=',$controldiario->equipo_id)
                                ->get();
        $error = DB::transaction(function() use($request, $detail){
            foreach($detail as $key => $value){
                $control = Controldiario::find($value->id);
                $controldiarioCop = json_encode($control);
                
                $control->fecha=$request->input('fecha');
                $control->save();
                event(new UserHasEdited($controldiarioCop,$control,'controldiario',auth()->user()->id));
            }
        });
        
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiario');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $controldiario = Controldiario::find($id);

     
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
        $cboThoras += array('0' => 'Horas Paradas');
        foreach($thoras as $k=>$v){
            $cboThoras += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }

        $cboTurnos = array();
        $cboTurnos += array(1 => 'Diurno');
        $cboTurnos += array(0 => 'Nocturno');

        $entidad  = 'Controldiario';
        $formData = array('controldiario.update', $id);
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
        $existe = Libreria::verificarExistencia($id, 'controldiario');
        if ($existe !== true) {
            return $existe;
        }
         $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                            'idEquipo'             => [new SearchEquipo()],
//                            'idEquipo'              =>'required',
                            'tipohora_id.0'             => 'numeric',
                            'ua_id.0'               =>  [ 'required', new SearchUaPadre() ],
                            'turno'                 => 'boolean',
                            'horometro_inicial'     => 'required|numeric',
                            'horometro_final'       => 'required|numeric',//|gt:horometro_inicial
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
            'horometro_inicial.required'          => 'Ingrese el horómetro inicial',
            'horometro_final.required'            => 'Ingrese el horómetro final',
            'horometro_inicial.numeric'           => 'Ingrese el horómetro inicial',
            'horometro_final.numeric'             => 'Ingrese el horómetro final',
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

           $controldiario =  Controldiario::find($id);

           $controldiarioCop = json_encode($controldiario);

//            $equipoDB = Equipo::where('codigo',$request->input('equipo_id')) -> get();
//            $idEquipo = explode('--',$request->input('equipo_id'))[1];
//            $controldiario->equipo_id 	 		  = intval($idEquipo);
            $controldiario->equipo_id           = $request -> input('idEquipo');
             if($request -> input('tipohora_id.0') != 0){
                    $tipohoraDB = Tipohora::where('id',$request -> input('tipohora_id.0')) ->get();
                    $controldiario->tipohora_id           = $tipohoraDB[0]->id;
                    
                }
            $uaDB =  Ua::where('codigo', $request -> input('ua_id.0')) -> get();
            $controldiario->ua_id                 = $uaDB[0]->id;

//	        $controldiario->hora_inicio = $request -> input('hora_inicio.0');
	        $controldiario->hora_total  = $request -> input('hora_total.0');
	        $controldiario->hora_parada  = $request -> input('hora_parada.0');
//            $controldiario->hora_fin 	= $request -> input('hora_fin.0');
            $controldiario->horometro_inicial       = $request -> input('horometro_inicial');
            $controldiario->horometro_final       = $request -> input('horometro_final');
	        $controldiario->fecha 		= $request ->input('fecha');
            $controldiario->turno       = $request -> input('turno');
            $controldiario->viajes       = $request -> input('viajes.0');
            $controldiario->km_inicial       = $request -> input('km_inicial.0');
            $controldiario->acceso_origen       = $request -> input('acceso_origen.0');
            $controldiario->km_destino       = $request -> input('km_destino.0');
            $controldiario->acceso_destino       = $request -> input('acceso_destino.0');
            $controldiario->tipo_material       = $request -> input('tipo_material.0');
            $controldiario->observaciones       = $request -> input('observaciones.0');
            if(!is_null($request -> input('uaorigen.0'))){
                $uaDB =  Ua::where('codigo', 'like', $request -> input('uaorigen.0')) -> get();
                if(!is_null($uaDB)){
                    $controldiario->uaorigen_id = $uaDB[0]->id;
                }else{
                    $controldiario->uaorigen_id = 0;
                }
            }else{
                $controldiario->uaorigen_id = 0;
            }
            if(!is_null($request -> input('uadestino.0'))){
                $uaDB =  Ua::where('codigo', 'like', $request -> input('uadestino.0')) -> get();
                if(!is_null($uaDB)){
                    $controldiario->uadestino_id = $uaDB[0]->id;
                }else{
                    $controldiario->uadestino_id = 0;
                }
            }else{
                $controldiario->uadestino_id = 0;
            }
            
            $controldiario->save();

            $equipo = Equipo::find($controldiario->equipo_id);
            if(($equipo->horometroactual+0)<$controldiario->horometro_final){
                $equipo->horometro_rec =  $controldiario->horometro_final - $equipo->horometroactual;
                $equipo->horometroactual = $controldiario->horometro_final;
                $equipo->save();
            }

            event(new UserHasEdited($controldiarioCop,$controldiario,'controldiario',auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'controldiario');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $controldiario = Controldiario::find($id);

            event( new UserHasCreatedOrDeleted($controldiario->id,'controldiario', auth()->user()->id,'crear'));
            $controldiario->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'controldiario');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Controldiario::find($id);
        $entidad  = 'Controldiario';
        $formData = array('route' => array('controldiario.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function autocomplete(){
        $uas = Ua::select('codigo','descripcion')->get();

        return response() -> json($uas);
    }

    public function HEquipoxUa(Request $request){
        $concesionaria = $this->consecionariaActual();

        $fecha1 = $request->fecha_registro_inicial;
        $fecha2 = $request->fecha_registro_final;
        $equipo_ua = $request->equipo_ua;

        
        return Excel::download(new ReportHrsEquiposUas($fecha1,$fecha2,$equipo_ua,$concesionaria), 'ReportH.EQ.X.UA.xlsx'); 
    }

    public function HEquipoxUaVertical(Request $request){
        $concesionaria = $this->consecionariaActual();

        $fecha1 = $request->fecha_registro_inicial;
        $fecha2 = $request->fecha_registro_final;
        $equipo_ua = $request->equipo_ua;

        
        return Excel::download(new ReportHrsEquiposUasVertical($fecha1,$fecha2,$equipo_ua,$concesionaria), 'ReportH.EQ.X.UA-Vertical.xlsx'); 
    }
    
    public function HEquipoxUapdf(Request $request){
        $concesionaria = $this->consecionariaActual();

        $fechaInicio = $request->fecha_registro_inicial;
        $fechaFin = $request->fecha_registro_final;
        $equipo_ua = $request->equipo_ua;

        $lista = new ReportHrsEquiposUas($fechaInicio,$fechaFin,$equipo_ua,$concesionaria);

        $collectionResult = $lista->collection();

        //días y total
        while($fechaInicio != $fechaFin){
            
            //$fechas[] = $fechaInicio;
            $fechas[] = substr($fechaInicio,8,2)."/".substr($fechaInicio,5,2);

            $fechaInicio = date("Y-m-d",strtotime($fechaInicio."+ 1 day"));
        }
        //$fechas[] = $fechaInicio;
        $fechas[] = substr($fechaInicio,8,2)."/".substr($fechaInicio,5,2);

        $fechas[] = 'Total';

        $data['data'] = $collectionResult;
        $data['fechas'] = $fechas;
        $html = view('app.controldiario.pdf.h_equixua', $data)->render();
        $mpdf = new Mpdf(['orientation' => 'L']);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_HxEquipoxUa.pdf', "I");

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

    public function generateReportMedicionEquipos(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiario';
        $controldiario = null;

        $formData = array(
            'route' => ['controldiario.exportExcelReportMedicionEquipos'], 
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
        
        return view($this->folderview.'.generate_report_medicion_equipos')->with(compact('controldiario' ,'formData', 'entidad', 'btnExcel', 'btnPdf', 'listar', 'cboContratista'));
    }

    public function getEquipos(Request $request)
    {
        $concesionaria = Concesionaria::getConcesionariaActual()['id'];

        // $start_date = (new DateTime($request->start_date))->format('Y-m-d');
        // $end_date = (new DateTime($request->end_date))->format('Y-m-d');
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $contratista_id = $request->contratista_id;
        $ua = trim($request->ua_id);

        $dbEquipos = Equipo::join('controldiario', 'equipo.id', '=', 'controldiario.equipo_id')
                        ->join('ua','ua.id','=','equipo.ua_id')
                        ->select('equipo.id AS id', 'equipo.descripcion AS descripcion','ua.codigo')->distinct()
                        ->where('controldiario.fecha', '>=', $startDate)
                        ->where('controldiario.fecha', '<=', $endDate)
                        ->where(function($sql) use($contratista_id){
                            if($contratista_id!=""){
                                $sql->where('equipo.contratista_id','=',$contratista_id);
                            }     
                        })
                        ->where(function($sql) use($ua){
                            if($ua!=""){
                                $sql->where('ua.codigo','like',$ua);
                            }
                        })
                        ->orderBy('equipo.descripcion', 'asc')->get();
        
        return [
            'listEquipos' => $dbEquipos
        ];
    }

    public function exportExcelReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $typeReport = $request->input('typeExport');
        $contratista = $request->input('contratista');
        $uaPadre = $request->input('ua_padre_id');

        if ( is_null($start_date) || is_null($end_date) || is_null($typeReport) ) throw new Exception('Some input on exportExcelReport method is null');

        $dates = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'contratista_id' => $contratista,
            'ua_padre' => $uaPadre
        ];

        if ( $typeReport === 'excel' ) {
            return (new ExcelReport_HorasTrabajadas($dates))->download('ReporteHorasTrabajadas.xlsx');
        } else if ( $typeReport === 'pdf' ) {
            
            $this->exportPdfReport_HorasTrabajadas(new DateTime($start_date), new DateTime($end_date), $contratista, $uaPadre);
        } else {
            throw new Exception("On exportExcelReport method, the value of \$typeExport has not found");
        }
    }

    public function exportExcelReportMedicionEquipos(Request $request)
    {
        if ( is_null($request->input('start_date')) || is_null($request->input('end_date')) ) return;

        $start_date = new DateTime($request->input('start_date'));
        $end_date = new DateTime($request->input('end_date'));
        $periodo = $request->input('periodo');
        $equipo_ids = $request->input('equipo');
        $reporte = $request->input('reporte');
        $typeExport = $request->input('typeExport');
        $fechas = array();
        for ($i=0; $i < count($equipo_ids); $i++) { 
            $fechas[$equipo_ids[$i]] = (strtotime($request->input('endDate_equipo'.$equipo_ids[$i])) - strtotime($request->input('startDate_equipo'.$equipo_ids[$i])))/86400 + 1;
        }
        if ( $typeExport === 'excel' ) {
            if ( $reporte === 'detallado' ) {
                return (new ExcelReport_ByA($start_date, $end_date, $equipo_ids, $periodo, $fechas))->download('report_detallado.xlsx');
            } else if ( $reporte === 'consolidado' ) {
                return (new ExcelReport_JyMP($start_date, $end_date, $equipo_ids, $periodo, $fechas))->download('report_consolidado.xlsx');
            } else {
                throw new Exception("Value of (\$reporte = $reporte) don't found");
            }
        } else if ( $typeExport === 'pdf' ) {
            $this->exportPdfReport($reporte, $start_date, $end_date, $equipo_ids, $periodo, $fechas);
        } else {
            throw new Exception("Value of (\$typeExport = $typeExport) don't found");
        }
    }
    
    public function exportPdfReport(string $reporte, DateTime $start_date, DateTime $end_date, array $equipo_ids = [],string $periodo, array $fechas)
    {
        if ( $reporte === 'detallado' ) {
            $this->exportPdfReport_ByA($start_date, $end_date, $equipo_ids, $periodo, $fechas);
        } else if ( $reporte === 'consolidado' ) {
            $this->exportPdfReport_JyMP($start_date, $end_date, $equipo_ids, $periodo, $fechas);
        } else {
            throw new Exception("Value of (\$reporte = $reporte) don't found");
        }
    }

    public function exportPdfReport_ByA(DateTime $start_date, DateTime $end_date, array $equipo_ids = [], string $periodo, array $fechas) 
    {
        $data = (new PdfReport_ByA($start_date, $end_date, $equipo_ids, $periodo, $fechas))->getData();

        $data['namefile'] = "Reporte Detalladado - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Equipo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.detallado", $data)->render();
        //echo $html;
        $mpdf = new Mpdf(['orientation' => "L"]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data['namefile'], "I");
    }

    public function exportPdfReport_JyMP(DateTime $start_date, DateTime $end_date, array $equipo_ids = [], string $periodo, array $fechas) 
    {
        $data = (new PdfReport_JyMP($start_date, $end_date, $equipo_ids, $periodo, $fechas))->getData();

        $data['namefile'] = "Reporte Consolidado - ".time().".pdf";
        $data['concesionaria_id'] = $this->getConsecionariaActual();
        
        $equipo = Equipo::whereIn('id',$equipo_ids)->first();
        $concesionaria = Concesionaria::find($data['concesionaria_id']);
        
        $data['subcontratista'] = $equipo->contratista->razonsocial;
        $data['concesionaria'] = $concesionaria->razonsocial;
        
        $html = view($this->folderview . ".exports.pdfReports.consolidado", $data)->render();

        $mpdf = new Mpdf(['orientation' => "L"]);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data["namefile"], "I");
    }

    public function exportPdfReport_HorasTrabajadas(DateTime $start_date, DateTime $end_date,$contratista_id,$ua)
    {
        $data = (new PdfReport_HorasTrabajadas($start_date, $end_date, $contratista_id , $ua))->getData();
        if($contratista_id==""){
            $nombre = "TODOS";
        }else{
            $nombre = Contratista::find($contratista_id)->razonsocial;
        }

        $data['namefile'] = "Reporte Horas Trabajadas - ".time().".pdf";
        $data['nombre'] = $nombre;
        
        $html = view($this->folderview . ".exports.pdfReports.HorasTrabajadas", $data)->render();
        //echo $html;die();
        $mpdf = new Mpdf(['orientation' => 'L']);
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->WriteHTML($html);

        $mpdf->Output($data["namefile"], "I");
    }
    
    public function transporteExcel(Request $request){
        $fecha1 = $request->fecha_registro_inicial;
        $fecha2 = $request->fecha_registro_final;

        
        return Excel::download(new TransporteExport($fecha1,$fecha2), 'ReporteTransporte.xlsx'); 
    }
    
    public function equipodiaExcel(Request $request){
        $fecha1 = $request->fecha_registro_inicial;
        $fecha2 = $request->fecha_registro_final;

        
        return Excel::download(new EquipodiaExport($fecha1,$fecha2), 'ReporteDiarioUA.xlsx'); 
    }
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function horometroFinal(Request $request){
        $control = Controldiario::join('equipo','equipo.id','=','controldiario.equipo_id')
                    ->join('ua','ua.id','=','equipo.ua_id')
                    ->where('fecha','<=',$request->input('fecha'))
                    ->where('ua.codigo','like',$request->input('equipo_id'))
                    ->orderBy('fecha','desc')
                    ->first();
        if(!is_null($control)){
            echo $control->horometro_final;
        }else{
            echo "0";
        }
    }
}
