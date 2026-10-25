<?php

namespace App\Http\Controllers;

use App\Concesionaria;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre2;
use App\Rules\SelectDifZero;
use App\VelocidadVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VelocidadVehiculoImport;
use App\Exports\VelocidadVehiculoExport;
use App\Exports\VelocidadVehiculoExport2;
use App\Exports\VelocidadVehiculoExport3;
use App\Exports\VelocidadVehiculoExportDetallado;
use App\Responsable;
use App\Zona;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class VelocidadVehiculoController extends Controller{
    protected $folderview      = 'app.velocidadvehiculo';
    protected $tituloAdmin     = 'Velocidad Vehiculo';
    protected $entidad         = 'VelocidadVehiculo';
    protected $tituloRegistrar = 'Registrar UA';
    protected $tituloModificar = 'Modificar UA';
    protected $tituloEliminar  = 'Eliminar UA';
    protected $rutas           = array('create' => 'ua.create', 
            'edit'   => 'ua.edit', 
            'delete' => 'ua.eliminar',
            'search' => 'velocidadvehiculo.buscar',
            'index'  => 'velocidadvehiculo.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = $this->entidad;
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $fechafin         = Libreria::getParam($request -> input('fechafin'));
        $zona             = Libreria::getParam($request -> input('zona'));
        $placa             = Libreria::getParam($request -> input('placa'));
        $localizacion     = Libreria::getParam($request -> input('localizacion'));
        $resultado        = VelocidadVehiculo::join('vehiculo','vehiculo.id','=','velocidadvehiculo.vehiculo_id')
            ->select('velocidadvehiculo.*')
            -> where([
                [ 'velocidadvehiculo.concesionaria_id', $this -> getConsecionariaActual() ] 
                ])
            -> orderBy('fecha', 'ASC');
        if($fechainicio!=""){
            $resultado = $resultado->whereRaw("fecha >= '$fechainicio 00:00:00'");
        }
        if($fechafin!=""){
            $resultado = $resultado->whereRaw("fecha <= '$fechafin 00:00:00'");
        }
        if($zona!=""){
            $resultado = $resultado->where('zona_id','=',$zona);
        }
        if($localizacion!=""){
            $resultado = $resultado->where('localizacion','like','%'.strtoupper(trim($localizacion)).'%');
        }
        if($placa!=""){
            $resultado = $resultado->where('placa','like','%'.strtoupper(trim($placa)).'%');
        }
        $lista            = $resultado -> get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Placa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Velocidad KM/H', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Rumbo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Latitud', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Longitud', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Localizacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Conductor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Zona', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Calle', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Mapa', 'numero' => '1');
        
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
            $lista = $resultado -> paginate($filas);
            $request->replace(array('page' => $paginaactual));

            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }
    

    public function index(){
        
        $entidad          = $this->entidad;
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboZona = array(''=>'Todos') + Zona::orderBy('nombre','asc')->pluck('nombre','id')->toArray();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboZona'));
    }

    public function create(Request $request, Unidad $unidadModel){

        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Ua';
        $ua = null;
        $formData = array('ua.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        
        $responsableList  = Responsable::select('id', 'nombre')->get();
        $unidadList = $unidadModel -> all();
        
        return view($this->folderview.'.mant')->with(compact('ua', 'formData', 'entidad', 'boton', 'listar', 'unidadList', 'responsableList'));
    }

    public function store(Request $request){

        $reglas     = [
            'codigo' => 'required|unique:ua,codigo,NULL,id,deleted_at,NULL|max:8|',
            'descripcion' => 'required',
            'habilitada' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'nullable|after_or_equal:fecha_inicio',
            'ua_padre_id' => [ new SearchUaPadre2() ]
        ];
        $mensajes = [
            'codigo.required' => 'Su código es requerido',
            'codigo.unique' => 'Su código proporcionado ya existe, especifique otro',
            'codigo.max' => 'Su código debe ser maximo de 8 carácteres',
            'descripcion.required' => 'Su descripcion es requerida',
            'habilitada.required' => 'El estado de la ua es requerida',
            'fecha_inicio.required' => 'Su fecha de inicio es requerida',
            'fecha_fin.after_or_equal' => 'Su fecha de fin no puede ser menor que la de inicio'
		];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        if($request -> input('ua_padre_id')){
            $uaDB =  Ua::where('codigo', $request -> input('ua_padre_id'))->where('concesionaria_id','=',$this -> getConsecionariaActual()) -> first();
            if(strtotime($uaDB->fecha_inicio)>strtotime($request->input('fecha_inicio'))){
                //echo "hola";
                return json_encode(['fecha_inicio' => ['Error fecha inicio no puede ser menor a la fecha de la UA Padre '.date("d/m/Y",strtotime($uaDB->fecha_inicio))]]);
            }
        }

        $listar     = Libreria::getParam($request->input('listar'), 'NO');
    
        $error = DB::transaction(function() use($request){
            $ua = new Ua();
            $ua -> codigo = $request -> input('codigo');
            $ua -> descripcion = strtoupper($request->input('descripcion'));
            $ua -> habilitada = $request -> input('habilitada');
            $ua -> concesionaria_id = $this -> getConsecionariaActual();
            $ua -> fecha_inicio = $request -> input('fecha_inicio');
            $ua -> fecha_fin = $request -> input('fecha_fin');
            $ua -> padre = $request->input('padre');
            //BUSCAR
            if($request -> input('ua_padre_id')){
                $uaDB =  Ua::where('codigo', $request -> input('ua_padre_id')) -> where('concesionaria_id','=',$this -> getConsecionariaActual()) -> get();
                $ua -> ua_padre_id = (!( $uaDB -> isEmpty() )) ? $uaDB[0] -> id : null;
            }
            //RESPONSABLE SI EXISTIERA
            if($request -> input('responsable_id')){
                $ua -> responsable_id = $request -> input('responsable_id');
            }
            $ua -> save();

            event( new UserHasCreatedOrDeleted($ua->id,'ua', auth()->user()->id,'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id){}

    public function edit($id, Request $request, Unidad $unidadModel){

        $existe = Libreria::verificarExistencia($id, 'ua');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $ua = Ua::find($id);
        $entidad  = 'Ua';
        $formData = array('ua.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        $responsableList  = Responsable::select('id', 'nombre')->get();
        $unidadList = $unidadModel -> all();

        return view($this->folderview.'.mant')->with(compact('ua', 'formData', 'entidad', 'boton', 'listar', 'unidadList', 'responsableList'));
    }

    public function update(Request $request, $id){

        $reglas     = [
            'codigo' => 'required|unique:ua,codigo,'.$id.',id,deleted_at,NULL|max:8',
            'descripcion' => 'required',
            'habilitada' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'nullable|after_or_equal:fecha_inicio',
            'ua_padre_id' => [ new SearchUaPadre2() ]
        ];
        $mensajes = [
            'codigo.required' => 'Su código es requerido',
            'codigo.unique' => 'Su código proporcionado ya existe, especifique otro',
            'codigo.max' => 'Su código debe ser de maximo de 8 carácteres',
            'descripcion.required' => 'Su descripcion es requerida',
            'habilitada.required' => 'El estado de la ua es requerida',
            'fecha_inicio.required' => 'Su fecha de inicio es requerida',
            'fecha_fin.after_or_equal' => 'Su fecha de fin no puede ser menor que la de inicio'
        ];

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        
        $uaDB =  Ua::where('ua_padre_id', $id)->where('concesionaria_id','=',$this -> getConsecionariaActual()) -> get();
        if(count($uaDB)>0 && $request -> input('habilitada')=="0"){
            //echo "hola";
            return json_encode(['habilitada' => ['Error no puede deshabilitar una UA que tiene UA asignados']]);
        }

        $existe = Libreria::verificarExistencia($id, 'ua');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $id){
            $ua = Ua::find($id);

            $uaOrg = json_encode($ua);

            $ua -> codigo = $request -> input('codigo');
            $ua -> descripcion = strtoupper($request->input('descripcion'));
            $ua -> habilitada = $request -> input('habilitada');
            $ua -> fecha_inicio = $request -> input('fecha_inicio');
            $ua -> fecha_fin = $request -> input('fecha_fin');
            $ua -> padre = $request->input('padre');
            //BUSCAR
            if($request -> input('ua_padre_id') && $request -> input('ua_padre_id')!=""){
                $uaDB =  Ua::where('codigo', $request -> input('ua_padre_id')) -> where('concesionaria_id','=',$this -> getConsecionariaActual()) -> get();
                $ua -> ua_padre_id = (!( $uaDB -> isEmpty() )) ? $uaDB[0] -> id : null;
            }else{
                $ua -> ua_padre_id = null;
            }
            //RESPONSABLE SI EXISTIERA
            if($request -> input('responsable_id')){
                $ua -> responsable_id = $request -> input('responsable_id');
            }else{
                $ua -> responsable_id = null;
            }
            $ua -> save();


            event( new UserHasEdited($uaOrg,$ua,'ua', auth()->user()->id));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego){

        $existe = Libreria::verificarExistencia($id, 'ua');
        if ($existe !== true) {
            return $existe;
        }

        //SEARCH SI ES PADRE
        $isFather = Ua::where('ua_padre_id', '=', $id) -> get();
        if( isset($isFather[0]) ){
            $childs = true;
            $entidadChild = 'UA';
            return view('app.confirmarEliminar')->with(compact('childs', 'entidadChild'));
        }

        //SEARCH SI TIENE RELACION CON EQUIPO
        $childEquipo = Equipo::where('ua_id', '=', $id) -> get();
        if( isset($childEquipo[0]) ){
            $childs = true;
            $entidadChild = 'Equipo';
            return view('app.confirmarEliminar')->with(compact('childs', 'entidadChild'));
        }

        //SEARCH SI TIENE RELACION CON VEHICULO
        $childVehiculo = Vehiculo::where('ua_id', '=', $id) -> get();
        if( isset($childVehiculo[0]) ){
            $childs = true;
            $entidadChild = 'Vehículo';
            return view('app.confirmarEliminar')->with(compact('childs', 'entidadChild'));
        }

        //SEARCH SI TIENE RELACION CON CONTROL DIARIO
        $childControl = Controldiario::where('ua_id', '=', $id) -> get();
        if( isset($childControl[0]) ){
            $childs = true;
            $entidadChild = 'Control diario';
            return view('app.confirmarEliminar')->with(compact('childs', 'entidadChild'));
        }

        //SEARCH SI TIENE RELACION CON ABASTECIMIENTO DE COMBUSTIBLE
        $childControl = AbastecimientoCombustible::where('ua_id', '=', $id) -> get();
        if( isset($childControl[0]) ){
            $childs = true;
            $entidadChild = 'Abastecimiento de combustible';
            return view('app.confirmarEliminar')->with(compact('childs', 'entidadChild'));
        }
       
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $mensaje = true;
        $modelo   = Ua::find($id);
        $entidad  = 'Ua';
        $formData = array('route' => array('ua.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }


    public function destroy($id){

        $existe = Libreria::verificarExistencia($id, 'ua');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $ua = Ua::find($id);

            event( new UserHasCreatedOrDeleted($ua->id,'ua', auth()->user()->id,'eliminar'));

            $ua->delete();
        });
        return is_null($error) ? "OK" : $error;
    }


    //EXPORTAR E IMPORTAR EXCEL
    public function importExcel(Request $request){

        try{
            $file = $request -> file('ua-excel');
            Excel::import(new VelocidadVehiculoImport, $file);
            $res = ['ok' => true];

            return response() -> json($res);

        }catch(Exception $ex){
            $res = ['ok' => false, 'ex' => $ex->getMessage()];
       
            return response() -> json($res);
        }
    }

    public function exportExcel(Request $request){

        return Excel::download(new VelocidadVehiculoExport($request->input('fechainicio'),$request->input('fechafin'),$request->input('localizacion'),$request->input('zona')), 'velocidadvehiculo.xlsx');
    }

    public function exportExcel2(Request $request){

        return Excel::download(new VelocidadVehiculoExport2($request->input('fechainicio'),$request->input('fechafin'),$request->input('localizacion'),$request->input('zona')), 'velocidadvehiculo2.xlsx');
    }

    public function exportExcel3(Request $request){

        return Excel::download(new VelocidadVehiculoExport3($request->input('fechainicio'),$request->input('fechafin'),$request->input('localizacion'),$request->input('zona')), 'velocidadvehiculoDetallado.xlsx');
    }

    public function exportExcelDetallado(Request $request){

        return Excel::download(new VelocidadVehiculoExportDetallado($request->input('fechainicio'),$request->input('fechafin'),$request->input('localizacion'),$request->input('zona')), 'velocidadvehiculoDetallado.xlsx');
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}

