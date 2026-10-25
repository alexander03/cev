<?php

namespace App\Http\Controllers;


use App\Vehiculo;
use App\Equipo;
use App\Vehiculodocument;
use App\Equipodocument;
use App\Concesionaria;
use App\Librerias\Libreria;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Exports\RastreoVehiculoExport;

class RastreoVehiculoController extends Controller
{
    protected $folderview      = 'app.rastreovehiculo';
    protected $tituloAdmin     = 'Rastreo Vehiculo';
    protected $tituloModificar = 'Editar Comentario';
    protected $rutas           = array('create' => 'rastreovehiculo.create', 
            'edit'   => 'rastreovehiculo.edit', 
            'search' => 'rastreovehiculo.buscar',
            'index'  => 'rastreovehiculo.index',
    );

     public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'RastreoVehiculo';
        $placa            = $request->input('placa');
        $fechainicio      = $request->input('fechainicio');
        $fecha            = $request->input('fechainicio');
        $fechafin         = $request->input('fechafin');

        $resultado        = Vehiculo::where('placa','like','%'.$placa.'%')
                            ->where('concesionaria_id','=', $this->concesionariaActual())
                            ->whereIn('contratista_id',[12,27])
                            ->whereIn('estado',['Activo','Temporal'])
                            ->orderBy('placa','asc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'KM Mant.', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'KM Mant. Repuesto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Entrada', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Salida', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Checklist Vehicular', 'numero' => '1');
        $titulo_modificar = $this->tituloModificar;
        
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            //$lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar','ruta','fechainicio','fechafin','fecha'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'RastreoVehiculo';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta'));
    }

    private function concesionariaActual(){
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

    public function edit($id,Request $request){
        if($request->input('tipo')=="vehiculo"){
            $existe = Libreria::verificarExistencia($id, 'vehiculodocument');
            $document = Vehiculodocument::find($id);
            $placa = $document->vehiculo->placa;
        }else{
            $existe = Libreria::verificarExistencia($id, 'equipodocument');
            $document = Equipodocument::find($id);
            $placa = $document->equipo->placa;
        }
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $tipo2 = $request->input('tipo');

        $entidad  = 'DocumentoVencido';

        $boton    = 'Modificar';

        $formData = array('documentovencido.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        return view($this->folderview.'.mant')->with(compact('entidad', 'document' , 'listar','formData','boton','tipo2','placa'));
    }

    public function update($id,Request $request)
    {   
        $error = DB::transaction(function() use($request,$id){
            $vehiculo_id  = $id;
            $user     = Auth::User();
            if($request->input('tipo2')=="vehiculo"){
                $vehiculo = Vehiculodocument::find( intval($vehiculo_id));
            }else{
                $vehiculo = Equipodocument::find( intval($vehiculo_id));
            }
            $vehiculo->comentario = $request->input('comentario');
            $vehiculo->usercomentario_id = $user->id;
            $vehiculo->save();

        });

        return is_null($error) ? "OK" : $error;
    }

    public function exportar(Request $request){

        return Excel::download(new RastreoVehiculoExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('placa')), 'rastreovehicular.xlsx');
    }
}
