<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Trabajo;
use App\Unidad;
use App\Equipo;
use App\Vehiculo;
use App\Checklistvehicular;
use App\RegManVeh;
use App\Regmanvehdocument;
use App\RegRepVeh;
use App\Ua;
use App\Diagnostico;
use App\Taller;
use App\Rules\SearchUaPadre;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Concesionaria;
use App\DescripcionRegManVeh;
use App\Configuracion;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegManVehExportExcelList;
use App\Exports\RegManVehExportExcelTodos;
use App\Curl;
use Illuminate\Support\Facades\Storage;

class RegManVehController extends Controller
{
    protected $folderview      = 'app.regmanveh';
    protected $tituloAdmin     = 'Registro de Mantenimiento Vehicular';
    protected $tituloRegistrar = 'Registro de Mantenimiento Vehicular';
    protected $tituloModificar = 'Modificar Mantenimiento Vehicular';
    protected $tituloEliminar  = 'Eliminar Mantenimiento Vehicular';
    protected $tituloActivar  = 'Activar mantenimiento';
    protected $rutas           = array(
       // 'create' => 'regmanveh.createregmanveh',
        'createtrabajo' => 'regmanveh.createtrabajo',
        'buscarporua' => 'regmanveh.buscarporua',
        'edit'   => 'regmanveh.edit', 
        'delete' => 'regmanveh.eliminar',
        'activar' => 'regmanveh.activar',
        'search' => 'regmanveh.buscar',
        'store' => 'regmanveh.store',
        'index'  => 'regmanveh.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
        define("MIN_DIAS", trim(Configuracion::where("id", "=", 5)->first()->valor));
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'RegManVeh';
        $filter           = Libreria::getParam($request->input('filter'));
		$ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

		$fechainicio= $request->input('fechainicio');
        $fechafin= $request->input('fechafin');
        $tipofecha= $request->input('tipofecha');
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

        $resultado= RegManVeh::leftjoin('vehiculo','vehiculo.id','=','regmanveh.ua_id')
                            ->leftjoin('equipo','equipo.id','=','regmanveh.equipo_id')
        ->where('regmanveh.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('regmanveh.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('regmanveh.'.$tipofecha,'<=', $fechafin);
            })
        ->where(function($q) use ($filter){
        $q->where('regmanveh.cliente', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.ua_id', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.ordencompra', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.telefono', 'like', '%'.$filter.'%')
          ->orWhere('vehiculo.placa', 'like', '%'.$filter.'%')
          ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.numero', 'like', '%'.$filter.'%')
          ->orWhere('regmanveh.fechasalida', 'like', '%'.$filter.'%');
     	})->where(function ($q) use($situacion){
            if($situacion!=""){
                $q->where('regmanveh.situacion','like',$situacion);
            }
        })->where(function ($q) use($docmat_id){
            if($docmat_id!="0"){
                $q->where('regmanveh.docmat_id','=',$docmat_id)
                    ->orWhere('regmanveh.docmat_id2','=',$docmat_id)
                    ->orWhere('regmanveh.docmat_id3','=',$docmat_id)
                    ->orWhere('regmanveh.docmat_id4','=',$docmat_id)
                    ->orWhere('regmanveh.ordencompra','like','%%');
            }
        })->where(function ($q) use($tipo){
            if($tipo!=""){
                $q->where('regmanveh.tipomantenimiento','like',$tipo);
            }
        })->where(function ($q) use($documentos){
            if($documentos!=""){
                if($documentos=="NO"){
                    $q->whereRaw('(select count(*) from regmanvehdocument where regmanveh_id=regmanveh.id and deleted_at is null) = 0');
                }else{
                    $q->whereRaw('(select count(*) from regmanvehdocument where regmanveh_id=regmanveh.id and deleted_at is null) > 0');
                }
            }
        });

        $lista            = $resultado->select('regmanveh.*',DB::raw('(select count(*) from regmanvehdocument where regmanveh_id=regmanveh.id and deleted_at is null) as documentos'))
                            ->orderBy('fechamantenimiento','desc')->get();
        $lista            = $resultado->get();
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
        $cabecera[]       = array('valor' => 'Usuario Reg.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '4');
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
        $entidad          = 'RegManVeh';
        $title            = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipo=array('' => 'Todos','1'=>'Preventivo','2'=>'Correctivo');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title','tituloRegistrar', 'ruta','cboTipo'));
    }


    public function createtrabajo(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'RegManVeh';
        $regmanveh = null;
        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $oObservaciones=array(new DescripcionRegManVeh());
        $oObservaciones[0]->id=-1 ; 
        $uades="";
        $placa="";
        $vehiculo_id="";
        $equipo_id ="";
        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;
        //$oTipos=array('' => 'Seleccione Tipo');
        $oTipos=array('' => 'Seleccione Tipo');
        $oTipos=array('1' => 'Preventivo');
        $oTipos+=array('2' => 'Correctivo');
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $cboDiagnostico = array('0'=>'--Seleccione--');// + Diagnostico::orderBy('numero','asc')->pluck('numero','id')->toArray();
        $formData = array('regmanveh.createregmanveh');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        $lista = RegManVeh::whereNull('numero')->orderBy('fecharegistro')->orderBy('id','asc')->get();
        foreach ($lista as $key => $value) {
            $data = RegManVeh::whereNotNull('numero')->where('fecharegistro','<=',$value->fecharegistro)->whereRaw('SUBSTRING(fecharegistro,1,4) like \''.date("Y",strtotime($value->fecharegistro)).'\'')->orderBy('id','desc')->orderBy('numero','desc')->first();
            if(is_null($data)){
                $numero = "1-".date("Y",strtotime($value->fecharegistro));
            }else{
                $numero = (explode("-", $data->numero)[0] + 1).date("-Y",strtotime($value->fecharegistro)); 
                $existe = RegManVeh::where('numero','like',$numero)->first();
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
        return view($this->folderview.'.mant2')->with(compact('regmanveh', 'oTipos','formData', 'entidad','oConcesionaria','idconc','uades','placa','vehiculo_id','oObservaciones', 'boton', 'listar','equipo_id','cboTaller','cboDiagnostico','cboDocmat','cboDocmat2','cboDocmat3','cboDocmat4'));
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
        $entidad  = 'RegManVeh';
        $mantenimiento = null;
        $formData = array('mantenimientos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        $arrUnidades = Unidad::getAll();
        $cboUnidades = array('' => 'Seleccione');
        foreach($arrUnidades as $k=>$v){
            $cboUnidades += array($v->id=>$v->descripcion);
        }
        return view($this->folderview.'.mant_checklistvehicular')->with(compact('mantenimiento', 'formData', 'entidad', 'boton', 'cboUnidades', 'listar'));
    }

    public function store(Request $request){
        //BUSCAR EQUIPO/VEHICULO KM
        $id = $request->input('equipo_id')+0;
        $equipo = Equipo::where('id','=', $id)->first();
        /*if($equipo != null) {
            $obj = RegManVeh::where('equipo_id','=',$equipo->id)->where('fechasalida','<=',$request->input('fechaentrada'))->orderBy('fechasalida','desc')->first();
            if(!is_null($obj) && ($request->input('kminicial')+0)< ($obj->kmfinal+0)){
                $dat = array("km" => ["Kilometraje registrado es menor del actual, el ultimo km es: ".$obj->kmfinal." del ".$obj->fechaentrada],"id"=>$obj->id);
                return json_encode($dat);
            }
        }else {
            $vehiculo = Vehiculo::where('id','=', $request->input('vehiculo_id'))->first();
            $obj = RegManVeh::where('ua_id','=',$vehiculo->id)->where('fechasalida','<=',$request->input('fechaentrada'))->orderBy('fechasalida','desc')->first();
            //dd($obj);
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
                'titulo' => 'required',
                'fechaentrada' => 'required',
                'fechasalida' => 'required',
                'fechamantenimiento' => 'required',
                'kmman' => 'required',
                'kminicial' => 'required',
                'kmfinal' => 'required',
                'tipomantenimiento' => 'required',
                'cantidad.*' => 'required',
                'monto.*' => 'required',
                'cuantasdescripciones'=>'required'
            ];

            $mensajes = [
                //'cliente.required' => 'Cliente Campo Vacio',
                'titulo.required' => 'Debe ingresar una descripcion para el mantenimiento',
                //'ordencompra.required' => 'DOCMAT Campo Vacio',
                'cliente.max' => 'Nombre de Cliente muy extenso, maximo 250 caracteres',
                //'ordencompra.max' => 'DOCMAT muy extensa, máximo 250 caracteres',
                'vehiculo_id.required' => 'Seleccione un vehículo valido',
                'fechaentrada.required' => 'Fecha Entrada Campo Vacio',
                'fechasalida.required' => 'Fecha Salida Campo Vacio',
                'fechamantenimiento.required' => 'Fecha Mant. Campo Vacío',
                'kmman.required' => 'Km de Mantenimiento Campo Vacio',
                'kminicial.required' => 'Km Inicial Campo Vacio',
                'kmfinal.required' => 'Km Final Campo Vacio',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
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

        $idn=DB::select("SHOW TABLE STATUS LIKE 'regmanveh'");
        $next_id=$idn[0]->Auto_increment;

        
        $error = DB::transaction(function() use($request,&$next_id){
            $data = RegManVeh::whereNotNull('numero')->where('fecharegistro','<=',date("Y-m-d"))->whereRaw('SUBSTRING(fecharegistro,1,4) like \''.date("Y").'\'')->orderBy('id','desc')->orderBy('numero','desc')->first();
            if(is_null($data)){
                $numero = "1-".date("Y");
            }else{
                $numero = (explode("-", $data->numero)[0] + 1).date("-Y"); 
                $existe = RegManVeh::where('numero','like',$numero)->first();
                if(!is_null($existe)){
                    $numero = (explode("-", $data->numero)[0] + 2).date("-Y"); 
                }
            }

            $regmanv = new RegManVeh();
            $regmanv -> numero =$numero;
            $regmanv -> cliente  = $request -> input('cliente');
            $regmanv -> titulo  = $request -> input('titulo');
            $regmanv -> docmat_id  = $request -> input('docmat_id');
            $regmanv -> ordencompra  = $request -> input('ordencompra');
            $regmanv -> concesionaria_id  = $request -> input('concesionaria_id');
            $regmanv -> taller_id = $request -> input('taller_id');
            $regmanv -> ua_id = $request -> input('vehiculo_id');
            $regmanv -> equipo_id = $request -> input('equipo_id');
            $regmanv -> kminicial = $request -> input('kminicial');
            $regmanv -> kmman = $request -> input('kmman');
            $regmanv -> kmfinal = $request -> input('kmfinal');
            $regmanv -> fechaentrada = $request -> input('fechaentrada');
            $regmanv -> fechasalida = $request -> input('fechasalida');
            $regmanv -> fechamantenimiento = $request -> input('fechamantenimiento');
            $regmanv -> fecharegistro = now();
            $regmanv -> tipomantenimiento = $request -> input('tipomantenimiento');
            $regmanv -> telefono = $request -> input('telefono');
            $regmanv -> otros = $request -> input('otros');
            $regmanv -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $regmanv -> regrepveh_id = $request -> input('regrepveh_id');
            $regmanv -> situacion = $request -> input('situacion');
            $regmanv -> diagnostico_id = $request -> input('diagnostico_id');
            $regmanv -> user_id = auth()->user()->id;
            $regmanv -> docmat_id2  = $request -> input('docmat_id2');
            $regmanv -> docmat_id3  = $request -> input('docmat_id3');
            $regmanv -> docmat_id4  = $request -> input('docmat_id4');
            $regmanv -> save();
            $next_id = $regmanv->id;
            event( new UserHasCreatedOrDeleted($regmanv->id,'regmanveh', auth()->user()->id,'crear'));

            if($request->input('vehiculo_id')!="0"){
                $vehiculo=Vehiculo::find($request -> input('vehiculo_id'));
                if(($request->input('kmfinal')+0)>$vehiculo->kilometraje_act){
                    $vehiculo->kilometraje_act = $request->input('kmfinal');
                    $vehiculo->save();
                }

                /*if($regmanv->tipomantenimiento=="1"){
                    $vehiculo->kilometraje_act = $regmanv->kmfinal;
                    $vehiculo->kilometraje_rec = 0;
                    $vehiculo->save();

                    $regmanv->especial = true;
                    $regmanv->save();
                }*/
            }

        });

        $cantidades=$request->cantidad;
        $mantenimientosid=$request->trabajoid;
        $montos=$request->monto;
        if($cantidades!=""){
            for ($i = 0; $i < count($cantidades); $i++) {
               $error = DB::transaction(function() use($i,$request,$next_id,$cantidades,$mantenimientosid,$montos){
                    $descripcionregmanv = new DescripcionRegManVeh();
                    $descripcionregmanv -> regmanveh_id =$next_id;
                    $descripcionregmanv -> cantidad  = $cantidades[$i];
                    $descripcionregmanv -> trabajo_id  = intval($mantenimientosid[$i]);
                    $descripcionregmanv -> monto = $montos[$i];;
                    $descripcionregmanv -> save();
                }); 
            }
        }

        $this->generatePDFSave($next_id);
        
        $cc = new Curl(false,'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegManVehicular.php');
        $url = 'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegManVehicular.php?id='.$next_id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);
        //error_log($datos);

        return is_null($error) ? "OK" : $error;
    }


    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'regmanveh');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = RegManVeh::find($id);
        $entidad  = 'RegManVeh';
        $formData = array('route' => array('regmanveh.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'regmanveh');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $regmanveh = RegManVeh::find($id);

            event( new UserHasCreatedOrDeleted($regmanveh->id,'regmanveh', auth()->user()->id,'eliminar'));
            $regmanveh->delete();
        });
        return is_null($error) ? "OK" : $error;
    }



    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'regmanveh');
        if ($existe !== true) {
            return $existe;
        }

        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();

        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;
        

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $regmanveh = RegManVeh::find($id);
        $oObservaciones=DescripcionRegManVeh::where('regmanveh_id','=',$id)
            ->join('trabajo', 'descripcionregmanveh.trabajo_id', '=', 'trabajo.id')
            ->select('descripcionregmanveh.id as id','descripcionregmanveh.monto as monto','descripcionregmanveh.cantidad as cantidad','trabajo.id as trabajo_id','trabajo.descripcion as descripcion')->get();
        if(!is_null($regmanveh->ua_id) && $regmanveh->ua_id!="" && $regmanveh->ua_id!="0"){
            $oua = Vehiculo::where('id','=',$regmanveh->ua_id)->get();
        }else{
            $oua = Equipo::where('id','=',$regmanveh->equipo_id)->get();
        }

        $placa= $oua[0]->placa;
        $oua2 = Ua::where('id','=',$oua[0]->ua_id)->get();
        $uades= $oua2[0]->codigo;
        $uades=$uades;
        $vehiculo_id=$regmanveh->ua_id;
        $equipo_id=$regmanveh->equipo_id;
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        if(!is_null($regmanveh->diagnostico)){
            $cboDiagnostico = Diagnostico::where('id','=',$regmanveh->diagnostico_id)->pluck('numero','id')->toArray();
        }else{
            $cboDiagnostico = array('0'=>'--Seleccione--');// + Diagnostico::orderBy('numero','asc')->pluck('numero','id')->toArray();
        }
        if(!is_null($regmanveh->docmat_id) && $regmanveh->docmat_id>0){
            $cboDocmat = array($regmanveh->docmat_id=>$regmanveh->ordencompra);
        }else{
            $cboDocmat = array('0'=>'--Seleccione--');
        }
        if(!is_null($regmanveh->docmat_id2) && $regmanveh->docmat_id2>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regmanveh->docmat_id2)[0];
            $cboDocmat2 = array($regmanveh->docmat_id2=>$data->numero);
        }else{
            $cboDocmat2 = array('0'=>'--Seleccione--');
        }
        if(!is_null($regmanveh->docmat_id3) && $regmanveh->docmat_id3>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regmanveh->docmat_id3)[0];
            $cboDocmat3 = array($regmanveh->docmat_id3=>$data->numero);
        }else{
            $cboDocmat3 = array('0'=>'--Seleccione--');
        }
        if(!is_null($regmanveh->docmat_id4) && $regmanveh->docmat_id4>0){
            $data = DB::connection('mysql3')->select('select docmat.*
            from docmat
            where id='.$regmanveh->docmat_id4)[0];
            $cboDocmat4 = array($regmanveh->docmat_id4=>$data->numero);
        }else{
            $cboDocmat4 = array('0'=>'--Seleccione--');
        }
        $entidad  = 'RegManVeh';
        $formData = array('regmanveh.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant2')->with(compact('regmanveh','oConcesionaria','idconc','oObservaciones', 'placa','uades','vehiculo_id', 'formData', 'entidad', 'boton', 'listar','equipo_id','cboTaller','cboDiagnostico','cboDocmat','cboDocmat2','cboDocmat3','cboDocmat4'));
    }

    public function update(Request $request, $id)
    {
        if($request->input('situacion')=="F" && auth()->user()->tipouser_id!="1"){//todos los usuario menos el admin
            $reglas = [
                'concesionaria_id' => 'required',
                'titulo' => 'required',
                //'ordencompra' => 'required|max:250',
                'vehiculo_id' => 'required',
                'titulo' => 'required',
                'fechaentrada' => 'required',
                'fechasalida' => 'required',
                'fechamantenimiento' => 'required',
                'kmman' => 'required',
                'kminicial' => 'required',
                'kmfinal' => 'required',
                'tipomantenimiento' => 'required',
                'cantidad.*' => 'required',
                'monto.*' => 'required',
                'cuantasdescripciones'=>'required'
            ];

            $mensajes = [
                'titulo.required' => 'Debe ingresar una descripcion para el mantenimiento',
                //'ordencompra.required' => 'DOCMAT Campo Vacio',
                //'ordencompra.max' => 'DOCMAT muy extensa, máximo 250 caracteres',
                'vehiculo_id.required' => 'Seleccione un vehículo válido',
                'fechaentrada.required' => 'Fecha Entrada Campo Vacio',
                'fechasalida.required' => 'Fecha Salida Campo Vacio',
                'fechamantenimiento.required' => 'Fecha Mant. Campo Vacio',
                'kmman.required' => 'Km de Mantenimiento Campo Vacio',
                'kminicial.required' => 'Km Inicial Campo Vacio',
                'kmfinal.required' => 'Km Final Campo Vacio',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
                'cantidad.*.required' => 'Ingrese Cantidad todas las Observaciones',
                'monto.*.required' => 'Ingrese Monto todas las Observaciones',
                'cuantasdescripciones.required' => 'Ingrese al menos una Observación'
            ];
            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $documentos = Regmanvehdocument::where('regmanveh_id','=',$id)->first();
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

        $existe = Libreria::verificarExistencia($id, 'regmanveh');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $regmanv = RegManVeh::find($id);

            $regmanvOrg = json_encode($regmanv);

            $regmanv -> cliente  = $request -> input('cliente');
            $regmanv -> titulo  = $request -> input('titulo');
            $regmanv -> ordencompra  = $request -> input('ordencompra');
            $regmanv -> docmat_id  = $request -> input('docmat_id');
            $regmanv -> concesionaria_id  = $request -> input('concesionaria_id');
            $regmanv -> taller_id = $request -> input('taller_id');
            $regmanv -> ua_id = $request -> input('vehiculo_id');
            $regmanv -> equipo_id = $request -> input('equipo_id');
            $regmanv -> kmman = $request -> input('kmman');
            $regmanv -> kminicial = $request -> input('kminicial');
            $regmanv -> kmfinal = $request -> input('kmfinal');
            $regmanv -> fechaentrada = $request -> input('fechaentrada');
            $regmanv -> fechasalida = $request -> input('fechasalida');
            $regmanv -> tipomantenimiento = $request -> input('tipomantenimiento');
            $regmanv -> telefono = $request -> input('telefono');
            $regmanv -> otros = $request -> input('otros');
            $regmanv -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $regmanv -> regrepveh_id = $request -> input('regrepveh_id');
            $regmanv -> situacion = $request -> input('situacion');
            $regmanv -> fechamantenimiento = $request -> input('fechamantenimiento');
            $regmanv -> diagnostico_id = $request -> input('diagnostico_id');
            $regmanv -> docmat_id2  = $request -> input('docmat_id2');
            $regmanv -> docmat_id3  = $request -> input('docmat_id3');
            $regmanv -> docmat_id4  = $request -> input('docmat_id4');
            $regmanv -> save();

            event( new UserHasEdited($regmanvOrg,$regmanv,'regmanveh', auth()->user()->id));
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
        $mantenimientosid=$request->trabajoid;
        if($cantidades!=""){
            for ($i = 0; $i < count($cantidades); $i++) {
               $error = DB::transaction(function() use($id,$i,$ids,$request,$cantidades,$mantenimientosid,$montos){
                    $descripcionregmanv = new DescripcionRegManVeh();
                    if($ids[$i]>=0){$descripcionregmanv = DescripcionRegManVeh::find($ids[$i]);}
                    $descripcionregmanv -> regmanveh_id =$id;
                    $descripcionregmanv -> cantidad  = $cantidades[$i];
                    $descripcionregmanv -> trabajo_id  = $mantenimientosid[$i];
                    $descripcionregmanv -> monto = $montos[$i];;
                    $descripcionregmanv -> save();
                }); 
            }
        }

        $idseliminados=$request->idseliminados;

        if($idseliminados!=null){
            foreach($idseliminados as $k=>$idd){
                $error = DB::transaction(function() use($idd){
                    if($idd>0){
                $regmanveh = DescripcionRegManVeh::find($idd);
                $regmanveh->delete();}
                }); 
            }
        }  

        $this->generatePDFSave($id);
        
        $cc = new Curl(false,'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegManVehicular.php');
        $url = 'https://sistemasolmos.pe/cev/PHPMailer/notificacionRegManVehicular.php?id='.$id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);
        

        return is_null($error) ? "OK" : $error;
    }


    public function searchAutocompleteTrabajo($query){

        $consulta = "select  id, descripcion, descripcion as 'search'
            from trabajo 
            where trabajo.deleted_at IS NULL AND
            (descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

	public function generatePDF(Request $request) {
	    $id=$request->id;
        if ( $request->id == null || !is_numeric($request->id) ) return;

        $namefile = 'RegistroDeMantenimientoVehicular - '.$id.'.pdf'; 
        
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
        ->where('users.id','=',auth()->user()->id)
        ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $ConcAct=$ConcesionariaActual[0]->razonsocial;
        $regmanveh = RegManVeh::find($id);
        $oObservaciones=DescripcionRegManVeh::where('regmanveh_id','=',$id)
            ->join('trabajo', 'descripcionregmanveh.trabajo_id', '=', 'trabajo.id')
            ->select('descripcionregmanveh.id as id','descripcionregmanveh.monto as monto','descripcionregmanveh.cantidad as cantidad','trabajo.id as trabajo_id','trabajo.descripcion as descripcion')->get();
        
        $data=[];
        $data['concesionaria'] = $ConcAct;
        if($regmanveh->taller_id>0){
            $data['cliente'] = $regmanveh->taller->razonsocial;
        }else{
            $data['cliente'] = "-";
        }
        $data['ordencompra'] = $regmanveh->ordencompra;
        $data['ua_id'] = $regmanveh->ua_id;
        $data['tipomantenimiento']=$regmanveh->tipomantenimiento==1?'PREVENTIVO':'CORRECTIVO';
        $data['kmman'] = $regmanveh->kmman;
        $data['kminicial'] = $regmanveh->kminicial;
        $data['kmfinal'] = $regmanveh->kmfinal;
        $data['telefono'] = $regmanveh->telefono;
        //$data['fechaentrada'] = $regmanveh->fechaentrada;
        $data['fechaentrada'] = $regmanveh->fechamantenimiento;
        $data['fechasalida'] = $regmanveh->fechasalida;
        $data['fecharegistro'] = date("d/m/Y",strtotime($regmanveh->fecharegistro));
        $data['regmanveh'] = $regmanveh;
        $data['observaciones'] = $oObservaciones;
        $data['namefile'] = $namefile;
        
        $data['concesionaria_id'] = $ConcesionariaActual[0]->id;

        
        $posiblevehiculo=Vehiculo::join('ua','vehiculo.ua_id','=','ua.id')
        ->join('marca','vehiculo.marca_id','=','marca.id')->where('vehiculo.id','=',$regmanveh->ua_id)
        ->select('vehiculo.placa as placa','vehiculo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();

 
        $posibleequipo=Equipo::join('ua','equipo.ua_id','=','ua.id')
        ->join('marca','equipo.marca_id','=','marca.id')->where('equipo.id','=',$regmanveh->equipo_id)
        ->select('equipo.descripcion as unidad','equipo.placa as placa','equipo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();
        

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
        }


        // dd($data);
        $html = view('app.regmanveh.pdf.template_individual', $data)->render();

        $mpdf = new Mpdf(['tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);
        $path = public_path('pdf');
        Storage::disk('public')->put('pdf/'.$namefile, $mpdf->Output($namefile, "S"));
        $mpdf->Output($namefile, "I");
    }

    public function generatePDFSave($id) {
        $namefile = 'RegistroDeMantenimientoVehicular - '.$id.'.pdf'; 
        
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
        ->where('users.id','=',auth()->user()->id)
        ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $ConcAct=$ConcesionariaActual[0]->razonsocial;
        $regmanveh = RegManVeh::find($id);
        $oObservaciones=DescripcionRegManVeh::where('regmanveh_id','=',$id)
            ->join('trabajo', 'descripcionregmanveh.trabajo_id', '=', 'trabajo.id')
            ->select('descripcionregmanveh.id as id','descripcionregmanveh.monto as monto','descripcionregmanveh.cantidad as cantidad','trabajo.id as trabajo_id','trabajo.descripcion as descripcion')->get();
        
        $data=[];
        $data['concesionaria'] = $ConcAct;
        if($regmanveh->taller_id>0){
            $data['cliente'] = $regmanveh->taller->razonsocial;
        }else{
            $data['cliente'] = "-";
        }
        $data['ordencompra'] = $regmanveh->ordencompra;
        $data['ua_id'] = $regmanveh->ua_id;
        $data['tipomantenimiento']=$regmanveh->tipomantenimiento==1?'PREVENTIVO':'CORRECTIVO';
        $data['kmman'] = $regmanveh->kmman;
        $data['kminicial'] = $regmanveh->kminicial;
        $data['kmfinal'] = $regmanveh->kmfinal;
        $data['telefono'] = $regmanveh->telefono;
        $data['fechaentrada'] = $regmanveh->fechaentrada;
        $data['fechasalida'] = $regmanveh->fechasalida;
        $data['fecharegistro'] = $regmanveh->fecharegistro;
        $data['regmanveh'] = $regmanveh;
        $data['observaciones'] = $oObservaciones;
        $data['namefile'] = $namefile;
        
        $data['concesionaria_id'] = $ConcesionariaActual[0]->id;

        
        $posiblevehiculo=Vehiculo::join('ua','vehiculo.ua_id','=','ua.id')
        ->join('marca','vehiculo.marca_id','=','marca.id')->where('vehiculo.id','=',$regmanveh->ua_id)
        ->select('vehiculo.placa as placa','vehiculo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();

 
        $posibleequipo=Equipo::join('ua','equipo.ua_id','=','ua.id')
        ->join('marca','equipo.marca_id','=','marca.id')->where('equipo.id','=',$regmanveh->equipo_id)
        ->select('equipo.descripcion as unidad','equipo.placa as placa','equipo.modelo as modelo','marca.descripcion as marca','ua.codigo as uacodigo')->get();
        

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
        }


        // dd($data);
        $html = view('app.regmanveh.pdf.template_individual', $data)->render();

        $mpdf = new Mpdf(['tempDir' => storage_path('app/mpdf')]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        Storage::disk('public')->put('pdf/'.$namefile, $mpdf->Output($namefile, "S"));
    }

    public function comboChecklist(Request $request){
        $combo = "<select id='checklistvehicular_id' name='checklistvehicular_id' class='form-control'><option value='0'>--Seleccione--</option>";
        if($request->input('vehiculo_id')!="0"){
            $data = Checklistvehicular::where('vehiculo_id','=',$request->input('vehiculo_id'))->orderBy('fecha_registro','desc')->limit(20)->get();
        }else{
            $data = Checklistvehicular::where('equipo_id','=',$request->input('equipo_id'))->orderBy('fecha_registro','desc')->limit(20)->get();
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

    public function comboRegRepVeh(Request $request){
        $combo = "<select id='regrepveh_id' name='regrepveh_id' class='form-control'><option value='0'>Ninguno</option>";
        if($request->input('vehiculo_id')!="0"){
            $data = RegRepVeh::where('ua_id','=',$request->input('vehiculo_id'))->orderBy('fechaentrada','desc')->limit(100)->get();
        }else{
            $data = RegRepVeh::where('equipo_id','=',$request->input('equipo_id'))->orderBy('fechaentrada','desc')->limit(100)->get();
        }
        foreach ($data as $key => $value) {
            $combo.="<option value='".$value->id."'>".date("d/m/Y",strtotime($value->fechaentrada))." | ".$value->cliente." | ".$value->ordencompra." | ".($value->tipomantenimiento==1?'Preventivo':'Correctivo')."</option>";
        }
        $combo.="</select>";
        return $combo;
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportExcelList(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $tipofecha = $request->tipofecha;
        $filter = $request->filter;
        
        return Excel::download(new RegManVehExportExcelList($fecha1,$fecha2,$concesionaria,$tipofecha,$filter), 'ReporteMantVeh.xlsx');
    }

    public function exportExcelTodos(Request $request){
        $concesionaria = $this->getConsecionariaActual();

        $fecha1 = $request->fechainicio;
        $fecha2 = $request->fechafin;
        $filter = $request->filter;
        
        return Excel::download(new RegManVehExportExcelTodos($fecha1,$fecha2,$concesionaria,$filter), 'ReporteRepMantVeh.xlsx');
    }

    public function diagnostico(Request $request){
        $lista = Diagnostico::whereRaw('id not in (select diagnostico_id from regmanveh where deleted_at is null and diagnostico_id is not null)')->whereRaw('id not in (select diagnostico_id from regrepveh where deleted_at is null and diagnostico_id is not null)')->orderBy('numero','asc')->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Numero', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Diag.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Taller', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descrip/Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Checklist.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Recorrido KM/Horometro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Revision', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Diagnostico', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Recomendacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cierre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '1');
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = "Diagnostico";
        $inicio = 0;
        $diagnostico             = null;
        $formData            = array('diagnostico.update');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton               = 'Listar';
        return view($this->folderview . '.list2')->with(compact('diagnostico', 'formData', 'entidad', 'boton', 'listar','lista','cabecera','inicio'));
    }

    public function docmat(Request $request){
        $searching="";
        $lista = DB::connection('mysql3')->select('select docmat.*,dmv.descripcion as motivoveh
            from docmat
            inner join concesionaria co on co.id=docmat.concesionaria_id
            left join descripcion_mantveh dmv on dmv.id=docmat.descripcion_mantveh_id
            where docmat.deleted_at is null and (docmat.placa like "%'.$searching.'%")');
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Numero', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Necesidad.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Km', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '1');
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = "Diagnostico";
        $inicio = 0;
        $diagnostico             = null;
        $tipo = $request->input('tipo');
        $formData            = array('diagnostico.update');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton               = 'Listar';
        return view($this->folderview . '.listDocmat')->with(compact('diagnostico', 'formData', 'entidad', 'boton', 'listar','lista','cabecera','inicio','tipo'));
    }
}
