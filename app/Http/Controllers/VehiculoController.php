<?php

namespace App\Http\Controllers;
use Validator;
use App\Vehiculo;
use App\Ua;
use App\Rules\SearchUaPadre;
use App\Area;
use App\Kilometraje;
use App\Brand;
use App\Carroceria;
use App\Contratista;
use App\Concesionaria;
use App\Tipocombustible;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehiculoExport;
use App\Exports\VehiculoDocExport;
use App\Exports\VehiculoDoc2Export;

class VehiculoController extends Controller
{
    protected $folderview      = 'app.vehiculo';
    protected $tituloAdmin     = 'Vehiculo';
    protected $tituloRegistrar = 'Registrar vehiculo';
    protected $tituloModificar = 'Modificar vehiculo';
    protected $tituloEliminar  = 'Eliminar vehiculo';
    protected $rutas           = array('create' => 'vehiculo.create', 
            'edit'   => 'vehiculo.edit', 
            'delete' => 'vehiculo.eliminar',
            'search' => 'vehiculo.buscar',
            'index'  => 'vehiculo.index',
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
        $entidad          = 'Vehiculo';

        $ua     	  = Libreria::getParam($request->input('ua'));
        $placa      = Libreria::getParam($request->input('placa'));
        $estado      = Libreria::getParam($request->input('estado'));
        $documentos      = Libreria::getParam($request->input('documentos'));

        $filtro           = array();
//        $filtro[]         = ['ua', 'LIKE', '%'.strtoupper($ua).'%'];
        $filtro[]         = ['placa', 'LIKE', '%'.strtoupper($placa).'%'];
        $filtro[]         = ['concesionaria_id', $this->concesionariaActual()];
        $filtro[]         = ['estado', 'LIKE', '%'.$estado.'%'];
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/
        $resultado        = Vehiculo::where($filtro)->whereHas('ua', function($query) use($ua){
                                $query->where('codigo', 'LIKE', '%'.strtoupper($ua).'%');
                            })->where(function($query) use($documentos){
                                if($documentos!=""){
                                    if($documentos=="SI"){
                                        $query->whereRaw('(select count(*) from vehiculodocument where vehiculo_id=vehiculo.id and deleted_at is null and YEAR(fecha)-1 = '.date("Y").')>0');
                                    }else{
                                        $query->whereRaw('(select count(*) from vehiculodocument where vehiculo_id=vehiculo.id and deleted_at is null and YEAR(fecha)-1 = '.date("Y").')=0');
                                    }
                                }
                            })->orderBy('modelo', 'ASC');

        $lista            = $resultado->select('vehiculo.*',DB::raw('(select count(*) from vehiculodocument where vehiculo_id=vehiculo.id and deleted_at is null and YEAR(fecha)-1 = '.date("Y").') as documentos'))->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Editar', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Ua', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Marca', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Año de Fbr', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Motor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Subcontratista/Propio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Area', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Asientos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Chasis', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Carrocería', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Color', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Regla', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
/*
        $cabecera[]       = array('valor' => 'Vencimiento SOAT', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Vencimiento GPS', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Vencimiento RTV', 'numero' => '1');
*/
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        }
        
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
        $entidad          = 'Vehiculo';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
/*
        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }
*/
        $cboEstado = array(''=>'Todos','Activo'=>'Activo','Desmovilizado'=>'Desmovilizado','Temporal'=>'Temporal');
        $cboDocumentos = array(''=>'Todos','SI'=>'SI','NO'=>'NO');
        $cboSaltarkm = array('N'=>'No',"S"=>"SI");
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboEstado','cboDocumentos','cboSaltarkm','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Vehiculo';
        $vehiculo = null;

        $marcas = Brand::orderBy('descripcion','asc')->get();
        $cboMarca = array();
        $cboMarca += array('0' => 'Selecione marca');
        foreach($marcas as $k=>$v){
            $cboMarca += array($v->id=>$v->descripcion);
        }

        $areas = Area::orderBy('descripcion','asc')->get();
        $cboArea = array();
        $cboArea += array('0' => 'Selecione área');
        foreach($areas as $k=>$v){
            $cboArea += array($v->id=>$v->descripcion);
        }

        $carrocerias = Carroceria::orderBy('descripcion','asc')->get();
        $cboCarroceria = array();
        foreach($carrocerias as $k=>$v){
            $cboCarroceria += array($v->id=>$v->descripcion);
        }


        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        
        $kilometraje = Kilometraje::orderBy('descripcion','asc')->get();
        $cboKilometraje = array();
        $cboKilometraje += array('0' => 'Selecione regla');
        foreach($kilometraje as $k=>$v){
            $cboKilometraje += array($v->id=>$v->descripcion . ':  ' .  $v->limite_inf . '-' . $v->limite_sup);
        }
        
        $tipocombustible = Tipocombustible::orderBy('descripcion','asc')->get();
        $cboTipocombustible = array();
        $cboTipocombustible += array('0' => 'Selecione tipo combustible');
        foreach($tipocombustible as $k=>$v){
            $cboTipocombustible += array($v->id=>$v->descripcion);
        }
/*
        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
//        $cboUa += array('1' => '12345ua');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo );
        }
*/
        $cboEstado = array('Activo'=>'Activo','Desmovilizado'=>'Desmovilizado','Temporal'=>'Temporal');
        $cboConcesionaria = Concesionaria::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $concesionaria_id = $this->concesionariaActual();
        $formData = array('vehiculo.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboSaltarkm = array('N'=>'No',"S"=>"SI");
        return view($this->folderview.'.mant')->with(compact('vehiculo', 'formData', 'entidad', 'boton', 'listar', 'cboMarca', 'cboArea', 'cboContratista', 'cboCarroceria', 'cboKilometraje','cboTipocombustible','cboConcesionaria','cboEstado','concesionaria_id','cboSaltarkm'));

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
        $ua = Ua::where('codigo',$request->input('ua_id'))->first();
        if(is_null($ua)){
            $dat = array("ua_id" => ["UA no existe"]);
            return json_encode($dat);
        }
        $vehiculo = Vehiculo::where('ua_id','=',Ua::where('codigo',$request->input('ua_id'))->get()[0]->id)->first();
        if(!is_null($vehiculo)){
            $dat = array("ua_id" => ["UA registrada en otro vehiculo"]);
            return json_encode($dat);
        }
        $reglas     = array(
//                            'ua' 				=> 'required|max:10',
    						'modelo' 				=> 'required|max:20',
    						'marca_id' 				=> 'numeric|min:1',
                            'placa'                 => 'max:15',
    						'anio' 					=> 'required|numeric',
    						'contratista_id'  		=> 'numeric|min:1',
    						'asientos'				=> 'required|numeric',
    						'area_id' 				=> 'numeric|min:1',
                            'kilometraje_id'        => 'numeric|min:1',
    						'color' 				=> 'required|max:20',
//                            'unidad'                => 'required|max:25',
    						'chasis' 				=> 'required|max:20',
                            'kilometraje_ini'           => 'required|numeric',
                            //'kilometraje_act'		    => 'required|numeric|gte:kilometraje_ini',
                            'ua_id'                 => 'required',
                            'uatrabajo_id'                 => 'required'
                            //'ua_id'                 => ['required', new SearchUaPadre() ]
/*                          
    						'fechavencimientosoat'  => 'required',
    						'fechavencimientogps'   => 'required',
    						'fechavencimientortv'   => 'required'
*/
                        );
        $mensajes = array(
        	'ua.required'         		  => 'Debe ingresar un código de ua',
            'uatrabajo.required'                 => 'Debe ingresar un código de ua de trabajo',
            'codigo.max'                      => 'El código de ua sobrepasa los 10 carácteres',
            'modelo.required'         		  => 'Debe ingresar el modelo',
            'modelo.max'                      => 'El modelo sobrepasa los 20 carácteres',
            'marca_id.min'  	  		  	  => 'Debe asignar una marca',
            'area_id.min'  	  		  	  	  => 'Debe asignar una area',
            'placa.max'                       => 'La placa sobrepasa los 15 carácteres',
            'anio.required'    				  => 'Debe ingresar un año de fabricación',
            'anio.numeric'    				  => 'Debe ingresar un año valido',
            'asientos.required'				  => 'Debe ingresar el número de aisentos',
            'asientos.numeric'				  => 'Debe ingresar un número valido',
            'color.required'				  => 'Debe ingresar un color',
            'color.max'				 		  => 'El color sobrepasa los 20 carácteres',
            'kilometraje_id.min'              => 'Debe seleccionar una regla de kilometraje',
//            'unidad.required'                 => 'Debe ingresar descripción de la unidad',
//            'unidad.max'                      => 'El unidad sobrepasa los 25 carácteres',
            'chasis.required'				  => 'Debe ingresar el codigo de chasis',
            'chasis.max'				 	  => 'El chasis sobrepasa los 20 carácteres',
            'contratista_id.min'   	  		  => 'Debe asignar un contratista',
            'kilometraje_ini.required'            => 'Kilometraje referencial es obligatorio',
            'kilometraje_ini.numeric'             => 'Debe ingresar un kilometraje válido',
            //'kilometraje_act.required'            => 'Kilometraje referencial es obligatorio',
            //'kilometraje_act.numeric'             => 'Debe ingresar un kilometraje actual válido',
            //'kilometraje_act.gte'    			  => 'Debe ingresar un kilometraje actual mayor o igual que el kilometraje inicial',
/*
            'fechavencimientosoat.required'   => 'Debe ingresar la fecha de vencimiento de SOAT',
            'fechavencimientogps.required'    => 'Debe ingresar la fecha de vencimiento de GPS',
            'fechavencimientortv.required'    => 'Debe ingresar la fecha de vencimiento de RTV'
*/
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $vehiculo = new Vehiculo();
//            $vehiculo->ua 	 		  = strtoupper($request->input('ua'));
            $vehiculo->ua_id                  = Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            $vehiculo->uatrabajo_id                  = Ua::where('codigo',$request->input('uatrabajo_id'))->get()[0]->id;
            $vehiculo->modelo 			      = strtoupper($request->input('modelo'));
            $vehiculo->marca_id 			  = $request->input('marca_id');
            $vehiculo->anio 				  = $request->input('anio');
            $vehiculo->contratista_id 	      = $request->input('contratista_id');
/*            
            $vehiculo->unidad                = $request->input('unidad'); 
            $vehiculo->fechavencimientosoat = $request->input('fechavencimientosoat');
            $vehiculo->fechavencimientogps  = $request->input('fechavencimientogps');
            $vehiculo->fechavencimientortv  = $request->input('fechavencimientortv');
*/          //$vehiculo->concesionaria_id       =  $this->concesionariaActual(); 
            $vehiculo->concesionaria_id       = $request->input('concesionaria_id'); 
            $vehiculo->area_id 				  = $request->input('area_id');
            $vehiculo->tipocombustible_id	  = $request->input('tipocombustible_id');
            $vehiculo->placa 				  = $request->input('placa');
            $vehiculo->motor 				  = $request->input('motor');
            $vehiculo->asientos 		      = $request->input('asientos');
            $vehiculo->chasis 				  = $request->input('chasis');
            $vehiculo->carroceria_id 		  = $request->input('carroceria_id');
            $vehiculo->color 				  = $request->input('color');
            $vehiculo->kilometraje_ini        = $request->input('kilometraje_ini');
            $vehiculo->kilometraje_act        = $request->input('kilometraje_act')+0;
            $vehiculo->kilometraje_id         = $request->input('kilometraje_id');
            $vehiculo->decimales     = $request->input('decimales');
            $vehiculo->precio = $request->input('precio');
            $vehiculo->estado = $request->input('estado');
            $vehiculo->saltarkm  = $request->input('saltarkm');

            $vehiculo->save();


            event( new UserHasCreatedOrDeleted($vehiculo->id,'vehiculo', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $vehiculo = Vehiculo::find($id);

        $marcas = Brand::orderBy('descripcion','asc')->get();
        $cboMarca = array();
        $cboMarca += array('0' => 'Selecione marca');
//        $cboMarca += array('1' => 'Wea');
        foreach($marcas as $k=>$v){
            $cboMarca += array($v->id=>$v->descripcion);
        }

        $areas = Area::orderBy('descripcion','asc')->get();
        $cboArea = array();
        $cboArea += array('0' => 'Selecione área');
//        $cboArea += array('1' => 'wea');
        foreach($areas as $k=>$v){
            $cboArea += array($v->id=>$v->descripcion);
        }

        $carrocerias = Carroceria::orderBy('descripcion','asc')->get();
        $cboCarroceria = array();
        foreach($carrocerias as $k=>$v){
            $cboCarroceria += array($v->id=>$v->descripcion);
        }

        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
//        $cboContratista += array('1' => 'Wea');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        
        $kilometraje = Kilometraje::orderBy('descripcion','asc')->get();
        $cboKilometraje = array();
        $cboKilometraje += array('0' => 'Selecione regla');
        foreach($kilometraje as $k=>$v){
            $cboKilometraje += array($v->id=>$v->descripcion . ':  ' .  $v->limite_inf . '-' . $v->limite_sup);
        }

        $kilometrajes = Kilometraje::orderBy('descripcion','asc')->get();
        $cboKilometraje = array();
        $cboKilometraje += array('0' => 'Selecione kilometraje');
        foreach($kilometrajes as $k=>$v){
            $cboKilometraje += array($v->id => $v->descripcion . ':  ' . $v->limite_inf . '-' . $v->limite_sup);
        }
        
        $tipocombustible = Tipocombustible::orderBy('descripcion','asc')->get();
        $cboTipocombustible = array();
        $cboTipocombustible += array('0' => 'Selecione tipo combustible');
        foreach($tipocombustible as $k=>$v){
            $cboTipocombustible += array($v->id=>$v->descripcion);
        }

/*        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
//        $cboUa += array('1' => '1543ua');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }        
*/
        $cboEstado = array('Activo'=>'Activo','Desmovilizado'=>'Desmovilizado','Temporal'=>'Temporal');
        $cboSaltarkm = array('N'=>'No',"S"=>"SI");
        $cboConcesionaria = Concesionaria::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $entidad  = 'Vehiculo';
        $formData = array('vehiculo.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('vehiculo', 'formData', 'entidad', 'boton', 'listar', 'cboMarca', 'cboArea' ,'cboContratista', 'cboCarroceria', 'cboKilometraje','cboTipocombustible','cboConcesionaria','cboEstado','cboSaltarkm'));
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
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
//                            'ua' 					=> 'required|max:10',
    						'modelo' 				=> 'required|max:20',
    						'marca_id' 				=> 'numeric|min:1',
                            'placa'                 => 'max:15',
    						'anio' 					=> 'required|numeric',
    						'asientos'				=> 'required|numeric',
    						'contratista_id'  		=> 'numeric|min:1',
    						'area_id' 				=> 'numeric|min:1',
                            'kilometraje_id'        => 'numeric|min:1',
    						'color'					=> 'required|max:20',
    						'chasis' 				=> 'required|max:20',
                            'kilometraje_ini'       => 'required|numeric',
                            'ua_id'                 => 'required',
                            //'uatrabajo_id'                 => 'required',
                            'kilometraje_act'		    => 'required|numeric|gte:kilometraje_ini',

//                            'unidad'                => 'required|max:25',
/*
    						'fechavencimientosoat'  => 'required',
    						'fechavencimientogps'   => 'required',
    						'fechavencimientortv'   => 'required'
*/
                        );
        $mensajes = array(
        	'ua.required'         		  => 'Debe ingresar un código de ua',
            'codigo.max'                      => 'El código de ua sobrepasa los 10 carácteres',
            'modelo.required'         		  => 'Debe ingresar el modelo',
            'modelo.max'                      => 'El modelo sobrepasa los 20 carácteres',
            'marca_id.min'  	  		  	  => 'Debe asignar una marca',
            'area_id.min'					  => 'Deve asignar un area',
            'placa.max'                       => 'La placa sobrepasa los 15 carácteres',
            'anio.required'    				  => 'Debe ingresar un año de fabricación',
            'anio.numeric'    				  => 'Debe ingresar un año valido',
            'asientos.required'				  => 'Debe ingresar el número de aisentos',
            'asientos.numeric'				  => 'Debe ingresar un número valido',
            'color.required'				  => 'Debe ingresar un color',
            'color.max'				 		  => 'El color sobrepasa los 20 carácteres',
            'kilometraje_id.min'              => 'Debe seleccionar una regla de kilometraje',
//            'unidad.required'                 => 'Debe ingresar descripción de la unidad',
//            'unidad.max'                      => 'El unidad sobrepasa los 25 carácteres',
            'chasis.required'				  => 'Debe ingresar el codigo de chasis',
            'chasis.max'				 	  => 'El chasis sobrepasa los 20 carácteres',
            'contratista_id.min'   	  		  => 'Debe asignar un contratista',
            'kilometraje_ini.required'        => 'Kilometraje referencial es obligatorio',
            'kilometraje_ini.numeric'         => 'Debe ingresar un kilometraje válido',
            'kilometraje_act.required'            => 'Kilometraje referencial es obligatorio',
            'kilometraje_act.numeric'             => 'Debe ingresar un kilometraje actual válido',
            'kilometraje_act.gte'    			  => 'Debe ingresar un kilometraje actual mayor o igual que el kilometraje inicial',
        );
        if(auth()->user()->tipouser_id!="1"){
            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            } 
        }
        $error = DB::transaction(function() use($request, $id){
            $vehiculo =  Vehiculo::find($id);

            $vehiculoOrg = json_encode($vehiculo);

//            $vehiculo->ua 	 		  = strtoupper($request->input('ua'));
            $vehiculo->ua_id                  =  Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            if($request->input('uatrabajo_id')!=""){
                $vehiculo->uatrabajo_id           = Ua::where('codigo',$request->input('uatrabajo_id'))->get()[0]->id;
            }
            $vehiculo->modelo 			      = strtoupper($request->input('modelo'));
            $vehiculo->marca_id 			  = $request->input('marca_id');
            $vehiculo->anio 				  = $request->input('anio');
            $vehiculo->contratista_id 	      = $request->input('contratista_id');
/*
            $vehiculo->fechavencimientosoat = $request->input('fechavencimientosoat');
            $vehiculo->fechavencimientogps  = $request->input('fechavencimientogps');
            $vehiculo->fechavencimientortv  = $request->input('fechavencimientortv');
            $vehiculo->unidad                = $request->input('unidad');  
*/          $vehiculo->concesionaria_id       = $request->input('concesionaria_id'); 
            $vehiculo->area_id 				  = $request->input('area_id');
            $vehiculo->tipocombustible_id	  = $request->input('tipocombustible_id');
            $vehiculo->placa 				  = $request->input('placa');
            $vehiculo->motor 				  = $request->input('motor');
            $vehiculo->asientos 			  = $request->input('asientos');
            $vehiculo->chasis 				  = $request->input('chasis');
            $vehiculo->carroceria_id    	  = $request->input('carroceria_id');
            $vehiculo->color 				  = $request->input('color');
            $vehiculo->decimales     = $request->input('decimales');
            $vehiculo->saltarkm  = $request->input('saltarkm');
            $ini = $vehiculo->kilometraje_ini;
            $vehiculo->kilometraje_ini        = $request->input('kilometraje_ini');
            $vehiculo->kilometraje_act        = $request->input('kilometraje_act');
            if($vehiculo->kilometraje_ini==$vehiculo->kilometraje_act){
                $vehiculo->kilometraje_rec        =  0;
            }
            $vehiculo->kilometraje_id         = $request->input('kilometraje_id');
            $vehiculo->precio = $request->input('precio');
            $vehiculo->estado = $request->input('estado');
            $vehiculo->save();

            event( new UserHasEdited($vehiculoOrg,$vehiculo,'vehiculo', auth()->user()->id));

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
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $vehiculo = Vehiculo::find($id);

            event( new UserHasCreatedOrDeleted($vehiculo->id,'vehiculo', auth()->user()->id,'eliminar'));

            $vehiculo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Vehiculo::find($id);
        $entidad  = 'Vehiculo';
        $formData = array('route' => array('vehiculo.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function autocomplete(){
        $uas = Ua::select('codigo','descripcion')->get();

        return response() -> json($uas);
    }
    
    public function searchAutocomplete($query){

        $consulta = "select   ve.modelo as 'descripcion', CONCAT(ua.codigo, ' - ', ve.modelo, ' - ', ve.placa, ' - ',marca.descripcion,' - ', carroceria.descripcion) as 'search' , ua.codigo as 'codigo' , ve.id as 'id'
            from vehiculo ve
            join ua on(ua.id = ve.ua_id)
            left join marca on marca.id=ve.marca_id
            left join carroceria on carroceria.id=ve.carroceria_id
            where ve.deleted_at IS NULL AND 
            ua.habilitada IS TRUE AND
            ve.concesionaria_id = {$this -> concesionariaActual()} AND
            (ua.codigo LIKE '%".$query."%' OR ve.modelo LIKE '%".$query."%' or CONCAT(ua.codigo, ' - ', ve.modelo, ' - ', ve.placa, ' - ',marca.descripcion,' - ', carroceria.descripcion) like '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    private function concesionariaActual(){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportar(Request $request){
        return Excel::download(new VehiculoExport(), 'vehiculo.xlsx');
    }

    public function exportarDoc(Request $request){
        ini_set("memory_limit", -1);
        return Excel::download(new VehiculoDocExport($request->input('documentos')), 'vehiculodocumento.xlsx');
    }

    public function exportarDoc2(Request $request){
        ini_set("memory_limit", -1);
        return Excel::download(new VehiculoDoc2Export($request->input('documentos')), 'vehiculodocumentoultimos.xlsx');
    }
}
