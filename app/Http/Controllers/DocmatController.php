<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Docmat;
use App\Detalledocmat;
use App\Concesionaria;
use App\Equipo;
use App\Vehiculo;
use App\Taller;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use App\Curl;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
//use App\Exports\DocmatExport;

class DocmatController extends Controller
{
    protected $folderview      = 'app.docmat';
    protected $tituloAdmin     = 'DOC-MAT';
    protected $tituloRegistrar = 'Registrar DOC-MAT';
    protected $tituloModificar = 'Modificar DOC-MAT';
    protected $tituloEliminar  = 'Eliminar DOC-MAT';
    protected $rutas           = array('create' => 'docmat.create', 
            'edit'   => 'docmat.edit', 
            'delete' => 'docmat.eliminar',
            'search' => 'docmat.buscar',
            'index'  => 'docmat.index',
        );

         /**
     * Create a new Docmatler instance.
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
        $entidad          = 'Docmat';
        $fechafin         = $request->input('fechafin');
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $numero           = $request->input('numero');
        $ua               = $request->input('ua');
        $placa            = $request->input('placa');
        $resultado        = Docmat::leftjoin('vehiculo','vehiculo.id','=','docmat.vehiculo_id')
                            ->leftjoin('equipo','equipo.id','=','docmat.equipo_id')
                            ->leftjoin('ua as uaequipo','uaequipo.id','=','equipo.ua_id')
                            ->leftjoin('ua as uavehiculo','uavehiculo.id','=','vehiculo.ua_id')
                            ->where('docmat.concesionaria_id','=',$this->getConsecionariaActual())->orderBy('fecha','desc');
        if($fechainicio!=""){
            $resultado = $resultado->where('fecha','>=',DB::raw("'".$fechainicio." 00:00:00'"));
        }
        if($fechafin!=""){
            $resultado = $resultado->where('fecha','<=',DB::raw("'".$fechafin." 23:59:59'"));
        }
        if($numero!=""){
            $resultado = $resultado->where('numero','like','%'.$numero.'%');
        }
        if($placa!=""){
            $resultado = $resultado->where(function($sql) use($placa){
                    $sql->where('vehiculo.placa','like','%'.$placa.'%')
                        ->orWhere('equipo.placa','like','%'.$placa.'%');
                });
        }
        if($ua!=""){
            $resultado = $resultado->where(function($sql) use($ua){
                    $sql->where('uavehiculo.codigo','like','%'.$ua.'%')
                        ->orWhere('uaequipo.codigo','like','%'.$ua.'%');
                });
        }
        $lista            = $resultado->select('docmat.*')->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Número', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Emision', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Necesidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cod. Oracle', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'KM', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Taller', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '3');
        
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

    public function index()
    {
        $entidad          = 'Docmat';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Docmat';
        $docmat = null;
        $formData = array('docmat.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboPresupuestado =array('SI'=>'SI','NO'=>'NO');
        $cboTipo = array('S'=>'Solicitud de Compra/Servicio','B'=>'Baja de Stock','E'=>'Entrada Interna','A'=>'Ajuste -> Sobra','F'=>'Ajuste -> Falta');
        $cboCondicion = array('NORMAL'=>'NORMAL','URGENCIA'=>'URGENCIA','EMERGENCIA'=>'EMERGENCIA');
        $cboTipoCompra = array('A'=>'Aplicación Directa','S'=>'Stock');
        $cboCondicionMercado = array('Nacional'=>'Mercado Nacional','Internacional'=>'Mercado Internacional');
        $cboDiasNacional = array('8'=>'Normal (8)','5'=>'Urgencia (5)','2'=>'Emergencia (2)');
        $cboDiasInternacional = array('60'=>'Normal (60)','45'=>'Urgencia (45)','20'=>'Emergencia (20)');
        $detalle = array();
        $placa="";
        $data = Docmat::orderBy('numero','desc')->first();
        if(is_null($data)){
            $numero = "001-000001";
        }else{
            $data2 = explode("-",$data->numero);
            $numero = "001-".str_pad($data2[1]+1, 6,"0",STR_PAD_LEFT);
        }
        $uades="";
        $placa="";
        $vehiculo_id="";
        $equipo_id="";
        $cboTipoMantenimiento = array('Preventivo'=>'Preventivo','Correctivo'=>'Correctivo');
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        return view($this->folderview.'.mant')->with(compact('docmat', 'formData', 'entidad', 'boton', 'listar','cboPresupuestado','cboTipo','cboCondicion','cboTipoCompra','cboCondicionMercado','cboDiasNacional','cboDiasInternacional','detalle','placa','numero','uades','placa','vehiculo_id','equipo_id','cboTipoMantenimiento','cboTaller'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('fecha' => 'required',
                        //'fechanecesidad' => 'required',
                        //'codigo' => 'required',
                        'km' => 'required',
                        'cuantasdescripciones'=>'required',
                        'uades' => 'required'
                        );
        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            //'fechanecesidad.required' => 'Debe ingresar una fecha de necesidad',
            //'codigo.required' => 'Debe ingresar un codigo oracle',
            'km.required' => 'Ingrese el km',
            'cuantasdescripciones.required' => 'Ingrese al menos un item',
            'uades.required' => 'Debe seleccionar una UA'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $id=0;
        $error = DB::transaction(function() use($request,&$id){
            $obj = new Docmat();
            $obj->fecha= $request->input('fecha');
            $obj->fechanecesidad= $request->input('fechanecesidad');
            $obj->codigo= $request->input('codigo');
            $obj->numero= $request->input('numero');
            $obj->vehiculo_id= $request->input('vehiculo_id');
            $obj->equipo_id= $request->input('equipo_id');
            $obj->presupuestado= $request->input('presupuestado');
            $obj->tipooperacion= $request->input('tipooperacion');
            $obj->condicionentrega= $request->input('condicionentrega');
            $obj->tipocompra= $request->input('tipocompra');
            $obj->trabajo= mb_strtoupper($request->input('trabajo'), 'utf-8');
            $obj->condicionmercado= $request->input('condicionmercado');
            $obj->km= $request->input('km');
            $obj->tipomantenimiento= $request->input('tipomantenimiento');
            $obj->taller_id= $request->input('taller_id');
            if($obj->condicionmercado=="Nacional"){
                $obj->dias = $request->input('dias1');
            }else{
                $obj->dias = $request->input('dias2');
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->save();

            $cantidad = $request->input('txtCantidad');
            $unidad = $request->input('txtUnidad');
            $codigo = $request->input('txtCodigo');
            $descripcion = $request->input('txtDescripcion');
            $proveedor1 = $request->input('txtProveedor1');
            $proveedor2 = $request->input('txtProveedor2');
            $proveedor3 = $request->input('txtProveedor3');

            if($descripcion!=""){
                for ($i = 0; $i < count($descripcion); $i++) {
                    $detalle = new Detalledocmat();
                    $detalle -> docmat_id =$obj->id;
                    $detalle -> cantidad  = $cantidad[$i];
                    $detalle -> unidad  = $unidad[$i];
                    $detalle -> codigo  = $codigo[$i];
                    $detalle -> descripcion  = $descripcion[$i];
                    $detalle -> proveedor1  = $proveedor1[$i];
                    $detalle -> proveedor2  = $proveedor2[$i];
                    $detalle -> proveedor3  = $proveedor3[$i];
                    $detalle -> save();
                }
            }
            $id = $obj->id;
            event( new UserHasCreatedOrDeleted($obj->id,'docmat', auth()->user()->id,'crear'));
            
        });
        /*$cc = new Curl(false,'https://h2olmos.pe/cev/PHPMailer/notificacionDocmat.php');
        $url = 'https://h2olmos.pe/cev/PHPMailer/notificacionDocmat.php?id='.$id;
        $Page = $cc->get($url,array());
        $datos = json_decode($Page);*/

        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'docmat');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $docmat = Docmat::find($id);
        $entidad  = 'Docmat';
        $formData = array('docmat.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboPresupuestado =array('SI'=>'SI','NO'=>'NO');
        $cboTipo = array('S'=>'Solicitud de Compra/Servicio','B'=>'Baja de Stock','E'=>'Entrada Interna','A'=>'Ajuste -> Sobra','F'=>'Ajuste -> Falta');
        $cboCondicion = array('NORMAL'=>'NORMAL','URGENCIA'=>'URGENCIA','EMERGENCIA'=>'EMERGENCIA');
        $cboTipoCompra = array('A'=>'Aplicación Directa','S'=>'Stock');
        $cboCondicionMercado = array('Nacional'=>'Mercado Nacional','Internacional'=>'Mercado Internacional');
        $cboDiasNacional = array('8'=>'Normal (8)','5'=>'Urgencia (5)','2'=>'Emergencia (2)');
        $cboDiasInternacional = array('60'=>'Normal (60)','45'=>'Urgencia (45)','20'=>'Emergencia (20)');
        $detalle = Detalledocmat::where('docmat_id','=',$id)->get();
        $numero = $docmat->numero;
        if(!is_null($docmat->vehiculo)){
            $uades=$docmat->vehiculo->ua->codigo;
            $placa=$docmat->vehiculo->placa;
        }else{
            $uades=$docmat->equipo->ua->codigo;
            $placa=$docmat->equipo->placa;
        }
        $vehiculo_id=$docmat->vehiculo_id;
        $equipo_id=$docmat->equipo_id;
        $cboTipoMantenimiento = array('Preventivo'=>'Preventivo','Correctivo'=>'Correctivo');
        $cboTaller = Taller::orderBy('razonsocial','asc')->pluck('razonsocial','id');
        return view($this->folderview.'.mant')->with(compact('docmat', 'formData', 'entidad', 'boton', 'listar','cboPresupuestado','cboTipo','cboCondicion','cboTipoCompra','cboCondicionMercado','cboDiasNacional','cboDiasInternacional','detalle','placa','numero','uades','placa','vehiculo_id','equipo_id','cboTipoMantenimiento','cboTaller'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'docmat');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('fecha' => 'required',
                        //'fechanecesidad' => 'required',
                        //'codigo' => 'required',
                        'km' => 'required',
                        'cuantasdescripciones'=>'required',
                        'uades' => 'required'
                        );
        $mensajes = array(
            'fecha.required' => 'Debe ingresar una fecha',
            //'fechanecesidad.required' => 'Debe ingresar una fecha de necesidad',
            //'codigo.required' => 'Debe ingresar un codigo oracle',
            'km.required' => 'Ingrese el km',
            'cuantasdescripciones.required' => 'Ingrese al menos un item',
            'uades.required' => 'Debe seleccionar una UA'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        /*$garita = Lugar::where('id',$request->input('destino_id'))->where('garita_id','like','%"'.$request->input('garita_id').'"%')->first();
        if(!is_null($garita)){
            return json_encode(['origen' => ['Destino no puede ser '.$garita->nombre]]);
        }*/

        $error = DB::transaction(function() use($request, $id){
            $obj = Docmat::find($id);
            $objCop = json_encode($obj);

            $obj->fecha= $request->input('fecha');
            $obj->fechanecesidad= $request->input('fechanecesidad');
            $obj->codigo= $request->input('codigo');
            $obj->numero= $request->input('numero');
            $obj->vehiculo_id= $request->input('vehiculo_id');
            $obj->equipo_id= $request->input('equipo_id');
            $obj->presupuestado= $request->input('presupuestado');
            $obj->tipooperacion= $request->input('tipooperacion');
            $obj->condicionentrega= $request->input('condicionentrega');
            $obj->tipocompra= $request->input('tipocompra');
            $obj->trabajo= mb_strtoupper($request->input('trabajo'), 'utf-8');
            $obj->condicionmercado= $request->input('condicionmercado');
            $obj->km= $request->input('km');
            $obj->tipomantenimiento= $request->input('tipomantenimiento');
            $obj->taller_id= $request->input('taller_id');
            if($obj->condicionmercado=="Nacional"){
                $obj->dias = $request->input('dias1');
            }else{
                $obj->dias = $request->input('dias2');
            }
            $obj->concesionaria_id = Concesionaria::getConcesionariaActual()->id;
            $obj->save();

            $cantidad = $request->input('txtCantidad');
            $unidad = $request->input('txtUnidad');
            $codigo = $request->input('txtCodigo');
            $descripcion = $request->input('txtDescripcion');
            $proveedor1 = $request->input('txtProveedor1');
            $proveedor2 = $request->input('txtProveedor2');
            $proveedor3 = $request->input('txtProveedor3');
            $ids = $request->input('iddetalle');

            if($descripcion!=""){
                for ($i = 0; $i < count($descripcion); $i++) {
                    if($ids[$i]>=0){
                        $detalle = DetalleDocmat::find($ids[$i]);
                    }else{
                        $detalle = new DetalleDocmat();
                    }
                    $detalle -> docmat_id =$obj->id;
                    $detalle -> cantidad  = $cantidad[$i];
                    $detalle -> unidad  = $unidad[$i];
                    $detalle -> codigo  = $codigo[$i];
                    $detalle -> descripcion  = $descripcion[$i];
                    $detalle -> proveedor1  = $proveedor1[$i];
                    $detalle -> proveedor2  = $proveedor2[$i];
                    $detalle -> proveedor3  = $proveedor3[$i];
                    $detalle -> save();
                }
            }

            $idseliminados=$request->idseliminados;

            if($idseliminados!=null){
                foreach($idseliminados as $k=>$idd){
                    $error = DB::transaction(function() use($idd){
                        if($idd>0){
                            $detalle = Detalledocmat::find($idd);
                            $detalle->delete();
                        }
                    }); 
                }
            }   


            event(new UserHasEdited($objCop,$obj,'docmat',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'docmat');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Docmat::find($id);
        $entidad  = 'Docmat';
        $formData = array('route' => array('docmat.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'docmat');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $obj = Docmat::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'Docmat', auth()->user()->id,'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportar(Request $request){

        return Excel::download(new DocmatExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('tipo'),$request->input('garita'),$request->input('turno')), 'Docmat.xlsx');
    }

    public function generatePDF(Request $request) {
        $id=$request->id;
        if ( $request->id == null || !is_numeric($request->id) ) return;
        
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
        ->where('users.id','=',auth()->user()->id)
        ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $ConcAct=$ConcesionariaActual[0]->razonsocial;
        $docmat = Docmat::find($id);

        $namefile = 'DocMat - '.$docmat->numero.'.pdf';  
        
        
        $detalles=Detalledocmat::where('docmat_id','=',$id)
            ->select('detalledocmat.*')
            ->get();

        $data=[];
        $data['docmat'] = $docmat;
        $data['detalles'] = $detalles;
        $data['namefile'] = $namefile;
        $data['concesionaria'] = $ConcAct;
        $data['concesionaria_id'] = $ConcesionariaActual[0]->id;

        // dd($data);
        $html = view('app.docmat.pdf.template_individual', $data)->render();

        $mpdf = new Mpdf();
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);

        $mpdf->Output($namefile, "I");
    }
}
