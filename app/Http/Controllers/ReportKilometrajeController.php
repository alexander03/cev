<?php

namespace App\Http\Controllers;


use App\Vehiculo;
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use App\RegManVeh;
use App\RegRepVeh;
use App\Concesionaria;
use App\Librerias\Libreria;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteKilometrajeExport;
use Illuminate\Support\Facades\Auth;

class ReportKilometrajeController extends Controller
{
    protected $folderview      = 'app.reportkilometraje';
    protected $tituloAdmin     = 'Recorrido Kilometraje';
    protected $tituloModificar = 'Status Mantenimientos';
    protected $rutas           = array('create' => 'reportkilometraje.create', 
            'edit'   => 'reportkilometraje.edit', 
            'edit2'   => 'reportkilometraje.edit2', 
            'edit3'   => 'reportkilometraje.edit3', 
            'edit4'   => 'reportkilometraje.edit4', 
            'search' => 'reportkilometraje.buscar',
            'index'  => 'reportkilometraje.index',
    );

     public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Reportkilometraje';
        $estado           = $request->input('estado');
        $placa            = $request->input('placa');
        $concesionaria    = $request->input('concesionaria');
        $user = Auth::User();
        $rule = [];	
        $resultado        = Vehiculo::join('kilometraje' ,'kilometraje.id','=','vehiculo.kilometraje_id')
                                ->where('placa','LIKE','%' . $placa . '%')
                                ->whereIn('contratista_id',[12,27,38])
        						//->where('concesionaria_id', $this->consecionariaActual())
                                ->where(function($sql) use($concesionaria){
                                    if($concesionaria!=""){
                                        $sql->where('concesionaria_id',$concesionaria);
                                    }
                                })
        						->where(function($sql) use($estado){
        						    if($estado=="1"){
        		                        $sql->where(DB::raw('cast(kilometraje.limite_inf as DECIMAL)') , '>' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
        						    }elseif($estado=="2"){
        						        $sql->where(DB::raw('cast(kilometraje.limite_sup as DECIMAL)') , '>' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'))
        						            ->where(DB::raw('cast(kilometraje.limite_inf as DECIMAL)') , '<' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
        						    }elseif($estado=="3"){
        						        $sql->where(DB::raw('cast(kilometraje.limite_sup as DECIMAL)') , '<' , DB::raw('cast(vehiculo.kilometraje_rec as DECIMAL)'));
        						    }
        						});
        		
        $resultado        = $resultado->select('vehiculo.*');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Regla KM', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'KM inicial de Control', 'numero' => '1');
        $cabecera[]       = array('valor' => 'KM recorrido', 'numero' => '1');
        $cabecera[]       = array('valor' => 'KM actual (check/comb/mant)', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Diferencia KM', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concesionaria', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '4');
        $titulo_modificar = $this->tituloModificar;
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar','ruta','user'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Reportkilometraje';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $cboConcesionaria = array(""=>"Todos") + Concesionaria::orderBy('abreviatura','asc')->pluck('abreviatura','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta','cboConcesionaria'));
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportkilometraje';

        $boton    = 'Modificar';

        $UltimosMant = collect([]);


        //ultimos mantenimientos especiales
        $UltimoChecklist = Checklistvehicular::where('especial',true)
        					->where('vehiculo_id',$id)->select('id','fecha_registro as fecha', 'vehiculo_id')->get()->first();
        if($UltimoChecklist){
        	$UltimosMant->offsetSet(1,$UltimoChecklist);
        }
        $UltimoControlCombustible = AbastecimientoCombustible::where('especial',true)->where('vehiculo_id',$id)
        								->select('id','fecha_abastecimiento as fecha', 'vehiculo_id')->get()->first();
        if($UltimoControlCombustible){
        	$UltimosMant->offsetSet(2,$UltimoControlCombustible);
        }
        $UltimoRegRepVeh = RegRepVeh::where('especial',true)->where('ua_id',$id)
        						->select('id','fechasalida as fecha','ua_id as vehiculo_id')->get()->first();
        if($UltimoRegRepVeh){
        	$UltimosMant->offsetSet(3,$UltimoRegRepVeh);
        }
        $UltimoRegManVeh = RegManVeh::where('especial',true)->where('ua_id',$id)
        						->select('id','fechasalida as fecha','ua_id as vehiculo_id')->get()->first();
        if($UltimoRegManVeh){
        	$UltimosMant->offsetSet(4,$UltimoRegManVeh);
        }
        
        
        $FechaUltimoMantEspecial = null;

        //Verifica si ya existió antes un mant especial
        /*if($UltimosMant->first()){
	        $UltimosMant->sortByDesc('fecha');

	        $FechaUltimoMantEspecial = date("Y-m-d",strtotime("-60 days",strtotime($UltimosMant->first()->fecha)));

        }*/

        $listaChecklistvehicular = Checklistvehicular::where('vehiculo_id', $id)
        							->where(function($q) use ($FechaUltimoMantEspecial){
        								if($FechaUltimoMantEspecial){
        									$q->where('fecha_registro','>=',$FechaUltimoMantEspecial);
        								}
        							})->select('id','k_final as kmfinal','fecha_registro','fecha_registro as fecha',DB::raw('\'-\' as tipo2'))
                                    ->orderBy('fecha_registro','desc')->get()->slice(0, 10);

        $listaChecklistvehicular = $listaChecklistvehicular->map(function ($item, $key) {
                                        $item->tipo = 1 ;
                                        return  $item;
									});


        $listaAbastecimientoCombustible = AbastecimientoCombustible::where('vehiculo_id', $id)
        									->where(function($q) use ($FechaUltimoMantEspecial){
		        								if($FechaUltimoMantEspecial){
		        									$q->where('fecha_abastecimiento','>=',$FechaUltimoMantEspecial);
		        								}
		        							})->select('id','km as kmfinal','created_at as fecha_registro','fecha_abastecimiento as fecha',DB::raw('\'-\' as tipo2'))
                                            ->orderBy('fecha_abastecimiento','desc')
                                            ->get()->slice(0, 10);

		$listaAbastecimientoCombustible = $listaAbastecimientoCombustible->map(function ($item, $key) {
										$item->tipo = 2 ;
                                        return  $item;
										});

		$listaRegRepVeh = RegRepVeh::where('ua_id', $id)
        									->where(function($q) use ($FechaUltimoMantEspecial){
		        								if($FechaUltimoMantEspecial){
		        									$q->where('fechasalida','>=',$FechaUltimoMantEspecial);
		        								}
		        							})->select('id','kmfinal','fechasalida as fecha_registro','fechamantenimiento as fecha',DB::raw('tipomantenimiento as tipo2'))
                                            ->orderBy('fechasalida','desc')
                                            ->get()->slice(0, 10);

		$listaRegRepVeh = $listaRegRepVeh->map(function ($item, $key) {
                                $item->tipo = 3 ;
						    	return $item;
							});

		$listaRegManVeh = RegManVeh::where('ua_id', $id)
        									->where(function($q) use ($FechaUltimoMantEspecial){
		        								if($FechaUltimoMantEspecial){
		        									$q->where('fechasalida','>=',$FechaUltimoMantEspecial);
		        								}
		        							})->select('id','kmfinal','fechasalida as fecha_registro','fechamantenimiento as fecha',DB::raw('tipomantenimiento as tipo2'))
                                            ->orderBy('fechasalida','desc')->get()->slice(0, 10);

		$listaRegManVeh = $listaRegManVeh->map(function ($item, $key) {
						  		$item->tipo = 4 ;
                                return $item;
							});
		       
       // 
		//Uniendo todos las listas
        $ListaFinal = collect([]);

		$ListaFinal = $ListaFinal->merge($listaChecklistvehicular);

        $ListaFinal = $ListaFinal->merge($listaAbastecimientoCombustible);

		$ListaFinal = $ListaFinal->merge($listaRegRepVeh);

		$ListaFinal = $ListaFinal->merge($listaRegManVeh);

		$ListaFinal = $ListaFinal->sortByDesc('fecha');
        

        $vehiculo = Vehiculo::find($id);



        return view($this->folderview.'.mant')->with(compact('entidad','ListaFinal', 'vehiculo' , 'entidad', 'listar'));
    }

    public function update(Request $request)
    {   
        $error = DB::transaction(function() use($request){

        	$tipo = $request->input('tipo');
	        $Mant_id   = $request->input('Mant_id');
	        $vehiculo_id  = $request->input('vehiculo_id');


	        $vehiculo = Vehiculo::find( intval($vehiculo_id));
	        $recorrido = $vehiculo->kilometraje_rec;
	        $actual = $vehiculo->kilometraje_act;
	        //$vehiculo->kilometraje_rec = 0;

	        switch (strval($tipo) ) {
	        	case '1':
	        		$Mantantenimiento = Checklistvehicular::find( $Mant_id );
	        		$vehiculo->kilometraje_inicial = $Mantantenimiento->k_final;
	        		$vehiculo->kilometraje_rec = $actual - $vehiculo->kilometraje_inicial;
	        		break;

	        	case '2':
	        		$Mantantenimiento = AbastecimientoCombustible::find($Mant_id);
	        		$vehiculo->kilometraje_inicial = $Mantantenimiento->km;
	        		$vehiculo->kilometraje_rec = $actual - $vehiculo->kilometraje_inicial;
	        		break;

	        	case '3':
	        		$Mantantenimiento = RegRepVeh::find($Mant_id);
	        		$vehiculo->kilometraje_inicial = $Mantantenimiento->kmfinal;
	        		$vehiculo->kilometraje_rec = $actual - $vehiculo->kilometraje_inicial;
	        		break;

	        	default:
	        		$Mantantenimiento = RegManVeh::find($Mant_id);
	        		$vehiculo->kilometraje_inicial = $Mantantenimiento->kmfinal;
	        		$vehiculo->kilometraje_rec = $actual - $vehiculo->kilometraje_inicial;
	        		break;
	        }

	        $Mantantenimiento->especial = true;
	        $Mantantenimiento->save();
            $vehiculo->comentario='';
            $vehiculo->usercomentario_id=0;
            $vehiculo->estadorecorrido='';
	        $vehiculo->save();
	        

	    });

        return is_null($error) ? "OK" : $error;
    }

    public function edit2($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportkilometraje';

        $boton    = 'Modificar';

        $vehiculo = Vehiculo::find($id);

        $formData = array('reportkilometraje.update2', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant2')->with(compact('entidad', 'vehiculo' , 'listar','formData','boton'));
    }

    public function update2($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $vehiculo_id  = $id;
            $vehiculo = Vehiculo::find( intval($vehiculo_id));
            $vehiculo->kilometraje_act = $request->input('kilometraje_act');
            $vehiculo->kilometraje_rec = $vehiculo->kilometraje_act - $vehiculo->kilometraje_inicial;
            $vehiculo->save();

        });

        return is_null($error) ? "OK" : $error;
    }

    private function consecionariaActual(){
        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }

    public function exportExcel(Request $request){
        $estado           = $request->input('estado');
        $placa            = $request->input('placa');

        return Excel::download(new ReporteKilometrajeExport($placa,$estado), 'ReporteKilometraje.xlsx'); 
    }

    public function edit3($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportkilometraje';

        $boton    = 'Modificar';

        $vehiculo = Vehiculo::find($id);

        $formData = array('reportkilometraje.update3', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant3')->with(compact('entidad', 'vehiculo' , 'listar','formData','boton'));
    }

    public function update3($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $vehiculo_id  = $id;
            $user     = Auth::User();
            $vehiculo = Vehiculo::find( intval($vehiculo_id));
            $vehiculo->comentario = $request->input('comentario');
            $vehiculo->usercomentario_id = $user->id;
            $vehiculo->save();

        });

        return is_null($error) ? "OK" : $error;
    }

    public function edit4($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'vehiculo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportkilometraje';

        $boton    = 'Modificar';

        $vehiculo = Vehiculo::find($id);

        $formData = array('reportkilometraje.update4', $id);
        $cboEstadoRecorrido = array('NO EJECUTADO'=>'NO EJECUTADO','EJECUTADO'=>'EJECUTADO');
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant4')->with(compact('entidad', 'vehiculo' , 'listar','formData','boton','cboEstadoRecorrido'));
    }

    public function update4($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $vehiculo_id  = $id;
            $user     = Auth::User();
            $vehiculo = Vehiculo::find( intval($vehiculo_id));
            $vehiculo->estadorecorrido = $request->input('estadorecorrido');
            $vehiculo->save();

        });

        return is_null($error) ? "OK" : $error;
    }
}
