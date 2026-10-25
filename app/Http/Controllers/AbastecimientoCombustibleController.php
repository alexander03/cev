<?php

namespace App\Http\Controllers;

use App\Abastecimiento;
use App\AbastecimientoCombustible;
use App\Concesionaria;
use App\Conductor;
use App\Equipo;
use App\Horometro;
use App\Piepagina;
use App\Exports\AbastecimientoCombustibleExport;
use App\Exports\ReporteDiariodeCombustible;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre;
use App\Rules\SelectDifZero;
use App\Ua;
use App\Configuracion;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UaExport;
use App\Grifo;
use App\Imports\UaImport;
use App\Rules\AuthenticateUser;
use App\Rules\SearchConductorRule;
use App\Rules\SearchEquipo;
use App\Rules\SearchGrifoRule;
use App\Tipocombustible;
use App\User;
use App\Vehiculo;
use Exception;
use App\Contratista;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;


//use function PHPSTORM_META\map;

class AbastecimientoCombustibleController extends Controller{
    protected $folderview      = 'app.abast-combustible';
    protected $tituloAdmin     = 'Abastecimiento de Combustible';
    protected $tituloRegistrar = 'Registrar Abastecimiento de Combustible';
    protected $tituloModificar = 'Modificar Abastecimiento de Combustible';
    protected $tituloEliminar  = 'Eliminar Abastecimiento de Combustible';
    protected $tituloGenerar  = 'Generar reporte Combustible';
    protected $rutas           = array('create' => 'abastecimiento.create', 
            'edit'   => 'abastecimiento.edit', 
            'delete' => 'abastecimiento.eliminar',
            'search' => 'abastecimiento.buscar',
            'index'  => 'abastecimiento.index',
            'generateReport' => 'abastecimiento.generateReport',
    );

    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 3)->first()->valor));
    }

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'AbastecimientoCombustible';
        $fechaQuery       = Libreria::getParam($request->input('date_abastecimiento'));
        $fechaFinal       = Libreria::getParam($request->input('date_abastecimiento2'));
        $codigoUaQuery    = Libreria::getParam($request->input('codigo_ua'));
        $grifoQuery       = Libreria::getParam($request->input('grifo'));
        $placaQuery       = Libreria::getParam($request->input('placa'));
        //Buscar ua 
        $uaException = false;
        if( isset($codigoUaQuery) ){
            $uaDB = Ua::select('id') -> where('codigo', 'LIKE', "%{$codigoUaQuery}%") -> first();   
            //*Excepcion si no encuentra registro
            ( isset( $uaDB ) ) ? $uaException = false : $uaException = true;
        }
        $uaId = (isset( $uaDB )) ? $uaDB -> id : null;
        //Buscar grifo
        $grifoException = true;
        $grifoId = null;
        if( $grifoQuery!="0"){
            $grifoId = $grifoQuery;
            //*Excepcion si no encuentra registro
            $grifoException = false;
        }
        //Buscar equipo
        $equipVehiException = false;
        if( isset($placaQuery) ){
            $equipoDB = Equipo::select('id') -> where('placa', 'LIKE', "%{$placaQuery}%") -> first();
            $vehiculoDB = Vehiculo::select('id') -> where('placa', 'LIKE', "%{$placaQuery}%") -> first();
            if( isset($equipoDB) ){
                $vehiculoDB = new Vehiculo();
                $vehiculoDB -> id = -1;
            }else if( isset($vehiculoDB) ){
                $equipoDB = new Equipo();
                $equipoDB -> id = -1;
            }
            //*Excepcion si no encuentra registro
            ( isset($equipoDB) || isset($vehiculoDB) ) ? $equipVehiException = false : $equipVehiException = true;
        }
        $equipoId = (isset( $equipoDB )) ? $equipoDB -> id : null;
        $vehiculoId = (isset( $vehiculoDB )) ? $vehiculoDB -> id : null;
        $concesionaria_id = $this->getConsecionariaActual();
        //Resultado
        $resultado = AbastecimientoCombustible::
            where([
                ['fecha_abastecimiento', '>=', $fechaQuery],
                ['fecha_abastecimiento', '<=', $fechaFinal],
                ['ua_id', 'LIKE', "%{$uaId}%"],
            ])
            /*->where(function($sql) use($concesionaria_id){
                $sql->whereIn('equipo_id',function($query) use($concesionaria_id) {
                            $query->from('equipo')
                            ->where('concesionaria_id','=',$concesionaria_id)
                            ->select('id');
                        })
                    ->orWhereIn('vehiculo_id',function($query) use($concesionaria_id) {
                            $query->from('vehiculo')
                            ->where('concesionaria_id','=',$concesionaria_id)
                            ->select('id');
                        });
            })*/
            ->orderBy('fecha_abastecimiento', 'desc'); 
        //Query compleja 
        if( isset($equipoId) || isset($vehiculoId) ){
            $resultado = AbastecimientoCombustible::
                where([
                    ['fecha_abastecimiento', '>=', $fechaQuery],
                    ['fecha_abastecimiento', '<=', $fechaFinal],
                    ['ua_id', 'LIKE', "%{$uaId}%"],
                ])
                ->where(function ($sql) use($equipoId,$vehiculoId){
                    if($equipoId != -1){
                        $sql->where('equipo_id', '=', "{$equipoId}");
                    }
                    if($vehiculoId != -1){
                        $sql->Where('vehiculo_id', '=', "{$vehiculoId}");
                    }
                })
                -> orderBy('fecha_abastecimiento', 'desc'); 
        }
        if(!is_null($grifoId)){
            $resultado = $resultado->where('grifo_id','=',$grifoId);
        }
        $resultado = $resultado->where('concesionaria_id','=',$concesionaria_id);
        $lista = $resultado->get();  
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $cabecera[]       = array('valor' => 'Fecha de abastecimiento', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Grifo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo de combustible', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Conductor - Responsable', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Unidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Marca', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa/serie', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'QTD(GL)', 'numero' => '1');
        $cabecera[]       = array('valor' => 'QTD(L)', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Abastecimiento por día', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Motivo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comprobante', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Número de comprobante', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora de inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora de fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Lugar de abastecimiento', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $usuario = auth()->user();
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));

            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'usuario'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'usuario'));
    }
    
    public function index(){
        
        $entidad          = 'AbastecimientoCombustible';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $titulo_generar   = $this->tituloGenerar;
        $cboGrifos = Grifo::select('id','descripcion')->orderBy('descripcion')->get();
        $cboGrifo = array("0"=>"Todos") + Grifo::select('id','descripcion')->orderBy('descripcion')->pluck('descripcion','id')->toArray();
        $cboTipocombustible = Tipocombustible::select('id','descripcion')->orderBy('descripcion')->get();
        $cboConcesionaria = Concesionaria::orderBy('razonsocial')->get();
        return view($this->folderview.'.admin')->with(compact('entidad', 'cboGrifos' ,'title', 'titulo_registrar', 'ruta', 'titulo_generar','cboConcesionaria','cboTipocombustible','cboGrifo'));
    }

    public function create(Request $request){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'AbastecimientoCombustible';
        $abastecimiento = null;
        $formData = array('abastecimiento.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $lstAbastecimientos = Abastecimiento::all();
        $lstTipoCombustibles = Tipocombustible::all();
        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        $cboUsuario = User::where('tipouser_id','=',7)->orderBy('name','asc')->get()->pluck('name','id');
        return view($this->folderview.'.mant')->with(compact('abastecimiento', 'formData', 'entidad', 'boton', 'listar', 'lstAbastecimientos', 'lstTipoCombustibles','cboContratista','cboUsuario'));
    }

    public function store(Request $request){
        //BUSCAR EQUIPO KM
        if($request -> input('equipo_id')){
            if($request -> input('equipo_tipo') === 'e'){
                $equipoDB =  Equipo::where('id', $request -> input('equipo_id')) -> first();
                if($equipoDB->saltarhoro=="N"){
                    $abast = AbastecimientoCombustible::where('equipo_id','=',$request->input('equipo_id'))->where('fecha_abastecimiento','<=',$request->input('fecha_abastecimiento'))->orderBy('fecha_abastecimiento','desc')->first();
                    if(!is_null($abast) && ($request->input('km')+0)< ($abast->km+0)){
                        $dat = array("km" => ["Horómetro a registrar es menor al actual, el último es:  ".$abast->km." del ".$abast->fecha_abastecimiento],"id"=>$abast->id);
                        return json_encode($dat);
                    }
                }
            }
            if($request -> input('equipo_tipo') === 'v'){
                $abast = AbastecimientoCombustible::where('vehiculo_id','=',$request->input('equipo_id'))->where('fecha_abastecimiento','<=',$request->input('fecha_abastecimiento'))->orderBy('fecha_abastecimiento','desc')->first();
                if(!is_null($abast) && ($request->input('km')+0)< ($abast->km+0)){
                    $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es:  ".$abast->km." del ".$abast->fecha_abastecimiento],"id"=>$abast->id);
                    return json_encode($dat);
                }
            }   
        }

        $reglas = [
            'fecha_abastecimiento' => 'required',
            'grifo_id' => ['required', new SearchGrifoRule()],
            'user_id' => 'required',
            //'usuario' => ['required'],
            //'password' => ['required', new AuthenticateUser($request->input('usuario'))],
            //'ua_id' => ['required', new SearchUaPadre()],
            'ua_id' => 'required',
            'equipo_id' => ['required', new SearchEquipo()],
            'qtdgl' => 'required',
            'qtdl' => 'required',
            'km' => 'required',
            'abastecimiento_dia' => 'required',
            //'motivo' => 'required',
            //'comprobante' => 'required',
            //'numero_comprobante' => 'required',
            //'hora_inicio' => 'required',
            //'hora_fin' => 'nullable|after_or_equal:hora_inicio',
            'abastecimiento_id' => 'required',
            'tipocombustible_id' => 'required'
        ];
        $mensajes = [
            'fecha_abastecimiento.required' => 'Su fecha de abastecimiento es requerida',
            'grifo_id.required' => 'El grifo es requerido',
            //'usuario.required' => 'El usuario es requerido',
            //'password.required' => 'El password es requerido',
            'ua_id.required' => 'Su ua es requerida',
            'user_id.required' => 'El usuario es requerido',
            'equipo_id.required' => 'El equipo o vehículo es requerido',
            'qtdgl.required' => 'Su QTD(GL) es requerido',
            'qtdl.required' => 'Su QTD(L) es requerido',
            'km.required' => 'Su km es requerido',
            'abastecimiento_dia.required' => 'Su abastecimiento por día es requerido',
            //'motivo.required' => 'El motivo es requerido',
            //'comprobante.required' => 'El comprobante es requerido',
            //'numero_comprobante.required' => 'El número de comprobante es requerido',
            //'hora_inicio.required' => 'Su fecha de inicio es requerida',
            //'hora_fin.required' => 'Su fecha de fin es requerida',
            //'hora_fin.after_or_equal' => 'Su fecha de fin no puede ser menor que la de inicio',
            'abastecimiento_id.required' => 'El lugar de abastecimiento es requerido',
            'tipocombustible_id.required' => 'El tipo de combustible es requerido'
        ];
        
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request){
            $abastecimiento = new AbastecimientoCombustible();
            $abastecimiento -> fecha_abastecimiento = $request -> input('fecha_abastecimiento');
            //BUSCAR GRIFO
            if($request -> input('grifo_id')){
                $grifoDB =  Grifo::where('descripcion', $request -> input('grifo_id')) -> get();
                $abastecimiento -> grifo_id = (!($grifoDB -> isEmpty())) ? $grifoDB[0] -> id : null;
            }
            //BUSCAR USUARIO
            //$usuarioDB = User::where('username', $request -> input('usuario')) -> get();
            //$abastecimiento -> user_id = $usuarioDB[0] -> id;
            $abastecimiento -> user_id = $request->input('user_id');
            
            //BUSCAR UA
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $abastecimiento -> ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            //BUSCAR EQUIPO
            $concesionaria_id = $uaDB[0] ->concesionaria_id;
            if($request -> input('equipo_id')){
                if($request -> input('equipo_tipo') === 'e'){
                    $equipoDB =  Equipo::where('id', $request -> input('equipo_id')) -> get();
                    if(! $equipoDB->isEmpty()){
                        $concesionaria_id = $equipoDB[0]->concesionaria_id;
                    }
                    
                    $abastecimiento -> equipo_id = (!($equipoDB -> isEmpty())) ? $equipoDB[0] -> id : null;

                    //ACTUALIZO H. ACTUAL DEL EQUIPO
                    $equipo = $equipoDB[0];
                    if(($request->input('km')+0)>$equipo->horometroactual){
                        $equipo->horometroactual = $request->input('km');
                        $equipo->save();
                    }
                }
                if($request -> input('equipo_tipo') === 'v'){
                    $vehiculoDB =  Vehiculo::where('id', $request -> input('equipo_id')) -> get();
                    if(! $vehiculoDB->isEmpty()){
                        $concesionaria_id = $vehiculoDB[0]->concesionaria_id;
                    }
                    $abastecimiento -> vehiculo_id = (!($vehiculoDB -> isEmpty())) ? $vehiculoDB[0] -> id : null;

                    //ACTUALIZO H. ACTUAL DEL VEHICULO
                    $vehiculo = $vehiculoDB[0];
                    if(($request->input('km')+0)>$vehiculo->kilometraje_act){
                        $vehiculo->kilometraje_act = $request->input('km');
                        $vehiculo->save();
                    }
                }   
            }
            $abastecimiento -> qtdgl = $request -> input('qtdgl');
            $abastecimiento -> qtdl = $request -> input('qtdl');
            $abastecimiento -> km = $request -> input('km');
            $abastecimiento -> abastecimiento_dia = $request -> input('abastecimiento_dia');
            $abastecimiento -> motivo = $request -> input('motivo');
            $abastecimiento -> comprobante = $request -> input('comprobante');
            $abastecimiento -> numero_comprobante = $request -> input('numero_comprobante');
            $abastecimiento -> hora_inicio = $request -> input('hora_inicio');
            $abastecimiento -> contratista_id = $request -> input('contratista_id');
            $abastecimiento -> concesionaria_id = $concesionaria_id;
            $abastecimiento -> descuento = $request->input('descuento');
            //$abastecimiento -> hora_fin = $request -> input('hora_fin');
            date_default_timezone_set("America/Lima");
            $abastecimiento -> hora_fin = date("H:i");
            $abastecimiento -> abastecimiento_id = $request -> input('abastecimiento_id');
            $abastecimiento -> tipocombustible_id = $request -> input('tipocombustible_id');

            $abastecimiento -> save();
            
            event( new UserHasCreatedOrDeleted($abastecimiento->id,'abastecimiento_combustible', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request){

        $existe = Libreria::verificarExistencia($id, 'abastecimiento_combustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $abastecimiento = AbastecimientoCombustible::find($id);
        $entidad  = 'AbastecimientoCombustible';
        $formData = array('abastecimiento.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $lstAbastecimientos = Abastecimiento::all();
        $lstTipoCombustibles = Tipocombustible::all();
        $contratistas = Contratista::orderBy('razonsocial','asc')->get();
        $cboContratista = array();
        $cboContratista += array('0' => 'Selecione subcontratista');
        foreach($contratistas as $k=>$v){
            $cboContratista += array($v->id=>$v->razonsocial);
        }
        $cboUsuario = User::where('tipouser_id','=',7)->orderBy('name','asc')->get()->pluck('name','id');
        return view($this->folderview.'.mant')->with(compact('abastecimiento', 'formData', 'entidad', 'boton', 'listar', 'lstAbastecimientos', 'lstTipoCombustibles','cboContratista','cboUsuario'));
    }

    public function update(Request $request, $id){
        //BUSCAR EQUIPO KM
        if($request -> input('equipo_id')){
            if($request -> input('equipo_tipo') === 'e'){
                $equipoDB =  Equipo::where('id', $request -> input('equipo_id')) -> first();
                if($equipoDB->saltarhoro=="N"){
                    $abast = AbastecimientoCombustible::where('equipo_id','=',$request->input('equipo_id'))->where('fecha_abastecimiento','<=',$request->input('fecha_abastecimiento'))->orderBy('fecha_abastecimiento','desc')->whereNotIn('id',[$id])->first();
                    if(!is_null($abast) && ($request->input('km')+0)< ($abast->km+0)){
                        $dat = array("km" => ["Horómetro a registrar es menor al actual, el último es:  ".$abast->km." del ".$abast->fecha_abastecimiento],"id"=>$abast->id);
                        return json_encode($dat);
                    }
                }
            }
            if($request -> input('equipo_tipo') === 'v'){
                $abast = AbastecimientoCombustible::where('vehiculo_id','=',$request->input('equipo_id'))->where('fecha_abastecimiento','<=',$request->input('fecha_abastecimiento'))->orderBy('fecha_abastecimiento','desc')->whereNotIn('id',[$id])->first();
                if(!is_null($abast) && ($request->input('km')+0)< ($abast->km+0)){
                    $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es:  ".$abast->km." del ".$abast->fecha_abastecimiento],"id"=>$abast->id);
                    return json_encode($dat);
                }
            }   
        }

        $reglas = [
            'fecha_abastecimiento' => 'required',
            'grifo_id' => ['required', new SearchGrifoRule()],
            'user_id' => 'required',
            //'usuario' => ['required'],
            //'password' => ['required'],
            //'ua_id' => ['required', new SearchUaPadre()],
            'ua_id' => 'required',
            'equipo_id' => ['required', new SearchEquipo()],
            'qtdgl' => 'required',
            'qtdl' => 'required',
            'km' => 'required',
            'abastecimiento_dia' => 'required',
            //'motivo' => 'required',
            //'comprobante' => 'required',
            //'numero_comprobante' => 'required',
            //'hora_inicio' => 'required',
            //'hora_fin' => 'nullable|after_or_equal:hora_inicio',
            'abastecimiento_id' => 'required',
            'tipocombustible_id' => 'required'
        ];

        $mensajes = [
            'fecha_abastecimiento.required' => 'Su fecha de abastecimiento es requerida',
            'grifo_id.required' => 'El grifo es requerido',
            //'usuario.required' => 'El usuario es requerido',
            //'password.required' => 'El password es requerido',
            'ua_id.required' => 'Su ua es requerida',
            'user_id.required' => 'El usuario es requerido',
            'equipo_id.required' => 'El equipo o vehículo es requerido',
            'qtdgl.required' => 'Su QTD(GL) es requerido',
            'qtdl.required' => 'Su QTD(L) es requerido',
            'km.required' => 'Su km es requerido',
            'abastecimiento_dia.required' => 'Su abastecimiento por día es requerido',
            //'motivo.required' => 'El motivo es requerido',
            //'comprobante.required' => 'El comprobante es requerido',
            //'numero_comprobante.required' => 'El número de comprobante es requerido',
            //'hora_inicio.required' => 'Su fecha de inicio es requerida',
            //'hora_fin.required' => 'Su fecha de fin es requerida',
            //'fecha_fin.after_or_equal' => 'Su fecha de fin no puede ser menor que la de inicio',
            'abastecimiento_id.required' => 'El lugar de abastecimiento es requerido',
            'tipocombustible_id.required' => 'El tipo de combustible es requerido'
        ];
        
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $existe = Libreria::verificarExistencia($id, 'abastecimiento_combustible');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $abastecimiento = AbastecimientoCombustible::find($id);
            $abastecimientoCop = json_encode($abastecimiento);

            $abastecimiento -> fecha_abastecimiento = $request -> input('fecha_abastecimiento');
            //BUSCAR GRIFO
            if($request -> input('grifo_id')){
                $grifoDB =  Grifo::where('descripcion', $request -> input('grifo_id')) -> get();
                $abastecimiento -> grifo_id = (!($grifoDB -> isEmpty())) ? $grifoDB[0] -> id : null;
            }
            //BUSCAR CONDUCTOR
            if($request -> input('conductor_id')){
                $abastecimiento -> conductor_fake = null;
                $conductorDB =  Conductor::where('dni', $request -> input('conductor_id')) -> get();
                $abastecimiento -> conductor_id = (!($conductorDB -> isEmpty())) ? $conductorDB[0] -> id : null;
                if($abastecimiento -> conductor_id === null) $abastecimiento -> conductor_fake = strtoupper($request -> input('conductor_id'));
            }
            //BUSCAR UA
            if($request -> input('ua_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_id')) -> get();
                $abastecimiento -> ua_id = (!($uaDB -> isEmpty())) ? $uaDB[0] -> id : null;
            }
            //BUSCAR EQUIPO
            if($request -> input('equipo_id')){
                if($request -> input('equipo_tipo') === 'e'){
                    $abastecimiento -> vehiculo_id = null;
                    $equipoDB =  Equipo::where('id', $request -> input('equipo_id')) -> get();
                    if(! $equipoDB->isEmpty()){
                        $concesionaria_id = $equipoDB[0]->concesionaria_id;
                    }

                    $abastecimiento -> equipo_id = (!($equipoDB -> isEmpty())) ? $equipoDB[0] -> id : null;

                    //ACTUALIZO H. ACTUAL DEL EQUIPO
                    $equipo = $equipoDB[0];
                    if(($request->input('km')+0)>$equipo->horometroactual){
                        $equipo->horometroactual = $request->input('km');
                        $equipo->save();
                    }
                }
                if($request -> input('equipo_tipo') === 'v'){
                    $abastecimiento -> equipo_id = null;
                    $vehiculoDB =  Vehiculo::where('id', $request -> input('equipo_id')) -> get();
                    if(! $vehiculoDB->isEmpty()){
                        $concesionaria_id = $vehiculoDB[0]->concesionaria_id;
                    }
                    
                    $abastecimiento -> vehiculo_id = (!($vehiculoDB -> isEmpty())) ? $vehiculoDB[0] -> id : null;

                    //ACTUALIZO K. RECORRIDO DEL VEHICULO
                    $vehiculo = $vehiculoDB[0];
                    if(($request->input('km')+0)>$vehiculo->kilometraje_act){
                        $vehiculo->kilometraje_act = $request->input('km');
                        $vehiculo->save();
                    }
                }   
            }
            
            //BUSCAR USUARIO
            //$usuarioDB = User::where('username', $request -> input('usuario')) -> get();
            //$abastecimiento -> user_id = $usuarioDB[0] -> id;
            $abastecimiento -> user_id = $request->input('user_id');
            
            
            $abastecimiento -> qtdgl = $request -> input('qtdgl');
            $abastecimiento -> qtdl = $request -> input('qtdl');
            $abastecimiento -> km = $request -> input('km');
            $abastecimiento -> abastecimiento_dia = $request -> input('abastecimiento_dia');
            $abastecimiento -> motivo = $request -> input('motivo');
            $abastecimiento -> comprobante = $request -> input('comprobante');
            $abastecimiento -> numero_comprobante = $request -> input('numero_comprobante');
            $abastecimiento -> contratista_id = $request -> input('contratista_id');
            $abastecimiento -> descuento = $request->input('descuento');
            //$abastecimiento -> concesionaria_id = $concesionaria_id;
            //$abastecimiento -> hora_inicio = $request -> input('hora_inicio');
            //$abastecimiento -> hora_fin = $request -> input('hora_fin');
            $abastecimiento -> abastecimiento_id = $request -> input('abastecimiento_id');
            $abastecimiento -> tipocombustible_id = $request -> input('tipocombustible_id');
            $abastecimiento -> save();
            
            event(new UserHasEdited($abastecimientoCop,$abastecimiento,'abastecimiento_combustible',auth()->user()->id));

        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego){

        $existe = Libreria::verificarExistencia($id, 'abastecimiento_combustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = AbastecimientoCombustible::find($id);
        $entidad  = 'AbastecimientoCombustible';
        $formData = array('route' => array('abastecimiento.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'abastecimiento_combustible');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $abastecimiento = AbastecimientoCombustible::find($id);
            event( new UserHasCreatedOrDeleted($abastecimiento->id,'abastecimiento_combustible', auth()->user()->id,'eliminar'));
            $abastecimiento->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    //PETICION GET QUE DEVUELVE TODOS LOS DATOS GRIFO
    public function searchAutocompleteGrifo($query){
    
        $consulta = "select id, descripcion from grifo where
            deleted_at IS NULL AND
            descripcion LIKE '%{$query}%'";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    //PETICION GET QUE DEVUELVE TODOS LOS DATOS CONDUCTOR
    public function searchAutocompleteConductor($query){

        $consulta = "select id, nombres, apellidos, dni, CONCAT(nombres, ' ', apellidos, ' - ', dni) as 'search' 
            from conductor where
            deleted_at IS NULL AND
            apellidos LIKE UPPER('%".$query."%') OR dni LIKE '%".$query."%'";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    //PETICION GET QUE DEVUELVE TODOS LOS DATOS EQUIPO O VEHICULO
    public function searchAutocompleteEquipo($query){

        $consulta = "select id, tipo, descripcion, ua, ua_desc,
            CONCAT(ua, ' - ', descripcion) as 'search'
            from view_equipo_vehiculo
            where deleted_at IS NULL AND 
            concesionaria_id = {$this -> getConsecionariaActual()} AND
            (ua LIKE '%".$query."%' OR descripcion LIKE '%".$query."%')";
        //echo $consulta;
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    //EXPORT EXCEL
    public function exportExcel(Request $request){
        
        return Excel::download(new AbastecimientoCombustibleExport($request->input('fechainicio'),$request->input('fechafin')), 'abast-combustible-list.xlsx');
    }

    //EXPORT EXCEL AND PDF
    public function exportControlDiario(Request $request){

        if($request->tipo == 1){
            return Excel::download(new ReporteDiariodeCombustible($request->fecha,$request->grifo,$request->concesionaria,$request->tipocombustible), 'abast-combustible-diario.xlsx');
        }else{
            $namefile = 'abast-combustible-diario-' . $request->fecha . '.pdf';

            $fechaAbast = $request->fecha;
            $idGrifo = $request->grifo;
            $grifo = Grifo::find($idGrifo)->descripcion;
            $horometro = Horometro::where('grifo_id','=',$idGrifo)->where('fecha','=',$request->fecha)->first();
            $tipocombustible_id = $request->tipocombustible;
            $lsConcesionaria = $request->concesionaria;
            $concesionaria = array();
            foreach($lsConcesionaria as $k=>$v){
                $concesionaria[] = $v;
            }
            
            $queryVehiculo = AbastecimientoCombustible::select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni',
                'ua.codigo as code',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc', 
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza'),
                'cd.archivo as firma')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                //-> whereIn('ua.concesionaria_id',$concesionaria)
                -> whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                -> whereNull('cd.deleted_at')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '=', $fechaAbast ],
                    [ 'abastecimiento_combustible.grifo_id', '=', $idGrifo ],
                    [ 'cd.tipo','like','imagen-firma']
                ])-> where(function($sql) use($tipocombustible_id){
                    if(!is_null($tipocombustible_id) && $tipocombustible_id!=""){
                        $sql->where('abastecimiento_combustible.tipocombustible_id','=',$tipocombustible_id);
                    }
                });

            $resultQuery = AbastecimientoCombustible::select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'),
                'c.dni as dni',
                'ua.codigo as code',
                DB::raw('(CASE WHEN eq.descripcion IS NULL THEN "-" ELSE eq.descripcion END) AS desceq'),
                'mc.descripcion as descmc', 
                DB::raw('(CASE WHEN eq.placa IS NULL THEN "-" ELSE eq.placa END) AS eqpl'), 
                'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza'),
                'cd.archivo as firma')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> leftJoin('equipo as eq', 'eq.id', '=', 'equipo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'eq.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else eq.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> whereNull('abastecimiento_combustible.vehiculo_id')
                //-> whereIn('ua.concesionaria_id',$concesionaria)
                -> whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                -> where(function($sql) use($tipocombustible_id){
                    if(!is_null($tipocombustible_id) && $tipocombustible_id!=""){
                        $sql->where('abastecimiento_combustible.tipocombustible_id','=',$tipocombustible_id);
                    }
                })
                -> unionAll($queryVehiculo)
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '=', $fechaAbast ],
                    [ 'abastecimiento_combustible.grifo_id', '=', $idGrifo ],
                    [ 'cd.tipo','like','imagen-firma']
                ])
                -> get();
            
            $newResult = [];
            $arrId = [];
        
            for($i = 0; $i < count($resultQuery); $i++) { 

                if(isset($arrId[0])){
                    $isRepeat = false;
                    foreach($arrId as $id){
                        if($resultQuery[$i] -> id === $id) $isRepeat = true;
                    }
                    if(!$isRepeat) array_push($newResult, $resultQuery[$i]);
                    array_push($arrId, $resultQuery[$i] -> id);
                }else{
                    $arrId = [ $resultQuery[0] -> id ];
                    $newResult = [ $resultQuery[0] ];
                }
            }

            for($i = 0; $i < count($newResult); $i++) unset($newResult[$i] -> id);
            $collectionResult = collect($newResult);

            $data['data'] = $collectionResult;
            $data['fecha'] = $fechaAbast;
            $data['grifo'] = $grifo;
            if(!is_null($horometro)){
                $data['horometro_inicial'] = $horometro->inicio;
                $data['horometro_final'] = $horometro->fin;
            }else{
                $data['horometro_inicial'] = '-';
                $data['horometro_final'] = '-';
            }
            if(!is_null($tipocombustible_id) && $tipocombustible_id!=""){
                $data['tipocombustible'] = Tipocombustible::find($request->tipocombustible)->descripcion;
            }else{
                $data['tipocombustible'] = "TODOS";
            }
            $html = view('app.abast-combustible.pdf.control_diario_combustible', $data)->render();
            $mpdf = new Mpdf(['orientation' => 'L']);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML($html);
            $mpdf->Output($namefile, "I");
        }
    }
    
    public function generateReport(Request $request){
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Controldiario';
        $controldiario = null;
        $cboGrifo = Grifo::orderBy('descripcion')->get();
        $cboConcesionaria = Concesionaria::orderBy('razonsocial')->get();
        $cboContratista = Contratista::whereNotIn('id',[12,27,38])->orderBy('razonsocial','asc')->get();
        $cboTipocombustible = Tipocombustible::select('id','descripcion')->orderBy('descripcion')->get();
        $formData = array('controldiario.exportExcelReport');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $btnExcel    = 'Generar Reporte en Excel'; 
        $btnPdf1    = 'Reporte Subcontratista'; 
        $btnPdf2    = 'Reporte Vehiculo / Equipo'; 
        
        return view($this->folderview.'.generate')->with(compact('controldiario' ,'formData', 'entidad', 'btnExcel', 'btnPdf1', 'listar','btnPdf2', 'cboGrifo','cboConcesionaria','cboContratista','cboTipocombustible'));
    }
    
    public function exportExcelReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $typeReport = $request->input('typeExport');
        $filtro = $request->input('grifo');
        $contratista_id = $request->input('contratista');
        $lsConcesionaria = $request->input('concesionaria');
        $lsTipocombustible = $request->input('tipocombustible');
        $grifo = array();
        foreach($filtro as $k=>$v){
            $grifo[] = $v;
        }
        $concesionaria = array();
        foreach($lsConcesionaria as $k=>$v){
            $concesionaria[] = $v;
        }
        $tipocombustible = array();
        foreach($lsTipocombustible as $k=>$v){
            $tipocombustible[] = $v;
        }

        if ( is_null($start_date) || is_null($end_date) || is_null($typeReport) ) throw new Exception('Some input on exportExcelReport method is null');

        $dates = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        if ( $typeReport === 'subcontratista' ) {
            $namefile = 'ReporteSubcontratisa.pdf';

            $resultQuery1 = AbastecimientoCombustible::select(
                'abastecimiento_combustible.id as id','g.descripcion as grifo',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'),
                'c.dni as dni',
                'ua.codigo as code',
                DB::raw('(CASE WHEN eq.descripcion IS NULL THEN "-" ELSE eq.descripcion END) AS desceq'),
                'mc.descripcion as descmc', 
                DB::raw('(CASE WHEN eq.placa IS NULL THEN "-" ELSE eq.placa END) AS eqpl'), 
                'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','eq.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "equipo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> leftJoin('equipo as eq', 'eq.id', '=', 'equipo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'eq.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else eq.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('abastecimiento_combustible.vehiculo_id')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    //[ 'cd.tipo','like','imagen-firma']
                ])
                -> whereNotIn('ct.id',[12,27,38])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                -> where(function($sql) use($contratista_id){
                    if(!is_null($contratista_id) && $contratista_id!=""){
                        $sql->where('ct.id','=',$contratista_id);
                    }
                })
                ->orderBy('eq.contratista_id','asc')
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
            
            $resultQuery = AbastecimientoCombustible::select(
                'abastecimiento_combustible.id as id','g.descripcion as grifo',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'),
                'c.dni as dni',
                'ua.codigo as code',
                DB::raw('(CASE WHEN eq.modelo IS NULL THEN "-" ELSE eq.modelo END) AS desceq'),
                'mc.descripcion as descmc', 
                DB::raw('(CASE WHEN eq.placa IS NULL THEN "-" ELSE eq.placa END) AS eqpl'), 
                'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','eq.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "vehiculo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> Join('vehiculo as eq', 'eq.id', '=', 'vehiculo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'eq.marca_id')
                -> Join('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else eq.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    //[ 'cd.tipo','like','imagen-firma']
                ])
                -> whereNotIn('ct.id',[12,27,38])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                -> where(function($sql) use($contratista_id){
                    if(!is_null($contratista_id) && $contratista_id!=""){
                        $sql->where('ct.id','=',$contratista_id);
                    }
                })
                ->orderBy('eq.contratista_id','asc')
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc')
                -> unionAll($resultQuery1)
                -> get();

            $data['data'] = $resultQuery;
            $data['fechainicial'] = $start_date;
            $data['fechafinal'] = $end_date;
            if(count($concesionaria)==1){
                $data['concesionaria_id'] = $concesionaria[0];//$this->getConsecionariaActual();
            }else{
                $data['concesionaria_id'] = 0;
            }
            $data['preciocombustible'] = $request->input('preciocombustible');
            $data['listapiepagina'] = Piepagina::where('formato','like','Combustible')->orderBy('orden','asc')->get();
            $data['tipo'] = $request->input('tipo');
            if($request->input('tipo')=="pdf"){
                $html = view('app.abast-combustible.pdf.reportesubcontratista', $data)->render();
                $mpdf = new Mpdf(['orientation' => 'L']);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->SetTitle('Reporte Equipo Combustible');
                $mpdf->WriteHTML($html);
                $mpdf->Output($namefile, "I");
            }else{
                $html = view('app.abast-combustible.pdf.reportesubcontratista', $data)->render();
                return view('app.abast-combustible.excel.reportesubcontratista')->with(compact('html'));
            }
            
        } else if ( $typeReport === 'combustible' ) {
            $namefile = 'ReporteCombustible.pdf';

            $queryVehiculo2 = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "vehiculo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    [ 'cd.tipo','like','imagen-firma']
                ])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
                
            $queryVehiculo = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "equipo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('equipo as vh', 'vh.id', '=', 'equipo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> whereNull('abastecimiento_combustible.vehiculo_id')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    [ 'cd.tipo','like','imagen-firma']
                ])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc')
                -> union($queryVehiculo2);
                //->get();
                
            
            $querySql = $queryVehiculo->toSql();

            $queryVehiculo2 = DB::table(DB::raw("($querySql order by fecha desc) as a"))->mergeBindings($queryVehiculo)->get();

            $data['data'] = $queryVehiculo2;
            $data['fechainicial'] = $start_date;
            $data['fechafinal'] = $end_date;
            if(count($concesionaria)==1){
                $data['concesionaria_id'] = $concesionaria[0];//$this->getConsecionariaActual();
            }else{
                $data['concesionaria_id'] = 0;
            }
            $data['listapiepagina'] = Piepagina::where('formato','like','Combustible')->orderBy('orden','asc')->get();
            $data['tipo'] = $request->input('tipo');
            if($request->input('tipo')=="pdf"){
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                $mpdf = new Mpdf(['orientation' => 'L']);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->WriteHTML($html);
                $mpdf->SetTitle('Reporte Vehiculo Combustible');
                $mpdf->Output($namefile, "I");
            }else{
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                return view('app.abast-combustible.excel.reportecombustible')->with(compact('html'));
            }
        } else if ( $typeReport === 'combustible-vehiculo' ) {
            $namefile = 'ReporteCombustible.pdf';

            $queryVehiculo = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "vehiculo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    //[ 'cd.tipo','like','imagen-firma']
                ])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->where('abastecimiento_combustible.descuento','like','N')
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
                
            
            $queryVehiculo2 = $queryVehiculo->get();

            $data['data'] = $queryVehiculo2;
            $data['fechainicial'] = $start_date;
            $data['fechafinal'] = $end_date;
            if(count($concesionaria)==1){
                $data['concesionaria_id'] = $concesionaria[0];//$this->getConsecionariaActual();
            }else{
                $data['concesionaria_id'] = 0;
            }
            $data['listapiepagina'] = Piepagina::where('formato','like','Combustible')->orderBy('orden','asc')->get();
            $data['tipo'] = $request->input('tipo');
            if($request->input('tipo')=="pdf"){
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                $mpdf = new Mpdf(['orientation' => 'L']);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->WriteHTML($html);
                $mpdf->SetTitle('Reporte Vehiculo Combustible');
                $mpdf->Output($namefile, "I");
            }else{
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                return view('app.abast-combustible.excel.reportecombustible')->with(compact('html'));
            }
        } else if ( $typeReport === 'combustible-equipo' ) {
            $namefile = 'ReporteCombustible.pdf';
                
            $queryVehiculo = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "equipo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('equipo as vh', 'vh.id', '=', 'equipo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> whereNull('abastecimiento_combustible.vehiculo_id')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ]
                    //[ 'cd.tipo','like','imagen-firma']
                ])
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->where('abastecimiento_combustible.descuento','like','N')
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
                //->get();
                
            $queryVehiculo2 = $queryVehiculo->get();

            $data['data'] = $queryVehiculo2;
            $data['fechainicial'] = $start_date;
            $data['fechafinal'] = $end_date;
            if(count($concesionaria)==1){
                $data['concesionaria_id'] = $concesionaria[0];//$this->getConsecionariaActual();
            }else{
                $data['concesionaria_id'] = 0;
            }
            $data['listapiepagina'] = Piepagina::where('formato','like','Combustible')->orderBy('orden','asc')->get();
            $data['tipo'] = $request->input('tipo');
            if($request->input('tipo')=="pdf"){
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                $mpdf = new Mpdf(['orientation' => 'L']);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->WriteHTML($html);
                $mpdf->SetTitle('Reporte Equipo Combustible');
                $mpdf->Output($namefile, "I");
            }else{
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                return view('app.abast-combustible.excel.reportecombustible')->with(compact('html'));
            }
        } else if ( $typeReport === 'combustible-descuento' ) {
            $namefile = 'ReporteCombustible.pdf';

            $queryVehiculo2 = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "vehiculo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    [ 'cd.tipo','like','imagen-firma']
                ])
                -> where(function($sql) use($contratista_id){
                    if(!is_null($contratista_id) && $contratista_id!=""){
                        $sql->where('ct.id','=',$contratista_id);
                    }
                })
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                ->where('abastecimiento_combustible.descuento','like','S')
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
                
            $queryVehiculo = DB::table('abastecimiento_combustible')
                ->select(
                'abastecimiento_combustible.id as id',
                DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
                'c.dni as dni','tcom.descripcion as tipocombustible','vh.modelo',
                'ua.codigo as code','vh.placa',
                DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
                'mc.descripcion as descmc','g.descripcion as grifo',
                DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
                'km', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
                DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS contratista'),
                'cd.archivo as firma','vh.contratista_id',
                'abastecimiento_combustible.motivo','abastecimiento_combustible.qtdl','abastecimiento_combustible.qtdgl','abastecimiento_combustible.fecha_abastecimiento as fecha','concesionaria.razonsocial as concesionariaua','co.razonsocial as concesionariatrabajo',DB::raw('case when equipo_id is null and vehiculo_id is null then "-" else "equipo" end as tipo'),'abastecimiento_combustible.descuento')
                -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
                -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
                -> leftJoin('conductordocument as cd','cd.conductor_id','=','c.id')
                -> leftjoin('ua', 'ua.id', '=', 'ua_id')
                -> join('concesionaria','concesionaria.id','=','ua.concesionaria_id')
                -> join('concesionaria as co','co.id','=','abastecimiento_combustible.concesionaria_id')
                -> leftJoin('equipo as vh', 'vh.id', '=', 'equipo_id')
                -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
                -> leftJoin('contratista as ct', 'ct.id', '=', DB::raw('case when abastecimiento_combustible.vehiculo_id is null and abastecimiento_combustible.equipo_id is null then abastecimiento_combustible.contratista_id else vh.contratista_id end'))
                -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
                -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
                -> whereNull('abastecimiento_combustible.deleted_at')
                -> whereNull('cd.deleted_at')
                -> whereNull('abastecimiento_combustible.vehiculo_id')
                -> where([
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '>=', $start_date ],
                    [ 'abastecimiento_combustible.fecha_abastecimiento', '<=', $end_date ],
                    [ 'cd.tipo','like','imagen-firma']
                ])
                -> where(function($sql) use($contratista_id){
                    if(!is_null($contratista_id) && $contratista_id!=""){
                        $sql->where('ct.id','=',$contratista_id);
                    }
                })
                ->whereIn('abastecimiento_combustible.grifo_id',$grifo)
                ->whereIn('abastecimiento_combustible.tipocombustible_id',$tipocombustible)
                ->where('abastecimiento_combustible.descuento','like','S')
                //->whereIn('ua.concesionaria_id',$concesionaria)
                ->whereIn('abastecimiento_combustible.concesionaria_id',$concesionaria)
                ->orderBy('abastecimiento_combustible.fecha_abastecimiento','asc')
                -> union($queryVehiculo2);
                //->get();
                
            
            $querySql = $queryVehiculo->toSql();
            $queryVehiculo2 = DB::table(DB::raw("($querySql order by fecha desc) as a"))->mergeBindings($queryVehiculo)->get();

            $data['data'] = $queryVehiculo2;
            $data['fechainicial'] = $start_date;
            $data['fechafinal'] = $end_date;
            if(count($concesionaria)==1){
                $data['concesionaria_id'] = $concesionaria[0];//$this->getConsecionariaActual();
            }else{
                $data['concesionaria_id'] = 0;
            }
            $data['listapiepagina'] = Piepagina::where('formato','like','Combustible')->orderBy('orden','asc')->get();
            $data['tipo'] = $request->input('tipo');
            if($request->input('tipo')=="pdf"){
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                $mpdf = new Mpdf(['orientation' => 'L']);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->WriteHTML($html);
                $mpdf->SetTitle('Reporte Vehiculo Combustible');
                $mpdf->Output($namefile, "I");
            }else{
                $html = view('app.abast-combustible.pdf.reportecombustible', $data)->render();
                return view('app.abast-combustible.excel.reportecombustible')->with(compact('html'));
            }
        } else {
            throw new Exception("On exportExcelReport method, the value of \$typeExport has not found");
        }
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}

