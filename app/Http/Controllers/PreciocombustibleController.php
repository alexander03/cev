<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Preciocombustible;
use App\Tipocombustible;
use App\Grifo;
use App\Detallepreciocombustible;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreciocombustibleExport;


class PreciocombustibleController extends Controller
{
    protected $folderview      = 'app.preciocombustible';
    protected $tituloAdmin     = 'Precio Combustible';
    protected $tituloRegistrar = 'Registrar precio';
    protected $tituloModificar = 'Modificar precio';
    protected $tituloEliminar  = 'Eliminar precio';
    protected $rutas           = array('create' => 'preciocombustible.create', 
            'edit'   => 'preciocombustible.edit', 
            'delete' => 'preciocombustible.eliminar',
            'search' => 'preciocombustible.buscar',
            'index'  => 'preciocombustible.index',
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
        $entidad          = 'Preciocombustible';
        $grifo            = $request->input('grifo');
        $resultado        = Preciocombustible::where('fechainicio','>=',$request->input('fechainicio'))->orderBy('fechainicio', 'ASC');
        if($grifo!="0"){
            $resultado = $resultado->where('grifo_id','=',$grifo);
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Grifo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Incio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        
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
        $entidad          = 'Preciocombustible';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboGrifo = array("0" => "Todos") + Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboGrifo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Preciocombustible';
        $preciocombustible = null;
        $formData = array('preciocombustible.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboTipocombustible = array();
        $tipocombustible = Tipocombustible::orderBy('descripcion','asc')->get();
        foreach($tipocombustible as $k=>$v){
            $cboTipocombustible += array($v->id=>$v->descripcion);
        }
        $cboGrifo = Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('preciocombustible', 'formData', 'entidad', 'boton', 'listar','cboTipocombustible','cboGrifo'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'fechainicio' => 'required',
            'fechafin' => 'required',
            'tipocombustible' => 'required',
        );
        $mensajes = array(
            'fechainicio.required'         => 'Debe ingresar una fecha de inicio',
            'fechafin.required'         => 'Debe ingresar una fecha de fin',
            'tipocombustible.required'         => 'Debe ingresar los precios',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $precio2 = Preciocombustible::where('fechafin','>=',$request->input('fechainicio'))->where('fechainicio','<=',$request->input('fechainicio'))->where('grifo_id','=',$request->input('grifo_id'))->first();
        if(!is_null($precio2)){
            $dat = array("fechainicio" => ["Precio ya registrado en el rango de fecha"]);
            return json_encode($dat);
        }
        $error = DB::transaction(function() use($request){
            $obj = new Preciocombustible();
            $obj->fechainicio = $request->input('fechainicio');
            $obj->fechafin = $request->input('fechafin');
            $obj->grifo_id = $request->input('grifo_id'); 
            $obj->save();

            $tipo = $request->input('tipocombustible');
            $precio = $request->input('precio');
            for ($i=0; $i < count($tipo); $i++) { 
                $detalle = new Detallepreciocombustible();
                $detalle->preciocombustible_id = $obj->id;
                $detalle->tipocombustible_id = $tipo[$i];
                $detalle->precio = $precio[$i];
                $detalle->save();
            }
            event( new UserHasCreatedOrDeleted($obj->id,'preciocombustible', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'preciocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $preciocombustible = Preciocombustible::find($id);
        $entidad  = 'Preciocombustible';
        $formData = array('preciocombustible.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        $cboTipocombustible = array();
        $tipocombustible = Tipocombustible::orderBy('descripcion','asc')->get();
        foreach($tipocombustible as $k=>$v){
            $cboTipocombustible += array($v->id=>$v->descripcion);
        }
        $cboGrifo = Grifo::orderBy('descripcion','asc')->pluck('descripcion','id')->toArray();
        return view($this->folderview.'.mant')->with(compact('preciocombustible', 'formData', 'entidad', 'boton', 'listar', 'cboTipocombustible','cboGrifo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'preciocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'fechainicio' => 'required',
            'fechafin' => 'required',
        );
        $mensajes = array(
            'fechainicio.required'         => 'Debe ingresar una fecha de inicio',
            'fechafin.required'         => 'Debe ingresar una fecha de fin',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $obj = Preciocombustible::find($id);
            
            $objCop = json_encode($obj);

            $obj->fechainicio = $request->input('fechainicio');
            $obj->fechafin = $request->input('fechafin');
            $obj->grifo_id = $request->input('grifo_id');
            $obj->save();

            $detalle = Detallepreciocombustible::where('preciocombustible_id','=',$id)->get();
            foreach ($detalle as $key => $value) {
                $value->delete();
            }

            $tipo = $request->input('tipocombustible');
            $precio = $request->input('precio');
            for ($i=0; $i < count($tipo); $i++) { 
                $detalle = new Detallepreciocombustible();
                $detalle->preciocombustible_id = $obj->id;
                $detalle->tipocombustible_id = $tipo[$i];
                $detalle->precio = $precio[$i];
                $detalle->save();
            }

            event(new UserHasEdited($objCop,$obj,'preciocombustible',auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'preciocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $preciocombustible = Preciocombustible::find($id);
            event( new UserHasCreatedOrDeleted($preciocombustible->id,'preciocombustible', auth()->user()->id,'eliminar'));
            $preciocombustible->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'preciocombustible');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Preciocombustible::find($id);
        $entidad  = 'Preciocombustible';
        $formData = array('route' => array('preciocombustible.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function exportar(Request $request){
        $fechainicio = $request->input('fechainicio');
        $fechafin   = $request->input('fechafin');
        return Excel::download(new PreciocombustibleExport($fechainicio,$fechafin), 'preciocombustible.xlsx');
    }

}
