<?php

namespace App\Http\Controllers;


use App\Equipo;
use App\Controldiario;
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
use App\Exports\ReporteHorometrajeExport;
use Illuminate\Support\Facades\Auth;

class ReporteHorometrajeController extends Controller
{
    protected $folderview      = 'app.reportehorometraje';
    protected $tituloAdmin     = 'Recorrido Horometro';
    protected $tituloModificar = 'Status Mantenimientos';
    protected $rutas           = array('create' => 'reportehorometraje.create', 
            'edit'   => 'reportehorometraje.edit', 
            'edit2'   => 'reportehorometraje.edit2',
            'edit3'   => 'reportehorometraje.edit3', 
            'edit4'   => 'reportehorometraje.edit4',
            'search' => 'reportehorometraje.buscar',
            'index'  => 'reportehorometraje.index',
    );

     public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Reportehorometraje';
        $estado           = $request->input('estado');
        $placa            = $request->input('placa');
        $concesionaria    = $request->input('concesionaria');
        $rule = [];	
        $resultado        = Equipo::join('horometraje' ,'horometraje.id','=','equipo.horometraje_id')
                                ->whereIn('contratista_id',[12,27,38])
                                ->where(function($sql) use($concesionaria){
                                    if($concesionaria!=""){
                                        $sql->where('concesionaria_id',$concesionaria);
                                    }
                                })
                                ->where(function($sql) use($placa){
                                    if($placa!=""){
                                        $sql->where('placa','LIKE','%' . $placa . '%');
                                    }
                                })
        						->where(function($sql) use($estado){
        						    if($estado=="1"){
        		                        $sql->where(DB::raw('cast(horometraje.limite_inf as DECIMAL)') , '>' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
        						    }elseif($estado=="2"){
        						        $sql->where(DB::raw('cast(horometraje.limite_sup as DECIMAL)') , '>' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'))
        						            ->where(DB::raw('cast(horometraje.limite_inf as DECIMAL)') , '<' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
        						    }elseif($estado=="3"){
        						        $sql->where(DB::raw('cast(horometraje.limite_sup as DECIMAL)') , '<' , DB::raw('cast(equipo.horometro_rec as DECIMAL)'));
        						    }
        						});
        		
        $resultado        = $resultado->select('equipo.*');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Regla Horometro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horometro inicial de Control', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horometro recorrido', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Horometro actual (contr/comb)', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Diferencia Horometro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado Mant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concesionaria', 'numero' => '1');
        if(auth()->user()->tipouser_id!="19"){
            $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '4');
        }
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Reportehorometraje';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $cboConcesionaria = array(""=>"Todos") + Concesionaria::orderBy('abreviatura','asc')->pluck('abreviatura','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta', 'cboConcesionaria'));
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportehorometraje';

        $boton    = 'Modificar';

        $UltimosMant = collect([]);
        
        
        $FechaUltimoMantEspecial = null;

        //Verifica si ya existió antes un mant especial
        /*if($UltimosMant->first()){
	        $UltimosMant->sortByDesc('fecha');

	        $FechaUltimoMantEspecial = date("Y-m-d",strtotime("-60 days",strtotime($UltimosMant->first()->fecha)));

        }*/

        $listaControldiario = Controldiario::where('equipo_id', $id)
        							->where(function($q) use ($FechaUltimoMantEspecial){
        								if($FechaUltimoMantEspecial){
        									$q->where('fecha','>=',$FechaUltimoMantEspecial);
        								}
        							})->select(DB::raw('distinct(fecha) as fecha'),DB::raw('\'0\' as id'),'horometro_final as kmfinal',DB::raw('\'-\' as tipo'))
                                    ->orderBy('fecha','desc')->get()->slice(0, 20);

        $listaControldiario = $listaControldiario->map(function ($item, $key) use($id) {
                                        $item->tipo = 1 ;
                                        $control = Controldiario::where('fecha',$item->fecha)->where('horometro_final',$item->kmfinal)->where('equipo_id',$id)->first();
                                        $item->id = $control->id;
                                        return  $item;
									});


        $listaAbastecimientoCombustible = AbastecimientoCombustible::where('equipo_id', $id)
        									->where(function($q) use ($FechaUltimoMantEspecial){
		        								if($FechaUltimoMantEspecial){
		        									$q->where('fecha_abastecimiento','>=',$FechaUltimoMantEspecial);
		        								}
		        							})->select('fecha_abastecimiento as fecha','id','km as kmfinal',DB::raw('\'-\' as tipo'))
                                            ->orderBy('fecha_abastecimiento','desc')
                                            ->get()->slice(0, 20);

		$listaAbastecimientoCombustible = $listaAbastecimientoCombustible->map(function ($item, $key) {
										$item->tipo = 2 ;
                                        return  $item;
										});

        $listaChecklist = Checklistvehicular::where('equipo_id', $id)
                                            ->where(function($q) use ($FechaUltimoMantEspecial){
                                                if($FechaUltimoMantEspecial){
                                                    $q->where('fecha_registro','>=',$FechaUltimoMantEspecial);
                                                }
                                            })->select('fecha_registro as fecha','id','k_final as kmfinal',DB::raw('\'-\' as tipo'))
                                            ->orderBy('fecha_registro','desc')
                                            ->get()->slice(0, 20);

        $listaChecklist = $listaChecklist->map(function ($item, $key) {
                                        $item->tipo = 3 ;
                                        return  $item;
                                        });
		       
        $listaRegRepVeh = RegRepVeh::where('equipo_id', $id)
                                            ->where(function($q) use ($FechaUltimoMantEspecial){
                                                if($FechaUltimoMantEspecial){
                                                    $q->where('fechasalida','>=',$FechaUltimoMantEspecial);
                                                }
                                            })->select('id','kmfinal','fechasalida as fecha',DB::raw('tipomantenimiento as tipo'))
                                            ->orderBy('fechasalida','desc')
                                            ->get()->slice(0, 10);

        $listaRegRepVeh = $listaRegRepVeh->map(function ($item, $key) {
                                $item->tipo = 4 ;
                                return $item;
                            });

        $listaRegManVeh = RegManVeh::where('equipo_id', $id)
                                            ->where(function($q) use ($FechaUltimoMantEspecial){
                                                if($FechaUltimoMantEspecial){
                                                    $q->where('fechasalida','>=',$FechaUltimoMantEspecial);
                                                }
                                            })->select('id','kmfinal','fechasalida as fecha',DB::raw('tipomantenimiento as tipo'))
                                            ->orderBy('fechasalida','desc')->get()->slice(0, 10);

        $listaRegManVeh = $listaRegManVeh->map(function ($item, $key) {
                                $item->tipo = 5 ;
                                return $item;
                            });
		//Uniendo todos las listas
        $ListaFinal = collect([]);

		$ListaFinal = $ListaFinal->merge($listaControldiario);

        $ListaFinal = $ListaFinal->merge($listaAbastecimientoCombustible);

        $ListaFinal = $ListaFinal->merge($listaChecklist);

        $ListaFinal = $ListaFinal->merge($listaRegRepVeh);

        $ListaFinal = $ListaFinal->merge($listaRegManVeh);

		$ListaFinal = $ListaFinal->sortByDesc('fecha');
        

        $equipo = Equipo::find($id);



        return view($this->folderview.'.mant')->with(compact('entidad','ListaFinal', 'equipo' , 'entidad', 'listar'));
    }

    public function update(Request $request)
    {   
        $error = DB::transaction(function() use($request){

        	$tipo = $request->input('tipo');
	        $Mant_id   = $request->input('Mant_id');
	        $equipo_id  = $request->input('equipo_id');


	        $equipo = Equipo::find( intval($equipo_id));
	        $recorrido = $equipo->horometroactual - $equipo->horometroinicial;
	        $actual = $equipo->horometroactual;
	        //$vehiculo->kilometraje_rec = 0;

	        switch (strval($tipo) ) {
	        	case '1':
	        		$Mantantenimiento = Controldiario::find( $Mant_id );
	        		$equipo->horometroinicial = $Mantantenimiento->horometro_final;
	        		break;

	        	case '2':
	        		$Mantantenimiento = AbastecimientoCombustible::find($Mant_id);
	        		$equipo->horometroinicial = $Mantantenimiento->km;
	        		break;

                case '3':
                    $Mantantenimiento = Checklistvehicular::find($Mant_id);
                    $equipo->horometroinicial = $Mantantenimiento->k_final;
                    break;

                case '4':
                    $Mantantenimiento = RegRepVeh::find($Mant_id);
                    $equipo->horometroinicial = $Mantantenimiento->kmfinal;
                    break;

                case '5':
                    $Mantantenimiento = RegManVeh::find($Mant_id);
                    $equipo->horometroinicial = $Mantantenimiento->kmfinal;
                    break;

	        	default:
	        		//$Mantantenimiento = RegManVeh::find($Mant_id);
	        		//$vehiculo->kilometraje_inicial = $Mantantenimiento->kmfinal;
	        		//$vehiculo->kilometraje_rec = $actual - $vehiculo->kilometraje_inicial;
	        		break;
	        }

	        /*$Mantantenimiento->especial = true;
	        $Mantantenimiento->save();*/
            //dd($equipo);
            $equipo->comentario='';
            $equipo->usercomentario_id=0;
            $equipo->estadorecorrido='';
	        $equipo->save();
	        

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

        return Excel::download(new ReporteHorometrajeExport($placa,$estado), 'ReporteHorometro.xlsx'); 
    }

    public function edit2($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportehorometraje';

        $boton    = 'Modificar';

        $equipo = Equipo::find($id);

        $formData = array('reportehorometraje.update2', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant2')->with(compact('entidad', 'equipo' , 'listar','formData','boton'));
    }

    public function update2($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $equipo = Equipo::find( intval($id));
            $equipo->horometroactual = $request->input('horometroactual');
            $equipo->horometro_rec = $equipo->horometroactual - $equipo->horometroinicial;
            $equipo->save();
        });

        return is_null($error) ? "OK" : $error;
    }

    public function edit3($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportehorometraje';

        $boton    = 'Modificar';

        $equipo = Equipo::find($id);

        $formData = array('reportehorometraje.update3', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant3')->with(compact('entidad', 'equipo' , 'listar','formData','boton'));
    }

    public function update3($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $user     = Auth::User();
            $equipo = Equipo::find( intval($id));
            $equipo->comentario = $request->input('comentario');
            $equipo->usercomentario_id = $user->id;
            $equipo->save();
        });

        return is_null($error) ? "OK" : $error;
    }

    public function edit4($id,Request $request){
        $existe = Libreria::verificarExistencia($id, 'equipo');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');

        $entidad  = 'Reportehorometraje';

        $boton    = 'Modificar';

        $equipo = Equipo::find($id);
        $cboEstadoRecorrido = array('NO EJECUTADO'=>'NO EJECUTADO','EJECUTADO'=>'EJECUTADO');
        $formData = array('reportehorometraje.update4', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant4')->with(compact('entidad', 'equipo' , 'listar','formData','boton','cboEstadoRecorrido'));
    }

    public function update4($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $equipo = Equipo::find( intval($id));
            $equipo->estadorecorrido = $request->input('estadorecorrido');
            $equipo->save();
        });

        return is_null($error) ? "OK" : $error;
    }
}
