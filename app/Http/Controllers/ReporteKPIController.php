<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Periodo;
use App\Dataantigua;
use App\Controldiario;
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use App\Concesionaria;
use App\Vehiculo;
use App\Vehiculodocument;
use App\Equipo;
use App\Detallepreciocombustible;
use App\RegRepVeh;
use App\RegManVeh;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use App\Imports\DataantiguaImport;
use App\Exports\ReporteKPIMantPreventivoExport;
use App\Exports\ReporteKPIMantCorrectivoExport;
use App\Exports\ReporteKPICombustiblePesadoExport;
use App\Exports\ReporteKPICombustibleLivianoExport;
use App\Exports\ReporteKPIOtrosExport;
use App\Exports\ReporteKPIGastoPesadoExport;
use App\Exports\ReporteKPIGastoLivianoExport;
use App\Exports\ReporteKPIRendimientoPesadoExport;
use App\Exports\ReporteKPIRendimientoLivianoExport;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;

class ReporteKPIController extends Controller
{
    protected $folderview      = 'app.reportekpi';
    protected $tituloAdmin     = 'Reporte KPI';
    protected $tituloRegistrar = 'Registrar periodo';
    protected $tituloModificar = 'Modificar periodo';
    protected $tituloEliminar  = 'Eliminar periodo';
    protected $tituloActivar  = 'Activar periodo';
    protected $rutas           = array('create' => 'reportekpi.create', 
            'edit'   => 'periodo.edit', 
            'delete' => 'periodo.eliminar',
            'search' => 'reportekpi.buscar',
            'index'  => 'reportekpi.index',
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
        $entidad          = 'Periodo';
        $filter           = Libreria::getParam($request->input('descripcion'));
        $resultado        = Periodo::where('nombre','like','%'.$filter.'%');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_activar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'ReporteKPI';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $lista = Periodo::orderBy('fechainicio','asc')->get();
        $cboPeriodo = array();
        foreach ($lista as $key => $value) {
            $cboPeriodo += array($value->id => $value->nombre." (".date("d/m/Y",strtotime($value->fechainicio))." - ".date("d/m/Y",strtotime($value->fechafin)).")");
        }
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboPeriodo'));
    }
    
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Periodo';
        $periodo = null;
        $formData = array('periodo.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('periodo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100|unique:periodo,nombre');
        $mensajes = array(
            'nombre.required' => 'Debe ingresar una descripcion',
            'nombre.unique' => 'Esta periodo ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $Periodo = new Periodo();
            $Periodo->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $Periodo->fechainicio = $request->input('fechainicio');
            $Periodo->fechafin = $request->input('fechafin');
            $Periodo->save();
            event( new UserHasCreatedOrDeleted($Periodo->id,'periodo', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $periodo = Periodo::find($id);
        $entidad  = 'Periodo';
        $formData = array('periodo.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('periodo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        //Valido si la periodo tiene la misma descripcion que la proporcionada
        $reglas = array('nombre' => ['required','max:100',Rule::unique('periodo')->ignore($id)]);

        $mensajes = array(
            'nombre.required'         => 'Debe ingresar una descripción',
            'nombre.unique' => 'Esta periodo ya existe',
            'nombre.max' => 'La descripcion debe tener max. 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $Periodo = Periodo::find($id);
            $PeriodoCop = json_encode($Periodo);

            $Periodo->nombre= mb_strtoupper($request->input('nombre'), 'utf-8');
            $Periodo->fechainicio = $request->input('fechainicio');
            $Periodo->fechafin = $request->input('fechafin');
            $Periodo->save();

            event(new UserHasEdited($PeriodoCop,$Periodo,'periodo',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje=true;
        $modelo   = Periodo::find($id);
        $entidad  = 'Periodo';
        $formData = array('route' => array('periodo.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
    
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'periodo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Periodo = Periodo::find($id);
            event( new UserHasCreatedOrDeleted($Periodo->id,'periodo', auth()->user()->id,'eliminar'));
            $Periodo->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    //EXPORTAR E IMPORTAR EXCEL
    public function importExcel(Request $request){

        try{
            $file = $request -> file('ua-excel');
            Excel::import(new DataantiguaImport, $file);
            $res = ['ok' => true];

            return response() -> json($res);

        }catch(Exception $ex){
            $res = ['ok' => false, 'ex' => $ex->getMessage()];
       
            return response() -> json($res);
        }
    }

    public function exportExcelMantPreventivo(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIMantPreventivoExport($periodoinicial,$periodofinal), 'reportekpimantpreventivo.xlsx');
    }

    public function exportExcelMantCorrectivo(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIMantCorrectivoExport($periodoinicial,$periodofinal), 'reportekpimantcorrectivo.xlsx');
    }

    public function exportExcelCombustiblePesado(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPICombustiblePesadoExport($periodoinicial,$periodofinal), 'reportekpicombustiblepesado.xlsx');
    }

    public function exportExcelCombustibleLiviano(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPICombustibleLivianoExport($periodoinicial,$periodofinal), 'reportekpicombustibleliviano.xlsx');
    }

    public function exportExcelOtros(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIOtrosExport($periodoinicial,$periodofinal), 'reportekpiotros.xlsx');
    }

    public function exportExcelGastoPesado(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIGastoPesadoExport($periodoinicial,$periodofinal), 'reportekpigastopesado.xlsx');
    }

    public function exportExcelGastoLiviano(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIGastoLivianoExport($periodoinicial,$periodofinal), 'reportekpigastoliviano.xlsx');
    }

    public function exportExcelRendimientoPesado(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIRendimientoPesadoExport($periodoinicial,$periodofinal), 'reportekpirendimientopesado.xlsx');
    }

    public function exportExcelRendimientoLiviano(Request $request){
        $periodoinicial = $request->input('periodoinicial');
        $periodofinal   = $request->input('periodofinal');
        return Excel::download(new ReporteKPIRendimientoLivianoExport($periodoinicial,$periodofinal), 'reportekpirendimientoliviano.xlsx');
    }

    private function getMonths($periodoinicial,$periodofinal){
        setlocale(LC_TIME, 'es_ES');

        $months = [];
        $periodo = Periodo::find($periodoinicial);
        $month = new DateTime($periodo->fechafin);
        $month = new DateTime($month->format('Y-m')."-01");
    
        $periodo = Periodo::find($periodofinal);
        $end_date = new DateTime($periodo->fechafin);
        $end_date = new DateTime($end_date->format("Y-m")."-01");

        while ( $month <= $end_date ) {
            $new_month["name"] = substr(strtolower(strftime('%b', $month->getTimestamp())), 0, 3)."-".$month->format('Y');
            $new_month["value"] = $month->format('Y-m-')."01";
            $months[] = $new_month;
            $month = new DateTime(date('Y-m-d', strtotime($month->format('Y-m-d') . ' + 1 months')));
        }

        return $months;
    }

    public function graficoMantPreventivo(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->whereIn('contratista_id',[12,27,38])->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                    ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                    ->where('fecha_registro','<=',$fecha_registro_final_final)
                                    ->orderBy('fecha_registro','asc')
                                    ->orderBy('hora_inicio','asc')
                                    ->first();
                    if(!is_null($check)){
                        $inicial = $check->k_inicial;
                    }else{
                        $inicial = 0;
                    }

                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                        ->where('fecha_registro','<=',$fecha_registro_final_final)
                                        ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                        ->orderBy('fecha_registro','desc')
                                        ->orderBy('hora_fin','desc')
                                        ->first();
                    if(!is_null($check)){
                        $final = $check->k_final;
                    }else{
                        $final = 0;
                    }

                    $serie[$month["value"]][] = $final - $inicial;

                    $preventivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                                ->where('regrepveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regrepveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regrepveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                                ->groupBy('regrepveh.ua_id')->first();
                    if(is_null($preventivorespuesto)){
                        $preventivorespuesto=0;
                    }else{
                        $preventivorespuesto=$preventivorespuesto->total;
                    }

                    $preventivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                                ->where('regmanveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regmanveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regmanveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                                ->groupBy('regmanveh.ua_id')->first();
                    if(is_null($preventivomano)){
                        $preventivomano=0;
                    }else{
                        $preventivomano=$preventivomano->total;
                    }

                    $serie2[$month["value"]][] = round($preventivorespuesto + $preventivomano,2);

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','MANTENIMIENTO PREVENTIVO')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->costo+0,2);
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoMantCorrectivo(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->whereIn('contratista_id',[12,27,38])->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                    ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                    ->where('fecha_registro','<=',$fecha_registro_final_final)
                                    ->orderBy('fecha_registro','asc')
                                    ->orderBy('hora_inicio','asc')
                                    ->first();
                    if(!is_null($check)){
                        $inicial = $check->k_inicial;
                    }else{
                        $inicial = 0;
                    }

                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                        ->where('fecha_registro','<=',$fecha_registro_final_final)
                                        ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                        ->orderBy('fecha_registro','desc')
                                        ->orderBy('hora_fin','desc')
                                        ->first();
                    if(!is_null($check)){
                        $final = $check->k_final;
                    }else{
                        $final = 0;
                    }

                    $serie[$month["value"]][] = $final - $inicial;

                    $preventivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                                ->where('regrepveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regrepveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regrepveh.tipomantenimiento','=','2')
                                ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                                ->groupBy('regrepveh.ua_id')->first();
                    if(is_null($preventivorespuesto)){
                        $preventivorespuesto=0;
                    }else{
                        $preventivorespuesto=$preventivorespuesto->total;
                    }

                    $preventivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                                ->where('regmanveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regmanveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regmanveh.tipomantenimiento','=','2')
                                ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                                ->groupBy('regmanveh.ua_id')->first();
                    if(is_null($preventivomano)){
                        $preventivomano=0;
                    }else{
                        $preventivomano=$preventivomano->total;
                    }

                    $serie2[$month["value"]][] = round($preventivorespuesto + $preventivomano,2);

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','MANTENIMIENTO CORRECTIVO')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->costo+0,2);
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoCombustibleLiviano(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $combustible = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('vehiculo_id','=',$value->id)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'))
                            ->first();

                    $serie[$month["value"]][] = $combustible->qtdgl;

                    $listaAbastecimiento = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('vehiculo_id','=',$value->id)
                            ->get();
                    $precio = 0;
                    foreach ($listaAbastecimiento as $x => $y) {
                        $preciocombustible = Detallepreciocombustible::join('preciocombustible','preciocombustible.id','=','detallepreciocombustible.preciocombustible_id')
                                    ->where('preciocombustible.grifo_id','=',$y->grifo_id)
                                    ->where('detallepreciocombustible.tipocombustible_id','=',$y->tipocombustible_id)
                                    ->where('preciocombustible.fechainicio','<=',$y->fecha_abastecimiento)
                                    ->where('preciocombustible.fechafin','>=',$y->fecha_abastecimiento)
                                    ->first();
                        if(!is_null($preciocombustible)){
                            $precio += $preciocombustible->precio*$y->qtdgl;
                        }
                    }
                    $serie2[$month["value"]][] = round($precio,2);

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','COMBUSTIBLE DIESEL B5, EQUIPOS LIVIANOS - S/')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->costo+0,2);
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoCombustiblePesado(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Equipo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->descripcion;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $combustible = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('equipo_id','=',$value->id)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'))
                            ->first();

                    $serie[$month["value"]][] = $combustible->qtdgl;

                    $listaAbastecimiento = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('equipo_id','=',$value->id)
                            ->get();
                    $precio = 0;
                    foreach ($listaAbastecimiento as $x => $y) {
                        $preciocombustible = Detallepreciocombustible::join('preciocombustible','preciocombustible.id','=','detallepreciocombustible.preciocombustible_id')
                                    ->where('preciocombustible.grifo_id','=',$y->grifo_id)
                                    ->where('detallepreciocombustible.tipocombustible_id','=',$y->tipocombustible_id)
                                    ->where('preciocombustible.fechainicio','<=',$y->fecha_abastecimiento)
                                    ->where('preciocombustible.fechafin','>=',$y->fecha_abastecimiento)
                                    ->first();
                        if(!is_null($preciocombustible)){
                            $precio += $preciocombustible->precio*$y->qtdgl;
                        }
                    }
                    $serie2[$month["value"]][] = round($precio,2);

                }else{

                    $dataantigua = Dataantigua::where('equipo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','COMBUSTIBLE DIESEL B5, EQUIPOS PESADOS - S/')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->costo+0,2);
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoOtros(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->whereIn('contratista_id',[12,27,38])->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%SOAT%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $soat = $vehiculodocument->precio;
                    }else{
                        $soat = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%GPS%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $gps = $vehiculodocument->precio;
                    }else{
                        $gps = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%RTV%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $rtv = $vehiculodocument->precio;
                    }else{
                        $rtv = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%POLIZA%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $poliza = $vehiculodocument->precio;
                    }else{
                        $poliza = 0;
                    }

                    $serie[$month["value"]][] = round($soat + $gps + $rtv + $poliza,2);

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','OTROS')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->costo+0;
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoGastoLiviano(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->whereIn('contratista_id',[12,27,38])->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%SOAT%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $soat = $vehiculodocument->precio;
                    }else{
                        $soat = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%GPS%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $gps = $vehiculodocument->precio;
                    }else{
                        $gps = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%RTV%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $rtv = $vehiculodocument->precio;
                    }else{
                        $rtv = 0;
                    }
                    
                    $vehiculodocument = Vehiculodocument::where('vehiculo_id','=',$value->id)
                                        ->where('created_at','>=',$fecha_registro_final_inicial." 00:00:00")
                                        ->where('created_at','<=',$fecha_registro_final_final." 23:59:59")
                                        ->where(DB::raw('trim(tipo)'),'like','%POLIZA%')
                                        ->select(DB::raw('sum(precio) as precio'))
                                        ->orderBy('created_at','desc')
                                        ->first();
                    if(!is_null($vehiculodocument)){
                        $poliza = $vehiculodocument->precio;
                    }else{
                        $poliza = 0;
                    }

                    $listaAbastecimiento = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('vehiculo_id','=',$value->id)
                            ->get();
                    $precio = 0;
                    foreach ($listaAbastecimiento as $x => $y) {
                        $preciocombustible = Detallepreciocombustible::join('preciocombustible','preciocombustible.id','=','detallepreciocombustible.preciocombustible_id')
                                    ->where('preciocombustible.grifo_id','=',$y->grifo_id)
                                    ->where('detallepreciocombustible.tipocombustible_id','=',$y->tipocombustible_id)
                                    ->where('preciocombustible.fechainicio','<=',$y->fecha_abastecimiento)
                                    ->where('preciocombustible.fechafin','>=',$y->fecha_abastecimiento)
                                    ->first();
                        if(!is_null($preciocombustible)){
                            $precio += $preciocombustible->precio*$y->qtdgl;
                        }
                    }

                    $correctivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                                ->where('regrepveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regrepveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regrepveh.tipomantenimiento','=','2')
                                ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                                ->groupBy('regrepveh.ua_id')->first();
                    if(is_null($correctivorespuesto)){
                        $correctivorespuesto=0;
                    }else{
                        $correctivorespuesto=$correctivorespuesto->total;
                    }

                    $correctivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                                ->where('regmanveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regmanveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regmanveh.tipomantenimiento','=','2')
                                ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                                ->groupBy('regmanveh.ua_id')->first();
                    if(is_null($correctivomano)){
                        $correctivomano=0;
                    }else{
                        $correctivomano=$correctivomano->total;
                    }

                    $preventivorespuesto = RegRepVeh::join('descripcionregrepveh','descripcionregrepveh.regrepveh_id','=','regrepveh.id')
                                ->where('regrepveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regrepveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regrepveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregrepveh.cantidad*descripcionregrepveh.monto) as total'),'regrepveh.ua_id')
                                ->groupBy('regrepveh.ua_id')->first();
                    if(is_null($preventivorespuesto)){
                        $preventivorespuesto=0;
                    }else{
                        $preventivorespuesto=$preventivorespuesto->total;
                    }

                    $preventivomano = RegManVeh::join('descripcionregmanveh','descripcionregmanveh.regmanveh_id','=','regmanveh.id')
                                ->where('regmanveh.fechaentrada','>=',$fecha_registro_final_inicial)
                                ->where('regmanveh.fechaentrada','<=',$fecha_registro_final_final)
                                ->where('ua_id','=',$value->id)
                                ->where('regmanveh.tipomantenimiento','=','1')
                                ->select(DB::raw('sum(descripcionregmanveh.cantidad*descripcionregmanveh.monto) as total'))
                                ->groupBy('regmanveh.ua_id')->first();
                    if(is_null($preventivomano)){
                        $preventivomano=0;
                    }else{
                        $preventivomano=$preventivomano->total;
                    }

                    $serie[$month["value"]][] = round($soat + $gps + $rtv + $poliza + $precio + $correctivomano + $correctivorespuesto + $preventivomano + $preventivorespuesto,2);                    

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','GASTO TOTAL')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->costo+0;
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoGastoPesado(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Equipo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->descripcion;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $listaAbastecimiento = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('equipo_id','=',$value->id)
                            ->get();
                    $precio = 0;
                    foreach ($listaAbastecimiento as $x => $y) {
                        $preciocombustible = Detallepreciocombustible::join('preciocombustible','preciocombustible.id','=','detallepreciocombustible.preciocombustible_id')
                                    ->where('preciocombustible.grifo_id','=',$y->grifo_id)
                                    ->where('detallepreciocombustible.tipocombustible_id','=',$y->tipocombustible_id)
                                    ->where('preciocombustible.fechainicio','<=',$y->fecha_abastecimiento)
                                    ->where('preciocombustible.fechafin','>=',$y->fecha_abastecimiento)
                                    ->first();
                        if(!is_null($preciocombustible)){
                            $precio += $preciocombustible->precio*$y->qtdgl;
                        }
                    }

                    $serie[$month["value"]][] = round($precio,2);                    

                }else{

                    $dataantigua = Dataantigua::where('equipo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','GASTO TOTAL')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->costo+0;
                        
                    }else{
                        $serie[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie));
    }

    public function graficoRendimientoLiviano(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Vehiculo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->placa;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                    ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                    ->where('fecha_registro','<=',$fecha_registro_final_final)
                                    ->orderBy('fecha_registro','asc')
                                    ->orderBy('hora_inicio','asc')
                                    ->first();
                    if(!is_null($check)){
                        $inicial = $check->k_inicial;
                    }else{
                        $inicial = 0;
                    }

                    $check = Checklistvehicular::where('vehiculo_id','=',$value->id)
                                        ->where('fecha_registro','<=',$fecha_registro_final_final)
                                        ->where('fecha_registro','>=',$fecha_registro_final_inicial)
                                        ->orderBy('fecha_registro','desc')
                                        ->orderBy('hora_fin','desc')
                                        ->first();
                    if(!is_null($check)){
                        $final = $check->k_final;
                    }else{
                        $final = 0;
                    }

                    $serie[$month["value"]][] = $final - $inicial;

                    $combustible = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('vehiculo_id','=',$value->id)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'))
                            ->first();

                    $serie2[$month["value"]][] = $combustible->qtdgl;
                    if($combustible->qtdgl>0){
                        $serie3[$month["value"]][] = round(($final - $inicial)/$combustible->qtdgl,2);
                    }else{
                        $serie3[$month["value"]][] = 0;
                    }

                }else{

                    $dataantigua = Dataantigua::where('vehiculo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','RENDIMIENTOS - VEHICULOS LIVIANOS')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->valor2+0,2);
                        $serie3[$month["value"]][] = round($dataantigua->costo+0,2);
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                        $serie3[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        $listaSerie3 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
            $listaSerie3[] = array("name"=>$month["name"],"data"=>$serie3[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie,"serie3"=>$listaSerie3));
    }

    public function graficoRendimientoPesado(Request $request){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;
        
        $vehiculo = Equipo::where('concesionaria_id','=',$idConcAct)->orderBy('placa','asc')->get();
        $months = $this->getMonths($request->input('periodoinicial'),$request->input('periodofinal'));
        $placa = [];
        $serie = [];
        $serie2 = [];
        foreach ($vehiculo as $key => $value) {
            $placa[] = $value->descripcion;
            foreach ($months as $month) {
                if(strtotime($month["value"])>strtotime("2023-08-01")){//agosto 2023 el ultimo mes
                    $periodo = Periodo::where("fechafin",'>=',$month["value"])->orderBy('fechainicio','asc')->first();
                    $fecha_registro_final_inicial = $periodo->fechainicio ;
                    $fecha_registro_final_final = $periodo->fechafin;
                    //dd($periodo);
                    $dbControl = Controldiario::select(DB::raw('SUM(controldiario.hora_total) AS hora_total'))
                                ->where('controldiario.equipo_id', '=', $value->id)
                                ->where(function ($query) use ($fecha_registro_final_inicial, $fecha_registro_final_final) {
                                    $query->where('controldiario.fecha', '>=', $fecha_registro_final_inicial)
                                        ->where('controldiario.fecha', '<=', $fecha_registro_final_final);
                                })
                                ->first();


                    $serie[$month["value"]][] = $dbControl->hora_total;

                    $combustible = AbastecimientoCombustible::where('fecha_abastecimiento','>=',$fecha_registro_final_inicial)
                            ->where('fecha_abastecimiento','<=',$fecha_registro_final_final)
                            ->where('equipo_id','=',$value->id)
                            ->select(DB::raw('sum(qtdgl) as qtdgl'),DB::raw('sum(qtdl) as qtdl'))
                            ->first();

                    $serie2[$month["value"]][]= $combustible->qtdgl;
                    if($combustible->qtdgl>0){
                        $serie3[$month["value"]][] = round(($dbControl->hora_total)/$combustible->qtdgl,2);
                    }else{
                        $serie3[$month["value"]][] = 0;
                    }

                }else{

                    $dataantigua = Dataantigua::where('equipo_id','=',$value->id)
                                    ->where('mes','=',$month["value"])
                                    ->where('tipo','like','RENDIMIENTOS - VEHICULOS PESADOS')
                                    ->first();
                    if(!is_null($dataantigua)){
                        $serie[$month["value"]][] = $dataantigua->valor+0;
                        $serie2[$month["value"]][] = round($dataantigua->valor2+0,2);
                        $serie3[$month["value"]][] = round($dataantigua->costo+0,2);
                    }else{
                        $serie[$month["value"]][] = 0;
                        $serie2[$month["value"]][] = 0;
                        $serie3[$month["value"]][] = 0;
                    }
                }
            }
            
        }
        $listaSerie = [];
        $listaSerie2 = [];
        $listaSerie3 = [];
        foreach ($months as $month) {
            $listaSerie[] = array("name"=>$month["name"],"data"=>$serie[$month["value"]]);
            $listaSerie2[] = array("name"=>$month["name"],"data"=>$serie2[$month["value"]]);
            $listaSerie3[] = array("name"=>$month["name"],"data"=>$serie3[$month["value"]]);
        }
        return json_encode(array("placa"=>$placa,"serie2"=>$listaSerie2,"serie"=>$listaSerie,"serie3"=>$listaSerie3));
    }
}
