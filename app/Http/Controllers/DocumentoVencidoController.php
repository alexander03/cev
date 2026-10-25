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
use App\Exports\ReporteKilometrajeExport;
use Illuminate\Support\Facades\Auth;

class DocumentoVencidoController extends Controller
{
    protected $folderview      = 'app.documentovencido';
    protected $tituloAdmin     = 'Doc. Vencidos';
    protected $tituloModificar = 'Editar Comentario';
    protected $rutas           = array('create' => 'documentovencido.create', 
            'edit'   => 'documentovencido.edit', 
            'search' => 'documentovencido.buscar',
            'index'  => 'documentovencido.index',
    );

     public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'DocumentoVencido';
        $placa            = $request->input('placa');
        $rule = [];	
        $fecha_actual = date("Y-m-d");
        $fecha_limite = date("Y-m-d",strtotime($fecha_actual."+ 1 month"));
        $resultado        = DB::select("select * from (
  (select vehiculo_id, tipo , MAX(fecha) as fecha,co.abreviatura as concesionaria ,'vehiculo' as tipo2
  from `vehiculodocument` 
  inner join vehiculo on `vehiculodocument`.`vehiculo_id` = `vehiculo`.`id`
  inner join concesionaria co on co.id=vehiculo.concesionaria_id 
  where `vehiculo`.`deleted_at` is null and `vehiculodocument`.`deleted_at` is null and `vehiculo`.contratista_id in (12,27,38) and `vehiculo`.estado not in ('Desmovilizado') 
  group by vehiculo_id , tipo,co.razonsocial
  having (fecha >= '$fecha_actual' and fecha <= '$fecha_limite') or fecha<'$fecha_actual'
  order by co.razonsocial asc,`vehiculodocument`.vehiculo_id asc)
  union all 
  (select equipo_id as vehiculo_id, tipo , MAX(fecha) as fecha,co.abreviatura as concesionaria ,'equipo' as tipo2
  from `equipodocument` 
  inner join equipo on `equipodocument`.`equipo_id` = `equipo`.`id`
  inner join concesionaria co on co.id=equipo.concesionaria_id 
  where `equipo`.`deleted_at` is null and `equipodocument`.`deleted_at` is null and equipo.contratista_id in (12,27,38) and equipo.estado not in ('Desmovilizado') 
  group by equipo_id , tipo,co.razonsocial
  having (fecha >= '$fecha_actual' and fecha <= '$fecha_limite') or fecha<'$fecha_actual'
  order by co.razonsocial asc,equipo_id asc)
  ) as T");
        $lista            = $resultado;
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Modelo', 'numero' => '1');;
        $cabecera[]       = array('valor' => 'Carroceria', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Venc.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concesionaria', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
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
            //$lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar','ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'DocumentoVencido';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta'));
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
}
