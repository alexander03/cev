<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Repuesto;
use App\Unidad;
use App\Equipo;
use App\Vehiculo;
use App\RegRepVeh;
use App\Checklistvehicular;
use App\Ua;
use App\Regrepvehdocument;
use App\Taller;
use App\Diagnostico;
use App\Configuracion;
use App\Rules\SearchUaPadre;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Concesionaria;
use App\DescripcionRegRepVeh;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegRepVehExportExcel;
use App\Exports\RegRepVehExportExcel2;
use App\Exports\RegRepVehExportExcel3;
use App\Exports\RegRepVehExportExcelRepuesto;
use App\Exports\RegRepVehExportExcelList;
use App\Exports\RegRepVehExportResumenUsuarioDia;
use App\Exports\RegRepVehExportResumenUsuarioMes;
use App\Curl;

class RegRepVehController extends Controller
{
    protected $folderview      = 'app.regrepveh';
    protected $tituloAdmin     = 'Registro de Mantenimiento con Repuesto Vehicular';
    protected $tituloRegistrar = 'Registro de Mantenimiento con Repuesto Vehicular';
    protected $tituloModificar = 'Modificar Repuesto Vehicular';
    protected $tituloEliminar  = 'Eliminar Repuesto Vehicular';
    protected $tituloActivar  = 'Activar repuesto';
    protected $rutas           = array(
       // 'create' => 'regrepveh.createregrepveh',
        'createrepuesto' => 'regrepveh.createrepuesto',
        'buscarporua' => 'regrepveh.buscarporua',
        'edit'   => 'regrepveh.edit', 
        'delete' => 'regrepveh.eliminar',
        'activar' => 'regrepveh.activar',
        'search' => 'regrepveh.buscar',
        'store' => 'regrepveh.store',
        'index'  => 'regrepveh.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 4)->first()->valor));
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'RegRepVeh';
        $filter           = Libreria::getParam($request->input('filter'));
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;


        $fechainicio= $request->input('fechainicio');
        $fechafin= $request->input('fechafin');
        $tipofecha= $request->input('tipofecha');
        $tipomantenimiento= $request->input('tipomantenimiento');
        $situacion= $request->input('situacion');
        $tipo= $request->input('tipo');
        $documentos= $request->input('documentos');

        $docmat_id = 0;
        if($filter!=""){
            $data = DB::connection('mysql3')->select('select docmat.*
                from docmat
                where numero like "%'.$filter.'%"');
            if(count($data)>0){
                $data = $data[0];
                $docmat_id = $data->id;
            }
        }

        $resultado= RegRepVeh::leftjoin('vehiculo','vehiculo.id','=','regrepveh.ua_id')
                            ->leftjoin('equipo','equipo.id','=','regrepveh.equipo_id')

        ->where('regrepveh.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('regrepveh.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regrepveh.'.$tipofecha,'<=', $fechafin);
            })
        ->where(function($q) use ($filter){
        $q->where('regrepveh.cliente', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.ua_id', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.telefono', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.ordencompra', 'like', '%'.$filter.'%')
          ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%')
          ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.numero', 'like', '%'.$filter.'%')
          ->orWhere('regrepveh.fechasalida', 'like', '%'.$filter.'%');
     	})->where(function ($q) use($situacion){
            if($situacion!=""){
                $q->where('regrepveh.situacion','like',$situacion);
            }
        })->where(function ($q) use($docmat_id){
            if($docmat_id!="0"){
                $q->where('regrepveh.docmat_id','=',$docmat_id)
                    ->orWhere('regrepveh.docmat_id2','=',$docmat_id)
                    ->orWhere('regrepveh.docmat_id3','=',$docmat_id)
                    ->orWhere('regrepveh.docmat_id4','=',$docmat_id)
                    ->orWhere('regrepveh.ordencompra','like','%%');
            }
        })->where(function ($q) use($documentos){
            if($documentos!=""){
                if($documentos=="NO"){
                    $q->whereRaw('(select count(*) from regrepvehdocument where regrepveh_id=regrepveh.id and deleted_at is null) = 0');
                }else{
                    $q->whereRaw('(select count(*) from regrepvehdocument where regrepveh_id=regrepveh.id and deleted_at is null) > 0');
                }
            }
        });

        if($tipomantenimiento!=""){
            $resultado = $resultado->where('regrepveh.tipomantenimiento','=',$tipomantenimiento);
        }


        $lista            = $resultado->select('regrepveh.*',DB::raw('(select count(*) from regrepvehdocument where regrepveh_id=regrepveh.id and deleted_at is null) as documentos'))
                            ->orderBy('fechamantenimiento','desc')
                            ->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Numero', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Taller', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro DOCMAT', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DOCMAT', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km Inicial', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km Final', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha DOCMAT', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Cotizacion/Salida', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario Reg', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Opciones', 'numero' => '4');
        }

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_activar  = $this->tituloActivar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_registrar', 'titulo_activar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'RegRepVeh';
        $title            = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipo=array('' => 'Todos','1'=>'Preventivo','2'=>'Correctivo');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title','tituloRegistrar', 'ruta','cboTipo'));
    }


    public function createrepuesto(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'RegRepVeh';
        $regrepveh = null;
        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $oObservaciones=array(new DescripcionRegRepVeh());
        $oObservaciones[0]->id=-1 ; 
        $uades="";
        $placa="";
        $vehiculo_id="";
        $equipo_id="";
        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;
        //$oTipos=array('' => 'Seleccione Tipo');
        $oTipos=array('' => 'Seleccione Tipo');
        $oTipos=array('1' => 'Preventivo');
        $oTipos+=array('2' => 'Correctivo');
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $formData = array('regrepveh.createregrepveh');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboDiagnostico = array('0'=>'--Seleccione--');// + Diagnostico::orderBy('numero','asc')->pluck('numero','id')->toArray();
        $lista = RegRepVeh::whereNull('numero')->orderBy('id','asc')->get();
        foreach ($lista as $key => $value) {
            $data = RegRepVeh::whereNotNull('numero')->where('fecharegistro','<=',$value->fecharegistro)->whereRaw('SUBSTRING(fecharegistro,1,4) like \''.date("Y",strtotime($value->fecharegistro)).'\'')->orderBy('id','desc')->orderBy('numero','desc')->first();
            if(is_null($data)){
                $numero = "1-".date("Y",strtotime($value->fecharegistro));
            }else{
                $numero = (explode("-", $data->numero)[0] + 1).date("-Y",strtotime($value->fecharegistro)); 
                $existe = RegRepVeh::where('numero','like',$numero)->first();
                if(!is_null($existe)){
                    $numero = (explode("-", $data->numero)[0] + 2).date("-Y",strtotime($value->fecharegistro)); 
                }
            }
            $value->numero = $numero;
            $value->save();
        }
        $cboDocmat = array('0'=>'--Seleccione--');
        $cboDocmat2 = array('0'=>'--Seleccione--');
        $cboDocmat3 = array('0'=>'--Seleccione--');
        $cboDocmat4 = array('0'=>'--Seleccione--');
        return view($this->folderview.'.mant2')->with(compact('regrepveh', 'oTipos','formData', 'entidad','oConcesionaria','idconc','uades','placa','vehiculo_id','oObservaciones', 'boton', 'listar','equipo_id','cboTaller','cboDiagnostico','cboDocmat','cboDocmat2','cboDocmat3','cboDocmat4'));
    }

    public function buscarporua(Request $request){
        $resultado = Ua::where('codigo', '=',$request->get("ua"))->get();
        
        if (count($resultado)>0) {
            $eq = Equipo::where('ua_id', '=',$resultado[0]->id)->get();

            if (count($eq)>0) {
                return json_encode(array('gg'=>"UA correcta: ".$eq[0]['descripcion']));
            }else{
            return json_encode(array('gg'=>'LA UA NO TIENE EQUIPO'));}
        }else{
        return json_encode(array('gg'=>'NO EXISTE UA'));}
    }

    public function createchecklistvehicular(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'RegRepVeh';
        $repuesto = null;
        $formData = array('repuestos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        $arrUnidades = Unidad::getAll();
        $cboUnidades = array('' => 'Seleccione');
        foreach($arrUnidades as $k=>$v){
            $cboUnidades += array($v->id=>$v->descripcion);
        }
        return view($this->folderview.'.mant_checklistvehicular')->with(compact('repuesto', 'formData', 'entidad', 'boton', 'cboUnidades', 'listar'));
    }

    public function store(Request $request){
        //BUSCAR EQUIPO/VEHICULO KM
        $id = $request->input('equipo_id')+0;
        $equipo = Equipo::where('id','=', $id)->first();
        /*if($equipo != null) {
            $obj = RegRepVeh::where('equipo_id','=',$equipo->id)->where('fechasalida','<=',$request->input('fechaentrada'))->orderBy('fechasalida','desc')->first();
            if(!is_null($obj) && ($request->input('kminicial')+0)< ($obj->kmfinal+0)){
                $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es: ".$obj->kmfinal." del ".$obj->fechaentrada],"id"=>$obj->id);
                return json_encode($dat);
            }
        }else {
            $vehiculo = Vehiculo::where('id','=', $request->input('vehiculo_id'))->first();
            $obj = RegRepVeh::where('ua_id','=',$vehiculo->id)->where('fechasalida','<=',$request->input('fechaentrada'))->orderBy('fechasalida','desc')->first();
            if(!is_null($obj) && ($request->input('kminicial')+0)< ($obj->kmfinal+0)){
                $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es:  ".$obj->kmfinal." del ".$obj->fechaentrada],"id"=>$obj->id);
                return json_encode($dat);
            }
        }*/
        if($request->input('situacion')=="F" && auth()->user()->tipouser_id!="1"){//todos los usuario menos el admin

            $reglas = [
                'concesionaria_id' => 'required',
                //'cliente' => 'required|max:250',
                'titulo' => 'required',
                //'ordencompra' => 'required|max:250',
                'vehiculo_id' => 'required',
                'fechaentrada' => 'required',
                'fechasalida' => 'required',
                'fechamantenimiento' => 'required',
                'kmman' => 'required|lte:kmfinal',
                'kminicial' => 'required|numeric',
                'kmfinal' => 'required|numeric|gte:kminicial',
                'tipomantenimiento' => 'required',
                //'telefono' => 'required',
                'cantidad.*' => 'required',
                'monto.*' => 'required',
                'cuantasdescripciones'=>'required'
            ];

            $mensajes = [
                //'cliente.required' => 'Cliente Campo Vacío',
                'titulo.required' => 'Debe ingresar una descripcion para el mantenimiento',
                //'ordencompra.required' => 'DOCMAT Campo Vacio',
                'cliente.max' => 'Nombre de Cliente muy extenso, máximo 250 caracteres',
                //'ordencompra.max' => 'Orden de Compra muy extensa, máximo 250 caracteres',
                'vehiculo_id.required' => 'Seleccione un vehiculo válido',
                'fechaentrada.required' => 'Fecha Entrada Campo Vacio',
                'fechasalida.required' => 'Fecha Salida Campo Vacio',
                'fechamantenimiento.required' => 'Fecha Mant. Campo Vacio',
                'kmman.required' => 'Km de Mantenimiento Campo Vacio',
                'kmman.lte' => 'Km de Mantenimiento debe ser menor o igual al Km Final ',
                'kminicial.required' => 'Km Inicial Campo Vacío',
                'kmfinal.required' => 'Km Final Campo Vacío',
                'kmfinal.gte' => 'Km Final debe ser mayor a Km Inicial',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
                //'telefono.required' => 'Teléfono Campo Vacío',
                'cantidad.*.required' => 'Ingrese Cantidad todas las Observaciones',
                'monto.*.required' => 'Ingrese Monto todas las Observaciones',
                'cuantasdescripciones.required' => 'Ingrese al menos una Observación'
            ];
            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $dat = array("situacion" => ["No se puede finalizar sin DOCUMENTOS adjuntos"],"id"=>0);
            return json_encode($dat);

            if($request->input('docmat_id')=="0" && $request->input('telefono')==""){
                $dat = array("docmat_id" => ["No se puede finalizar sin DOCMAT asignado"],"id"=>0);
                return json_encode($dat);
            }
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');

        $idn=DB::select("SHOW TABLE STATUS LIKE 'regrepveh'");
        $next_id=$idn[0]->Auto_increment;

        
        $error = DB::transaction(function() use($request,&$next_id){
            $data = RegRepVeh::whereNotNull('numero')->where('fecharegistro','<=',date("Y-m-d"))->whereRaw('SUBSTRING(fecharegistro,1,4) like \''.date("Y").'\'')->orderBy('id','desc')->orderBy('numero','desc')->first();
            if(is_null($data)){
                $numero = "1-".date("Y");
            }else{
                $numero = (explode("-", $data->numero)[0] + 1).date("-Y"); 
                $existe = RegRepVeh::where('numero','like',$numero)->first();
                if(!is_null($existe)){
                    $numero = (explode("-", $data->numero)[0] + 2).date("-Y"); 
                }
            }

            $regrepv = new RegRepVeh();
            $regrepv -> numero =$numero;
            $regrepv -> cliente  = $request -> input('cliente');
            $regrepv -> titulo  = $request -> input('titulo');
            $regrepv -> ordencompra  = $request -> input('ordencompra');
            $regrepv -> docmat_id  = $request -> input('docmat_id');
            $regrepv -> concesionaria_id  = $request -> input('concesionaria_id');
            $regrepv -> taller_id = $request -> input('taller_id');
            $regrepv -> ua_id = $request -> input('vehiculo_id');
            $regrepv -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $regrepv -> equipo_id = $request -> input('equipo_id');
            $regrepv -> kminicial = $request -> input('kminicial');
            $regrepv -> kmman = $request -> input('kmman');
            $regrepv -> kmfinal = $request -> input('kmfinal');
            $regrepv -> fechaentrada = $request -> input('fechaentrada');
            $regrepv -> fechasalida = $request -> input('fechasalida');
            $regrepv -> fechamantenimiento = $request -> input('fechamantenimiento');
            $regrepv -> fecharegistro = now();
            $regrepv -> tipomantenimiento = $request -> input('tipomantenimiento');
            $regrepv -> telefono = $request -> input('telefono');
            $regrepv -> otros = $request -> input('otros');
            $regrepv -> situacion = $request -> input('situacion');
            $regrepv -> diagnostico_id = $request -> input('diagnostico_id');
            $regrepv -> fecharegistro = date("Y-m-d");
            $regrepv -> user_id = auth()->user()->id;
            $regrepv -> docmat_id2  = $request -> input('docmat_id2');
            $regrepv -> docmat_id3  = $request -> input('docmat_id3');
            $regrepv -> docmat_id4  = $request -> input('docmat_id4');
            $regrepv -> save();
            $next_id = $regrepv->id;
            event( new UserHasCreatedOrDeleted($regrepv->id,'regrepveh', auth()->user()->id,'crear'));
            
            if($request->input('vehiculo_id')!="0"){
                $vehiculo=Vehiculo::find($request -> input('vehiculo_id'));
                if(($request->input('kmfinal')+0)>$vehiculo->kilometraje_act){
                    $vehiculo->kilometraje_act = $request->input('kmfinal');
                    $vehiculo->save();
                }
                /*if($regrepv->tipomantenimiento=="1"){
                    $vehiculo->kilometraje_act = $regrepv->kmfinal;
                    $vehiculo->kilometraje_rec = 0;
                    $vehiculo->save();

                    $regrepv->especial = true;
                    $regrepv->save();
                }*/
            }
        });

        $cantidades=$request->cantidad;
        $repuestosid=$request->repuestoid;
        $montos=$request->monto;
        if($cantidades!=""){
            for ($i = 0; $i < count($cantidades); $i++) {
                $error = DB::transaction(function() use($i,$request,$next_id,$cantidades,$repuestosid,$montos){
                    $descripcionregrepv = new DescripcionRegRepVeh();
                    $descripcionregrepv -> regrepveh_id =$next_id;
                    $descripcionregrepv -> cantidad  = $cantidades[$i];
                    $descripcionregrepv -> repuesto_id  = intval($repuestosid[$i]);
                    $descripcionregrepv -> monto = $montos[$i];;
                    $descripcionregrepv -> save();

                    //event( new UserHasCreatedOrDeleted($descripcionregrepv->id,'descripcionregrepveh', auth()->user()->id,'crear'));
                }); 
            }
        }

        $cc = new Curl(false,'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegRepVehicular.php');
        $url = 'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegManVehicular.php?id='.$next_id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);


        return is_null($error) ? "OK" : $error;
    }


    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'regrepveh');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = RegRepVeh::find($id);
        $entidad  = 'RegRepVeh';
        $formData = array('route' => array('regrepveh.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'regrepveh');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $regrepveh = RegRepVeh::find($id);
            event( new UserHasCreatedOrDeleted($regrepveh->id,'regrepveh', auth()->user()->id,'eliminar'));
            $regrepveh->delete();
        });
        return is_null($error) ? "OK" : $error;
    }



    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'regrepveh');
        if ($existe !== true) {
            return $existe;
        }

        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();

        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $regrepveh = RegRepVeh::find($id);
        $oObservaciones=DescripcionRegRepVeh::where('regrepveh_id','=',$id)
            ->join('repuesto', 'descripcionregrepveh.repuesto_id', '=', 'repuesto.id')
            ->join('unidad', 'repuesto.unidad_id', '=', 'unidad.id')
            ->select('descripcionregrepveh.id as id','descripcionregrepveh.monto as monto','descripcionregrepveh.cantidad as cantidad','repuesto.codigo as codigo','repuesto.id as repuesto_id','repuesto.descripcion as descripcion','unidad.descripcion as unidad')
        ->get();
        if(!is_null($regrepveh->ua_id) && $regrepveh->ua_id!="" && $regrepveh->ua_id!="0"){
            $oua = Vehiculo::where('id','=',$regrepveh->ua_id)->get();
        }else{
            $oua = Equipo::where('id','=',$regrepveh->equipo_id)->get();
        }
        $placa= $oua[0]->placa;
        $oua2 = Ua::where('id','=',$oua[0]->ua_id)->get();
        $uades= $oua2[0]->codigo;
        $uades=$uades;
        $vehiculo_id=$regrepveh->ua_id;
        $equipo_id=$regrepveh->equipo_id;
        $entidad  = 'RegRepVeh';
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        if(!is_null($regrepveh->diagnostico)){
            $cboDiagnostico = Diagnostico::where('id','=',$regrepveh->diagnostico_id)->pluck('numero','id')->toArray();
        }else{
            $cboDiagnostico = array('0'=>'--Seleccione--');// + Diagnostico::orderBy('numero','asc')->pluck('numero','id')->toArray();
        }
        if(!is_null($regrepveh->docmat_id) && $regrepveh->docmat_id>0){
            $cboDocmat = array($regrepveh->docmat_id=>$regrepveh->ordencompra);
        }else{
            $cboDocmat = array('0'=>'--Seleccione--');
        }
        if(!is_null($regrepveh->docmat_id2) && $regrepveh->docmat_id2>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regrepveh->docmat_id2)[0];
            $cboDocmat2 = array($regrepveh->docmat_id2=>$data->numero);
        }else{
            $cboDocmat2 = array('0'=>'--Seleccione--');
        }
        if(!is_null($regrepveh->docmat_id3) && $regrepveh->docmat_id3>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regrepveh->docmat_id3)[0];
            $cboDocmat3 = array($regrepveh->docmat_id3=>$data->numero);
        }else{
            $cboDocmat3 = array('0'=>'--Seleccione--');
        }
        if(!is_null($regrepveh->docmat_id4) && $regrepveh->docmat_id4>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regrepveh->docmat_id4)[0];
            $cboDocmat4 = array($regrepveh->docmat_id4=>$data->numero);
        }else{
            $cboDocmat4 = array('0'=>'--Seleccione--');
        }
        $formData = array('regrepveh.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant2')->with(compact('regrepveh','oConcesionaria','idconc','placa','uades','vehiculo_id','oObservaciones', 'formData', 'entidad', 'boton', 'listar','equipo_id','cboTaller','cboDiagnostico','cboDocmat','cboDocmat2','cboDocmat3','cboDocmat4'));
    }

    public function update(Request $request, $id)
    {
        if($request->input('situacion')=="F" && auth()->user()->tipouser_id!="1"){//todos los usuario menos el admin
            $reglas = [
                'concesionaria_id' => 'required',
                //'cliente' => 'required|max:250',
                'titulo' => 'required',
                //'ordencompra' => 'required|max:250',
                'vehiculo_id' => 'required',
                'fechaentrada' => 'required',
                'fechasalida' => 'required',
                'fechamantenimiento' => 'required',
                'kmman' => 'required|lte:kmfinal',
                'kminicial' => 'required|numeric',
                'kmfinal' => 'required|numeric|gte:kminicial',
                'tipomantenimiento' => 'required',
                //'telefono' => 'required',
                'cantidad.*' => 'required',
                'monto.*' => 'required',
                'cuantasdescripciones'=>'required'
            ];

            $mensajes = [
                //'cliente.required' => 'Cliente Campo Vacío',
                'titulo.required' => 'Debe ingresar una descripcion para el mantenimiento',
                //'ordencompra.required' => 'Orden compra Campo Vacio',
                'cliente.max' => 'Nombre de Cliente muy extenso, máximo 250 caracteres',
                //'ordencompra.max' => 'Orden de Compra muy extensa, máximo 250 caracteres',
                'vehiculo_id.required' => 'Seleccione un vehiculo válido',
                'fechaentrada.required' => 'Fecha Entrada Campo Vacio',
                'fechasalida.required' => 'Fecha Salida Campo Vacio',
                'fechamantenimiento.required' => 'Fecha Mant. Campo Vacio',
                'kmman.required' => 'Km de Mantenimiento Campo Vacio',
                'kmman.lte' => 'Km de Mantenimiento debe ser menor o igual al Km Final ',
                'kminicial.required' => 'Km Inicial Campo Vacío',
                'kmfinal.required' => 'Km Final Campo Vacío',
                'kmfinal.gte' => 'Km Final debe ser mayor a Km Inicial',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
                //'telefono.required' => 'Campo Vacío',
                'cantidad.*.required' => 'Ingrese Cantidad todas las Observaciones',
                'monto.*.required' => 'Ingrese Monto todas las Observaciones',
                'cuantasdescripciones.required' => 'Ingrese al menos una Observación'
            ];

            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
            $documentos = Regrepvehdocument::where('regrepveh_id','=',$id)->first();
            if(is_null($documentos)){
                $dat = array("situacion" => ["No se puede finalizar sin DOCUMENTOS adjuntos"],"id"=>0);
                return json_encode($dat);
            }
            /*if($request->input('diagnostico_id')=="0"){
                $dat = array("situacion" => ["No se puede finalizar sin DIAGNOSTICO asignado"],"id"=>0);
                return json_encode($dat);
            }*/
            if($request->input('docmat_id')=="0" && $request->input('telefono')==""){
                $dat = array("docmat_id" => ["No se puede finalizar sin DOCMAT asignado"],"id"=>0);
                return json_encode($dat);
            }
        }

        $error = DB::transaction(function() use($request, $id){
            $regrepv = RegRepVeh::find($id);

            $regrepvOrg = json_encode($regrepv);

            $regrepv -> cliente  = $request -> input('cliente');
            $regrepv -> titulo  = $request -> input('titulo');
            $regrepv -> ordencompra  = $request -> input('ordencompra');
            $regrepv -> docmat_id  = $request -> input('docmat_id');
            $regrepv -> concesionaria_id  = $request -> input('concesionaria_id');
            $regrepv -> taller_id = $request -> input('taller_id');
            $regrepv -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $regrepv -> ua_id = $request -> input('vehiculo_id');
            $regrepv -> equipo_id = $request -> input('equipo_id');
            $regrepv -> kmman = $request -> input('kmman');
            $regrepv -> kminicial = $request -> input('kminicial');
            $regrepv -> kmfinal = $request -> input('kmfinal');
            $regrepv -> fechaentrada = $request -> input('fechaentrada');
            $regrepv -> fechasalida = $request -> input('fechasalida');
            $regrepv -> fechamantenimiento = $request -> input('fechamantenimiento');
            $regrepv -> tipomantenimiento = $request -> input('tipomantenimiento');
            $regrepv -> telefono = $request -> input('telefono');
            $regrepv -> otros = $request -> input('otros');
            $regrepv -> situacion = $request -> input('situacion');
            $regrepv -> diagnostico_id = $request -> input('diagnostico_id');
            $regrepv -> docmat_id2  = $request -> input('docmat_id2');
            $regrepv -> docmat_id3  = $request -> input('docmat_id3');
            $regrepv -> docmat_id4  = $request -> input('docmat_id4');
            
            $regrepv -> save();

            event( new UserHasEdited($regrepvOrg,$regrepv,'regrepveh', auth()->user()->id));
        });

        if($request->input('vehiculo_id')!="0"){
            $vehiculo=Vehiculo::find($request -> input('vehiculo_id'));
            if(($request->input('kmfinal')+0)>$vehiculo->kilometraje_act){
                $vehiculo->kilometraje_act = $request->input('kmfinal');
                $vehiculo->save();
            }
        }

        
        $ids=$request->idobservacion;
        $montos=$request->monto;
        $cantidades=$request->cantidad;
        $repuestosid=$request->repuestoid;
        if($cantidades!=""){
            for ($i = 0; $i < count($cantidades); $i++) {
               $error = DB::transaction(function() use($id,$i,$ids,$request,$cantidades,$repuestosid,$montos){
                $descripcionregrepv = new DescripcionRegRepVeh();

                $descripcionregrepvOrg = $descripcionregrepv;

                if($ids[$i]>=0){$descripcionregrepv = DescripcionRegRepVeh::find($ids[$i]);}
                $descripcionregrepv -> regrepveh_id =$id;
                $descripcionregrepv -> cantidad  = $cantidades[$i];
                $descripcionregrepv -> repuesto_id  = $repuestosid[$i];
                $descripcionregrepv -> monto = $montos[$i];;
                $descripcionregrepv -> save();

                if($ids[$i]>=0){
                    event( new UserHasEdited($descripcionregrepvOrg,$descripcionregrepv,'descripcionregrepveh', auth()->user()->id));
                }else{
                    event( new UserHasCreatedOrDeleted($descripcionregrepv->id,'descripcionregrepveh', auth()->user()->id,'crear'));
                }
            }); 
            }
        }

        $idseliminados=$request->idseliminados;

       /* echo "<script>console.log('Debug idobservacion: " . print_r($ids) . "' );</script>";
         echo "<script>console.log('Debug idobservacion: " . print_r($montos) . "' );</script>";
        echo "<script>console.log('Debug repuestosid: " . print_r($repuestosid) . "' );</script>";
        echo "<script>console.log('Debug idseliminados: " . print_r($idseliminados) . "' );</script>";
        */if($idseliminados!=null){
            foreach($idseliminados as $k=>$idd){
                $error = DB::transaction(function() use($idd){
                    if($idd>0){
                $regrepveh = DescripcionRegRepVeh::find($idd);
                $regrepveh->delete();}
                }); 
            }
        }   

        return is_null($error) ? "OK" : $error;
    }


    public function searchAutocompleteRepuesto($query){

        $consulta = "select repuesto.id as id,repuesto.codigo as codigo, repuesto.descripcion as descripcion, CONCAT(`codigo`, ' - ', repuesto.descripcion) as 'search', unidad.descripcion as unidad
            from repuesto inner join unidad on repuesto.unidad_id = unidad.id
            where repuesto.deleted_at IS NULL AND
            (`codigo` LIKE '%".$query."%' OR repuesto.descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }
    public function searchAutocompleteVehiculo($query){

        $consulta = "select * from (select vehiculo.id as id, vehiculo.placa as placa,ua.codigo  as codigoua, ua.descripcion as ua,
            CONCAT(vehiculo.placa,' - ', ua.codigo, ' - ', ua.descripcion) as 'search','vehiculo' as tipo
            from vehiculo inner join ua on vehiculo.ua_id = ua.id
            where vehiculo.deleted_at IS NULL AND 
            vehiculo.concesionaria_id = {$this -> getConsecionariaActual()} AND
            (ua.codigo LIKE '%".$query."%' OR vehiculo.placa LIKE '%".$query."%') union all 
            select equipo.id as id, equipo.placa as placa,ua.codigo  as codigoua, ua.descripcion as ua,
            CONCAT(equipo.placa,' - ', ua.codigo, ' - ', ua.descripcion) as 'search','equipo' as tipo
            from equipo inner join ua on equipo.ua_id = ua.id
            where equipo.deleted_at IS NULL AND equipo.mantenimiento like 'S' and
            ua.concesionaria_id = {$this -> getConsecionariaActual()} AND
            (ua.codigo LIKE '%".$query."%' OR equipo.placa LIKE '%".$query."%')) as T";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }


    public function generatePDF(Request $request) {
        $id=$request->id;
        if ( $request->id == null || !is_numeric($request->id) ) return;

        $namefile = 'RegistroDeRepuestoVehicular - '.time().'.pdf';  
        
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
        ->where('users.id','=',auth()->user()->id)
        ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $ConcAct=$ConcesionariaActual[0]->razonsocial;
        $regrepveh = RegRepVeh::find($id);
        $oObservaciones=DescripcionRegRepVeh::where('regrepveh_id','=',$id)
            ->join('repuesto', 'descripcionregrepveh.repuesto_id', '=', 'repuesto.id')
            ->join('unidad', 'repuesto.unidad_id', '=', 'unidad.id')
            ->select('descripcionregrepveh.id as id','descripcionregrepveh.monto as monto','descripcionregrepveh.cantidad as cantidad','repuesto.codigo as codigo','repuesto.id as repuesto_id','repuesto.descripcion as descripcion','unidad.descripcion as unidad')
        ->get();
        

        $data=[];
        $data['concesionaria'] = $ConcAct;
        if($regrepveh->taller_id>0){
            $data['cliente'] = $regrepveh->taller->razonsocial;
        }else{
            $data['cliente'] = "-";
        }
        $data['ordencompra'] = $regrepveh->ordencompra;
        $data['ua_id'] = $regrepveh->ua_id;
        $data['tipomantenimiento']=$regrepveh->tipomantenimiento==1?'Preventivo':'Correctivo';
        $data['kmman'] = $regrepveh->kmman;
        $data['kminicial'] = $regrepveh->kminicial;
        $data['kmfinal'] = $regrepveh->kmfinal;
        $data['telefono'] = $regrepveh->telefono;
        //$data['fechaentrada'] = $regrepveh->fechaentrada;
        $data['fechaentrada'] = $regrepveh->fechamantenimiento;
        $data['fechasalida'] = $regrepveh->fechasalida;
        $data['fecharegistro'] = date("d/m/Y",strtotime($regrepveh->fecharegistro));
        $data['regrepveh'] = $regrepveh;
        $data['observaciones'] = $oObservaciones;
        $data['namefile'] = $namefile;
        $data['concesionaria_id'] = $ConcesionariaActual[0]->id;
        
        $posibleequipo=Equipo::join('ua','equipo.ua_id','=','ua.id')
        ->join('marca','equipo.marca_id','=','marca.id')->where('equipo.id','=',$regrepveh->equipo_id)
        ->select('equipo.descripcion as unidad','equipo.placa as placa','equipo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();

        $posiblevehiculo=Vehiculo::join('ua','vehiculo.ua_id','=','ua.id')
        ->join('marca','vehiculo.marca_id','=','marca.id')->where('vehiculo.id','=',$regrepveh->ua_id)
        ->select('vehiculo.placa as placa','vehiculo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();
        
        if(count($posibleequipo)>0||count($posiblevehiculo)>0){
            if(count($posibleequipo)>0){
                $data['unidad'] = $posibleequipo[0]->unidad;
                $data['placa'] = $posibleequipo[0]->placa;
                $data['modelo'] = $posibleequipo[0]->modelo;
                $data['marca'] = $posibleequipo[0]->marca;
                $data['uacodigo'] = $posibleequipo[0]->uacodigo;
            }else{
                $data['unidad'] = '--';
                $data['placa'] = $posiblevehiculo[0]->placa;
                $data['modelo'] = $posiblevehiculo[0]->modelo;
                $data['marca'] = $posiblevehiculo[0]->marca;
                $data['uacodigo'] = $posiblevehiculo[0]->uacodigo;
            }
        }else{
            $data['unidad'] = '--';
            $data['placa'] = '--';
            $data['modelo'] = '--';
            $data['marca'] = '--';
            $data['uacodigo'] = '--';
        }

        // dd($data);
        $html = view('app.regrepveh.pdf.template_individual', $data)->render();

        $mpdf = new Mpdf(['tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        $mpdf->Output($namefile, "I");
    }
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
    
    public function exportExcel(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $precio = $request->precio;
        
        return Excel::download(new RegRepVehExportExcel($fecha1,$fecha2,$precio,$concesionaria), 'ReporteDetalladoVehiculo.xlsx');
    }
    
    public function exportExcel2(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $precio = $request->precio;
        
        return Excel::download(new RegRepVehExportExcel2($fecha1,$fecha2,$precio,$concesionaria), 'RegistroConsolidadoVehiculo.xlsx');
    }

    public function exportExcel3(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        
        return Excel::download(new RegRepVehExportExcel3($fecha1,$fecha2,$concesionaria), 'ReporteMantChecklist.xlsx');
    }

    public function exportExcelRepuesto(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $repuesto = $request->repuesto;
        $filter = $request->filter;
        
        return Excel::download(new RegRepVehExportExcelRepuesto($fecha1,$fecha2,$repuesto,$concesionaria,$filter), 'ReporteMantRepuesto.xlsx');
    }

    public function exportExcelList(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $tipofecha= $request->input('tipofecha');
        $filter= $request->input('filter');
        
        return Excel::download(new RegRepVehExportExcelList($fecha1,$fecha2,$concesionaria,$tipofecha,$filter), 'ReporteMantRep.xlsx');
    }

    public function exportResumenUsuarioDia(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $repuesto = $request->repuesto;
        $filter = $request->filter;
        $tipofecha = $request->tipofecha;
        
        return Excel::download(new RegRepVehExportResumenUsuarioDia($fecha1,$fecha2,$concesionaria,$tipofecha,$filter), 'ReporteResumenUsuarioDia.xlsx');
    }

    public function exportResumenUsuarioMes(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $repuesto = $request->repuesto;
        $filter = $request->filter;
        $tipofecha = $request->tipofecha;
        
        return Excel::download(new RegRepVehExportResumenUsuarioMes($fecha1,$fecha2,$concesionaria,$tipofecha,$filter), 'ReporteResumenUsuarioMes.xlsx');
    }

    public function comboChecklist(Request $request){
        $combo = "<select id='checklistvehicular_id' name='checklistvehicular_id' class='form-control'>";
        if($request->input('vehiculo_id')!="0"){
            $data = Checklistvehicular::where('vehiculo_id','=',$request->input('vehiculo_id'))->orderBy('fecha_registro','desc')->limit(100)->get();
        }else{
            $data = Checklistvehicular::where('equipo_id','=',$request->input('equipo_id'))->orderBy('fecha_registro','desc')->limit(100)->get();
        }
        foreach ($data as $key => $value) {
            $sistema_electrico = ($value->sistema_electrico);
            $sistema_mecanico = ($value->sistema_mecanico);
            $accesorios = ($value->accesorios);
            $documentos = ($value->documentos);
            $revisiones = "";
            foreach ($sistema_electrico as $item){
                if($item['estado']==''){
                    $revisiones .= '* '.$item['titulo']."<br />";
                }
            }
            foreach ($sistema_mecanico as $item){
                if($item['estado']==''){
                    $revisiones .= '* '.$item['titulo']."<br />";
                }
            }
            foreach ($accesorios as $item){
                if($item['estado']==''){
                    $revisiones .= '* '.$item['titulo']."<br />";
                }
            }
            foreach ($documentos as $item){
                if($item['estado']==''){
                    $revisiones .= '* '.$item['titulo']."<br />";
                }
            }
            $combo.="<option value='".$value->id."'>".date("d/m/Y",strtotime($value->fecha_registro))." ".substr($value->hora_inicio,0,5)." - ".substr($value->hora_fin,0,5)." | ".$value->conductor->nombres." ".$value->conductor->apellidos." | ".$value->observaciones." ".$value->incidentes." | ".$revisiones."</option>";
        }
        $combo.="</select>";
        return $combo;
    }
}