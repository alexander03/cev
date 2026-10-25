<?php

namespace App\Http\Controllers;

use Validator;
use App\Equipo;
use App\Area;
use App\Brand;
use App\Ua;
use App\Concesionaria;
use App\Contratista;
use App\Horometraje;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EquipoExport;
use App\Exports\EquipoDocExport;

class EquipoController extends Controller
{
    protected $folderview      = 'app.equipo';
    protected $tituloAdmin     = 'Equipo';
    protected $tituloRegistrar = 'Registrar equipo';
    protected $tituloModificar = 'Modificar equipo';
    protected $tituloEliminar  = 'Eliminar equipo';
    protected $rutas           = array('create' => 'equipo.create', 
            'edit'   => 'equipo.edit', 
            'delete' => 'equipo.eliminar',
            'search' => 'equipo.buscar',
            'index'  => 'equipo.index',
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
        $entidad          = 'Equipo';

        $ua     	  = Libreria::getParam($request->input('codigo'));
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        $contratista      = Libreria::getParam($request->input('contratista'));
        $placa       = Libreria::getParam($request->input('placa'));
        $estado      = Libreria::getParam($request->input('estado'));

        $filtro           = array();
//        $filtro[]         = ['codigo', 'LIKE', '%'.strtoupper($codigo).'%'];
        $filtro[]         = ['descripcion', 'LIKE', '%'.strtoupper($descripcion).'%'];
        $filtro[]         = ['concesionaria_id', $this->consecionariaActual()];
        $filtro[]         = ['estado', 'LIKE', '%'.$estado.'%'];
        if($placa!=""){
            $filtro[]         = ['placa', 'LIKE', '%'.strtoupper($placa).'%'];
        }
/*
        if($ua_id != 0 ){
			$filtro[]         = ['ua_id', '=', $ua_id];        	
        }
*/
        $resultado        = Equipo::where($filtro)->whereHas('ua', function($query) use($ua){
                                $query->where('codigo', 'LIKE', '%'.strtoupper($ua).'%');
                            })->whereHas('contratista', function($query) use($contratista){
                                $query->where('razonsocial', 'LIKE', '%'.strtoupper($contratista).'%');
                            })
                            ->orderBy('descripcion', 'ASC');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Editar', 'numero' => '1');
        }

        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Marca', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Año de Fbr', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Capac. Carga(m3)', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Subcontratista', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Area', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horas mínimas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Moneda', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Unidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '1');
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
        $entidad          = 'Equipo';
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
        $cboEstado = array(''=>'Todos','Activo'=>'Activo','Desmovilizado'=>'Desmovilizado');
        $user = auth()->user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboEstado','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Equipo';
        $equipo = null;

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


        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
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
        $horometraje = Horometraje::orderBy('descripcion','asc')->get();
        $cboHorometraje = array();
        $cboHorometraje += array('0' => 'Selecione regla');
        foreach($horometraje as $k=>$v){
            $cboHorometraje += array($v->id=>$v->descripcion . ':  ' .  $v->limite_inf . '-' . $v->limite_sup);
        }

        $cboConcesionaria = Concesionaria::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $cboMantenimiento = array("N"=>"No","S"=>"Si");
        $cboSaltarhoro = array("N"=>"No","S"=>"Si");
        $cboEstado = array('Activo'=>'Activo','Desmovilizado'=>'Desmovilizado');
        $concesionaria_id = $this->consecionariaActual();
        $formData = array('equipo.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('equipo', 'formData', 'entidad', 'boton', 'listar', 'cboMarca', 'cboArea', 'cboContratista','cboMantenimiento','cboConcesionaria','cboHorometraje','cboEstado','concesionaria_id','cboSaltarhoro'));
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
        if($request->input('ua_id')!=""){
            $equipo = Equipo::where('ua_id','=',Ua::where('codigo',$request->input('ua_id'))->get()[0]->id)->first();
            if(!is_null($equipo)){
                $dat = array("ua_id" => ["UA registrada en otro equipo"]);
                return json_encode($dat);
            }
        }

        $reglas     = array(
//                            'codigo' 				=> 'required|max:10',
    						'descripcion' 			=> 'required|max:40',
    						'modelo' 				=> 'required|max:40',
    						'marca_id' 				=> 'numeric|min:1',
                            'placa'                 => 'max:15',
    						'anio' 					=> 'required',
    						'contratista_id'  		=> 'numeric|min:1',
    						'ua_id'                 => ['required', new SearchUaPadre() ],
                            'horas_min'             => 'numeric|min:1|max:999',
                            'precio'                => 'numeric|min:1|max:999999',
                            'moneda'                => 'boolean',
                            'unidad_id'             => 'numeric|min:1'
//    						'fechavencimientosoat'  => 'required',
//    						'fechavencimientogps'   => 'required',
//    						'fechavencimientortv'   => 'required'
                        );
        $mensajes = array(
        	'codigo.required'         		  => 'Debe ingresar un código',
            'codigo.max'                      => 'El código sobrepasa los 10 carácteres',
            'descripcion.required' 		      => 'Debe ingresar una descripcion',
            'descripcion.max'                 => 'La descripcion sobrepasa los 40 carácteres',
            'modelo.required'         		  => 'Debe ingresar el modelo',
            'modelo.max'                      => 'El modelo sobrepasa los 40 carácteres',
            'marca_id.min'  	  		  	  => 'Debe asignar una marca',
            'placa.max'                       => 'La placa sobrepasa los 15 carácteres',
            'anio.required'    				  => 'Debe ingresar la fecha de fabricación',
            'contratista_id.min'   	  		  => 'Debe asignar un contratista',
            'horas_min.numeric'               => 'Número de horas inválido',
            'horas_min.min'                   => 'Debe ingresar un numero de horas mínimas',
            'horas_min.max'                   => 'Se sobrepasó el numero de horas mínimas posibles de almacenar',
            'precio.numeric'                  => 'Precio inválido',
            'precio.min'                      => 'Debe ingresar un precio',
            'precio.max'                      => 'Se sobrepasó el valor del precio posibles de almacenar',
            'moneda.boolean'                  => 'Debe ingresar una moneda',
            'unidad_id.numeric'               => 'Debe ingresar una unidad',
            'ua_id.required'                  => 'Debe asignar una UA',

//            
//            'fechavencimientosoat.required'   => 'Debe ingresar la fecha de vencimiento de SOAT',
//            'fechavencimientogps.required'    => 'Debe ingresar la fecha de vencimiento de GPS',
//            'fechavencimientortv.required'    => 'Debe ingresar la fecha de vencimiento de RTV'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $equipo = new Equipo();
//            $equipo->codigo 	 		  = strtoupper($request->input('codigo'));
            $equipo->ua_id                = Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            if($request->input('uatrabajo_id')!=""){
                $equipo->uatrabajo_id         = Ua::where('codigo',$request->input('uatrabajo_id'))->get()[0]->id;
            }
            $equipo->descripcion 		  = strtoupper($request->input('descripcion'));
            $equipo->modelo 			  = strtoupper($request->input('modelo'));
            $equipo->marca_id 			  = $request->input('marca_id');
            $equipo->anio 				  = $request->input('anio');
            $equipo->placa 				  = Libreria::getParam($request->input('placa'),'');
            $equipo->capacidad_carga      = $request->input('capacidad_carga');
            $equipo->contratista_id 	  = $request->input('contratista_id');
            //$equipo->concesionaria_id     =  $this->consecionariaActual();
            $equipo->concesionaria_id     = $request->input('concesionaria_id');

            $equipo->horas_min            = $request->input('horas_min');
            $equipo->precio               = $request->input('precio');
            $equipo->moneda               = $request->input('moneda');
            $equipo->unidad_id            = $request->input('unidad_id');
            $equipo->mantenimiento        = $request->input('mantenimiento');
            $equipo->horometraje_id       = $request->input('horometraje_id');
            $equipo->horometroinicial     = $request->input('horometroinicial');
            $equipo->decimales     = $request->input('decimales')+0;
            $equipo->estado               = $request->input('estado');
            $equipo->saltarhoro  = $request->input('saltarhoro');
            
            if($request->input('area_id') != 0){
            	$equipo->area_id 				  = $request->input('area_id');
            }


            $equipo->save();


            event( new UserHasCreatedOrDeleted($equipo->id,'equipo', auth()->user()->id,'crear'));
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
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $equipo = Equipo::find($id);

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


        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
//        $cboContratista += array('1' => 'Wea');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }

/*        $uas = Ua::orderBy('descripcion','asc')->get();
        $cboUa = array();
        $cboUa += array('0' => 'Selecione UA');
//        $cboUa += array('1' => '1543ua');
        foreach($uas as $k=>$v){
            $cboUa += array($v->id=>$v->descripcion . '-' .$v->codigo);
        }        
*/
        $horometraje = Horometraje::orderBy('descripcion','asc')->get();
        $cboHorometraje = array();
        $cboHorometraje += array('0' => 'Selecione regla');
        foreach($horometraje as $k=>$v){
            $cboHorometraje += array($v->id=>$v->descripcion . ':  ' .  $v->limite_inf . '-' . $v->limite_sup);
        }

        $cboConcesionaria = Concesionaria::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $cboMantenimiento = array("N"=>"No","S"=>"Si");
        $cboSaltarhoro = array("N"=>"No","S"=>"Si");
        $cboEstado = array('Activo'=>'Activo','Desmovilizado'=>'Desmovilizado');
        $entidad  = 'Equipo';
        $formData = array('equipo.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('equipo', 'formData', 'entidad', 'boton', 'listar', 'cboMarca', 'cboArea', 'cboContratista', 'cboMantenimiento','cboConcesionaria','cboHorometraje','cboEstado','cboSaltarhoro'));
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
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                        //    'codigo'                => 'required|max:10',
                            'descripcion'           => 'required|max:40',
                            'modelo'                => 'required|max:40',
                            'marca_id'              => 'numeric|min:1',
                            'placa'                 => 'max:15',
                            'anio'                  => 'required',
                            'contratista_id'        => 'numeric|min:1',
                            'ua_id'                 => 'required',
                            'horas_min'             => 'numeric|min:1|max:999',
                            'precio'                => 'numeric|min:1|max:999999',
                            'moneda'                => 'boolean',
                            'unidad_id'             => 'numeric|min:1',
                            //'uatrabajo_id'                 => 'required'
//                            'fechavencimientosoat'  => 'required',
//                            'fechavencimientogps'   => 'required',
//                            'fechavencimientortv'   => 'required'
                        );
        $mensajes = array(
        	'codigo.required'                => 'Debe ingresar un código',
            'codigo.max'                      => 'El código sobrepasa los 10 carácteres',
            'descripcion.required'            => 'Debe ingresar una descripcion',
            'descripcion.max'                 => 'La descripcion sobrepasa los 40 carácteres',
            'modelo.required'                 => 'Debe ingresar el modelo',
            'modelo.max'                      => 'El modelo sobrepasa los 40 carácteres',
            'marca_id.min'                    => 'Debe asignar una marca',
            'placa.max'                       => 'La placa sobrepasa los 15 carácteres',
            'anio.required'                   => 'Debe ingresar la fecha de fabricación',
            'contratista_id.min'              => 'Debe asignar un contratista',
            'horas_min.numeric'               => 'Número de horas inválido',
            'horas_min.min'                   => 'Debe ingresar un numero de horas mínimas',
            'horas_min.max'                   => 'Se sobrepasó el numero de horas mínimas posibles de almacenar',
            'precio.numeric'                  => 'Precio inválido',
            'precio.min'                      => 'Debe ingresar un precio',
            'precio.max'                      => 'Se sobrepasó el valor del precio posibles de almacenar',
            'moneda.boolean'                  => 'Debe ingresar una moneda',
            'unidad_id.numeric'               => 'Debe ingresar una unidad',
//            'ua_id.required'                  => 'Debe asignar una ua',
//            'fechavencimientosoat.required'   => 'Debe ingresar la fecha de vencimiento de SOAT',
//            'fechavencimientogps.required'    => 'Debe ingresar la fecha de vencimiento de GPS',
//            'fechavencimientortv.required'    => 'Debe ingresar la fecha de vencimiento de RTV'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $equipo = Equipo::find($id);

            //Copia bitacora
            $equipoOrg = json_encode($equipo);

            $equipo->ua_id                = Ua::where('codigo',$request->input('ua_id'))->get()[0]->id;
            if(!is_null($request->input('uatrabajo_id'))){
                $equipo->uatrabajo_id         = Ua::where('codigo',$request->input('uatrabajo_id'))->get()[0]->id;
            }
            $equipo->descripcion 		  = strtoupper($request->input('descripcion'));
            $equipo->modelo 			  = strtoupper($request->input('modelo'));
            $equipo->marca_id 			  = $request->input('marca_id');
            $equipo->anio 				  = $request->input('anio');
            $equipo->capacidad_carga      = $request->input('capacidad_carga');
            $equipo->placa                = Libreria::getParam($request->input('placa'),'');
            $equipo->contratista_id 	  = $request->input('contratista_id');
            if($request->input('area_id') != 0){
            	$equipo->area_id 				  = $request->input('area_id');
            }
            $equipo->horas_min            = $request->input('horas_min');
            $equipo->precio               = $request->input('precio');
            $equipo->moneda               = $request->input('moneda');
            $equipo->unidad_id            = $request->input('unidad_id');
            $equipo->mantenimiento        = $request->input('mantenimiento');
            $equipo->concesionaria_id     = $request->input('concesionaria_id');
            $equipo->horometraje_id       = $request->input('horometraje_id');
            $equipo->horometroinicial     = $request->input('horometroinicial');
            $equipo->decimales     = $request->input('decimales')+0;
            $equipo->estado               = $request->input('estado');
            $equipo->saltarhoro  = $request->input('saltarhoro');

            $equipo->save();

            event( new UserHasEdited($equipoOrg,$equipo,'equipo', auth()->user()->id));
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
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $equipo = Equipo::find($id);
            event( new UserHasCreatedOrDeleted($equipo->id,'equipo', auth()->user()->id,'eliminar'));
            $equipo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Equipo::find($id);
        $entidad  = 'Equipo';
        $formData = array('route' => array('equipo.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function searchAutocomplete($query){

        $consulta = "select   eq.descripcion as 'descripcion', CONCAT(ua.codigo, ' -- ', eq.descripcion) as 'search' , ua.codigo as 'codigo' , eq.id as 'id'
            from equipo eq
            join ua on(ua.id = eq.ua_id)
            where eq.deleted_at IS NULL AND 
            ua.habilitada IS TRUE AND
            eq.concesionaria_id = {$this -> consecionariaActual()} AND
            (ua.codigo LIKE '%".$query."%' OR eq.descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    private function consecionariaActual(){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportar(Request $request){
        return Excel::download(new EquipoExport(), 'equipo.xlsx');
    }

    public function exportarDoc(Request $request){
        ini_set("memory_limit", -1);
        return Excel::download(new EquipoDocExport(), 'equipodocumento.xlsx');
    }
}
