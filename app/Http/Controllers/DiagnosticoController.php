<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Equipo;
use App\Diagnostico;
use App\Vehiculo;
use App\Checklistvehicular;
use App\Ua;
use App\Taller;
use App\Rules\SearchUaPadre;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Concesionaria;
use App\Configuracion;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiagnosticoExport;
use App\Curl;

class DiagnosticoController extends Controller
{
    protected $folderview      = 'app.diagnostico';
    protected $tituloAdmin     = 'Registro de Diagnostico Vehicular';
    protected $tituloRegistrar = 'Registro de Diagnostico Vehicular';
    protected $tituloModificar = 'Modificar Diagnostico Vehicular';
    protected $tituloEliminar  = 'Eliminar Diagnostico Vehicular';
    protected $rutas           = array(
        'create' => 'diagnostico.create',
        'buscarporua' => 'diagnostico.buscarporua',
        'edit'   => 'diagnostico.edit', 
        'delete' => 'diagnostico.eliminar',
        'activar' => 'diagnostico.activar',
        'search' => 'diagnostico.buscar',
        'store' => 'diagnostico.store',
        'index'  => 'diagnostico.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
        //define("MIN_DIAS", trim(Configuracion::where("id", "=", 5)->first()->valor));
        define("MIN_DIAS", "100");
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Diagnostico';
        $filter           = Libreria::getParam($request->input('filter'));
		$ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

		$fechainicio= $request->input('fechainicio');
        $fechafin= $request->input('fechafin');
        $tipofecha= $request->input('tipofecha');
        $docmat= $request->input('docmat');

        $resultado= Diagnostico::leftjoin('vehiculo','vehiculo.id','=','diagnostico.vehiculo_id')
                            ->leftjoin('equipo','equipo.id','=','diagnostico.equipo_id')
        ->where('diagnostico.concesionaria_id',$idConcAct)
        ->where(function($q) use ($fechainicio, $fechafin,$tipofecha) {
                if ( $fechainicio !== null ) $q->where('diagnostico.'.$tipofecha,'>=', $fechainicio);
                if ( $fechafin !== null ) $q->where('diagnostico.'.$tipofecha,'<=', $fechafin);
            })
        ->where(function($q) use ($filter){
            $q->Where('vehiculo.placa', 'like', '%'.$filter.'%')
              ->orWhere('vehiculo.modelo', 'like', '%'.$filter.'%')
              ->orWhere('equipo.modelo', 'like', '%'.$filter.'%')
              ->orWhere('equipo.placa', 'like', '%'.$filter.'%')
              ->orWhere('equipo.descripcion', 'like', '%'.$filter.'%');
     	})
        ->where(function($q) use($docmat){
            if($docmat!=""){
                $q->Where('diagnostico.docmat','like','%'.$docmat.'%');
            }
        });

        $lista            = $resultado->select('diagnostico.*')->get();
        $lista            = $resultado->get();
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
        $cabecera[]       = array('valor' => 'DOCMAT', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_registrar = $this->tituloRegistrar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_registrar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Diagnostico';
        $title            = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        
        return view($this->folderview.'.admin')->with(compact('entidad', 'title','tituloRegistrar', 'ruta'));
    }


    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Diagnostico';
        $diagnostico = null;
        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        
        $uades="";
        $placa="";
        $vehiculo_id="";
        $equipo_id ="";
        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;
        $oTipos=array('Preventivo' => 'Preventivo','Correctivo' => 'Correctivo');
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        $formData = array('diagnostico.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $numero = Diagnostico::NumeroSigue();
        return view($this->folderview.'.mant2')->with(compact('diagnostico', 'oTipos','formData', 'entidad','oConcesionaria','idconc','uades','placa','vehiculo_id', 'boton', 'listar','equipo_id','cboTaller','numero'));
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

    public function store(Request $request){
        if($request->input('situacion')=="F"){
            $reglas = [
                'concesionaria_id' => 'required',
                //'cliente' => 'required|max:250',
                'fecha' => 'required',
                'recorrido' => 'required',
                'revision' => 'required',
                'diagnostico' => 'required',
                'recomendacion' => 'required',
                'cierre' => 'required',
                'tipomantenimiento' => 'required',
                'docmat' => 'required',
            ];

            $mensajes = [
                'fecha.required' => 'Fecha Campo Vacio',
                'recorrido.required' => 'Recorrido Campo Vacio',
                'revision.required' => 'Revision Campo Vacio',
                'diagnostico.required' => 'Diagnostico Campo Vacio',
                'recomendacion.required' => 'Recomendacion Campo Vacio',
                'cierre.required' => 'Cierre Campo Vacio',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
                'docmat.required' => 'Es obligatorio ingresar el nro de DOCMAT',
            ];


            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
        }
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        
        $error = DB::transaction(function() use($request,&$next_id){
            $obj = new Diagnostico();
            $obj -> fecha  = $request -> input('fecha');
            $obj -> tipomantenimiento = $request -> input('tipomantenimiento');
            $obj -> concesionaria_id  = $request -> input('concesionaria_id');
            $obj -> taller_id = $request -> input('taller_id');
            $obj -> vehiculo_id = $request -> input('vehiculo_id');
            $obj -> equipo_id = $request -> input('equipo_id');
            $obj -> recorrido = $request -> input('recorrido');
            $obj -> revision = $request -> input('revision');
            $obj -> diagnostico = $request -> input('diagnostico');
            $obj -> cierre = $request -> input('cierre');
            $obj -> recomendacion = $request -> input('recomendacion');
            $obj -> fecharegistro = now();
            $obj -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $obj -> situacion = $request -> input('situacion');
            $obj -> numero = Diagnostico::NumeroSigue();
            $obj -> docmat = $request -> input('docmat');
            $obj -> save();
            $next_id = $obj->id;
            event( new UserHasCreatedOrDeleted($obj->id,'diagnostico', auth()->user()->id,'crear'));

        });

        $cc = new Curl(false,'https://sistemasolmos.pe/cev/PHPMailer/notificacionDiagnostico.php');
        $url = 'https://sistemasolmos.pe/cev/PHPMailer/notificacionDiagnostico.php?id='.$next_id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);

        return is_null($error) ? "OK" : $error;
    }


    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'diagnostico');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Diagnostico::find($id);
        $entidad  = 'Diagnostico';
        $formData = array('route' => array('diagnostico.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'diagnostico');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Diagnostico::find($id);

            event( new UserHasCreatedOrDeleted($obj->id,'diagnostico', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }



    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'diagnostico');
        if ($existe !== true) {
            return $existe;
        }

        $arrConcesionarias = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();

        $idconc=$arrConcesionarias[0]->id;
        $oConcesionaria=$arrConcesionarias[0]->razonsocial;

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $diagnostico = Diagnostico::find($id);
        if(!is_null($diagnostico->vehiculo_id) && $diagnostico->vehiculo_id!="" && $diagnostico->vehiculo_id!="0"){
            $oua = Vehiculo::where('id','=',$diagnostico->vehiculo_id)->get();
        }else{
            $oua = Equipo::where('id','=',$diagnostico->equipo_id)->get();
        }
        $numero = $diagnostico->numero;
        $placa= $oua[0]->placa;
        $oua2 = Ua::where('id','=',$oua[0]->ua_id)->get();
        $uades= $oua2[0]->codigo;
        $uades=$uades;
        $vehiculo_id=$diagnostico->vehiculo_id;
        $equipo_id=$diagnostico->equipo_id;
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');

        $entidad  = 'Diagnostico';
        $formData = array('diagnostico.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant2')->with(compact('diagnostico','oConcesionaria','idconc', 'placa','uades','vehiculo_id', 'formData', 'entidad', 'boton', 'listar','equipo_id','cboTaller','numero'));
    }

    public function update(Request $request, $id)
    {
        if($request->input('situacion')=="F"){
            $reglas = [
                'concesionaria_id' => 'required',
                'fecha' => 'required',
                'recorrido' => 'required',
                'revision' => 'required',
                'diagnostico' => 'required',
                'recomendacion' => 'required',
                'cierre' => 'required',
                'tipomantenimiento' => 'required',
                'docmat' => 'required',
            ];

            $mensajes = [
                'fecha.required' => 'Fecha Campo Vacio',
                'recorrido.required' => 'Recorrido Campo Vacio',
                'revision.required' => 'Revision Campo Vacio',
                'diagnostico.required' => 'Diagnostico Campo Vacio',
                'recomendacion.required' => 'Recomendacion Campo Vacio',
                'cierre.required' => 'Cierre Campo Vacio',
                'tipomantenimiento.required' => 'Tipo de Mantenimiento No Seleccionado',
                'docmat.required' => 'Es obligatorio ingresar el nro de DOCMAT',
            ];
            $validacion = Validator::make($request->all(), $reglas, $mensajes);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

        }

        $existe = Libreria::verificarExistencia($id, 'diagnostico');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $obj = Diagnostico::find($id);

            $objOrg = json_encode($obj);

            $obj -> fecha  = $request -> input('fecha');
            $obj -> tipomantenimiento = $request -> input('tipomantenimiento');
            $obj -> concesionaria_id  = $request -> input('concesionaria_id');
            $obj -> taller_id = $request -> input('taller_id');
            $obj -> vehiculo_id = $request -> input('vehiculo_id');
            $obj -> equipo_id = $request -> input('equipo_id');
            $obj -> revision = $request -> input('revision');
            $obj -> diagnostico = $request -> input('diagnostico');
            $obj -> cierre = $request -> input('cierre');
            $obj -> recomendacion = $request -> input('recomendacion');
            $obj -> checklistvehicular_id = $request -> input('checklistvehicular_id');
            $obj -> situacion = $request -> input('situacion');
            $obj -> docmat = $request -> input('docmat');
            $obj -> save();

            event( new UserHasEdited($objOrg,$obj,'diagnostico', auth()->user()->id));
        });

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

        $namefile = 'RegistroDeMantenimientoVehicular - '.time().'.pdf'; 
        
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

        $mpdf = new Mpdf();
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        $mpdf->Output($namefile, "I");
    }

    public function recorrido(Request $request){
        if($request->input('vehiculo_id')!="0"){
            $data = Vehiculo::find($request->input('vehiculo_id'));
            $recorrido = $data->kilometraje2->descripcion." | KM. Ini: ".$data->kilometraje_inicial." | KM. Act: ".$data->kilometraje_act." | Dif: ".($data->kilometraje_act - $data->kilometraje_inicial);
        }else{
            $data = Equipo::find($request->input('equipo_id'));
            if(!is_null($data)){
                $recorrido = $data->horometraje2->descripcion." | Horo. Ini: ".$data->horometroinicial." | Horo. Act: ".$data->horometroactual." | Dif: ".($data->horometroactual - $data->horometroinicial);
            }else{
                $recorrido ="SIN REGLA";
            }
        }
        return $recorrido;
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function excel(Request $request){
        return Excel::download(new DiagnosticoExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('tipofecha')), 'diagnostico.xlsx');
    }

}
