<?php

namespace App\Http\Controllers;

use App\AbastecimientoCombustible;
use App\Concesionaria;
use App\Controldiario;
use App\Equipo;
use App\Librerias\Libreria;
use App\Rules\SearchUaPadre2;
use App\Rules\SelectDifZero;
use App\Ua;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UaExport;
use App\Exports\UaExport2;
use App\Imports\UaImport;
use App\Responsable;
use App\Vehiculo;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Events\UserHasEdited;
use App\Events\UserHasCreatedOrDeleted;

class UaController extends Controller{
    protected $folderview      = 'app.ua';
    protected $tituloAdmin     = 'UA';
    protected $tituloRegistrar = 'Registrar UA';
    protected $tituloModificar = 'Modificar UA';
    protected $tituloEliminar  = 'Eliminar UA';
    protected $rutas           = array('create' => 'ua.create', 
            'edit'   => 'ua.edit', 
            'delete' => 'ua.eliminar',
            'search' => 'ua.buscar',
            'index'  => 'ua.index',
    );

    public function buscar(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Ua';
        $nombre           = Libreria::getParam($request->input('descripcion'));
        $codigo           = Libreria::getParam($request -> input('codigo'));
        $esPadre           = Libreria::getParam($request -> input('is_father'));
        $resultado        = Ua::
            select('ua.id', 'codigo', 'descripcion', 'habilitada', 'fecha_inicio', 'fecha_fin',
                'ua.ua_padre_id', 'concesionaria_id', 'created_at', 'updated_at', 'deleted_at','ua.padre','ua.responsable_id',
                DB::raw("(case when ua.ua_padre_id is null or ua.ua_padre_id=0 then '-' else (select concat(ua2.codigo,' ',ua2.descripcion) from ua as ua2 where ua2.id=ua.ua_padre_id) end) as uaPadre2")
            )
            -> where([
                [ 'descripcion', 'LIKE', '%'.strtoupper($nombre).'%' ],
                [ 'codigo', 'LIKE', '%'.$codigo.'%' ],
                [ 'ua.padre', 'LIKE', '%'.$esPadre.'%' ],
                [ 'concesionaria_id', $this -> getConsecionariaActual() ] 
                ])
            -> orderBy('id', 'ASC')
            -> orderBy('codigo', 'ASC')
            -> orderBy('descripcion', 'ASC');
        $lista            = $resultado -> get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Opciones', 'numero' => '2');
        $cabecera[]       = array('valor' => 'Seleccionar', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ua padre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Es padre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Responsable', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;

        $uaLst =  Ua::where('concesionaria_id','=', $this -> getConsecionariaActual())
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_fin','<',DB::raw('now()'))
                    ->where('habilitada','=',true)
                    ->get();
        foreach($uaLst as $uaDB){
            $hoy = new DateTime(date("Y-m-d")); 
            $fechaFin = new DateTime($uaDB -> fecha_fin);
            if($hoy > $fechaFin){          
               $uaNew = new Ua();
               $uaNew = $uaDB;
               $uaNew -> habilitada = false;
               $uaNew -> save();
           }
        }

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
        
        $entidad          = 'Ua';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
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
        $uaCodigo = Ua::where('codigo','like',$request->input('codigo'))->first();
        if(!is_null($uaCodigo)){
            $codigoDuplicado = $uaCodigo->concesionaria->abreviatura;
        }else{
            $codigoDuplicado = '';
        }
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
            'codigo.unique' => 'Su código proporcionado ya existe, especifique otro. Registrada en '.$codigoDuplicado,
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
        $uaCodigo = Ua::where('codigo','like',$request->input('codigo'))->whereNotIn('id',[$id])->first();
        if(!is_null($uaCodigo)){
            $codigoDuplicado = $uaCodigo->concesionaria->abreviatura;
        }else{
            $codigoDuplicado = '';
        }
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
            'codigo.unique' => 'Su código proporcionado ya existe, especifique otro. Registrada en '.$codigoDuplicado,
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

    private function handleVerifyUa($id){

        $existe = Libreria::verificarExistencia($id, 'ua');
        if ($existe !== true) throw new Exception('No existe una ua con este id.');
    
        //SEARCH SI ES PADRE
        $isFather = Ua::where('ua_padre_id', '=', $id) -> get();
        if( isset($isFather[0]) ) throw new Exception('Esta asignada a una ua.');
        
        //SEARCH SI TIENE RELACION CON EQUIPO
        $childEquipo = Equipo::where('ua_id', '=', $id) -> get();
        if( isset($childEquipo[0]) ) throw new Exception('Tiene relación con equipo.');
        
        //SEARCH SI TIENE RELACION CON VEHICULO
        $childVehiculo = Vehiculo::where('ua_id', '=', $id) -> get();
        if( isset($childVehiculo[0]) ) throw new Exception('Tiene relación con vehiculo.');

        //SEARCH SI TIENE RELACION CON CONTROL DIARIO
        $childControl = Controldiario::where('ua_id', '=', $id) -> get();
        if( isset($childControl[0]) ) throw new Exception('Tiene relación con control diario.');

        //SEARCH SI TIENE RELACION CON ABASTECIMIENTO DE COMBUSTIBLE
        $childControl = AbastecimientoCombustible::where('ua_id', '=', $id) -> get();
        if( isset($childControl[0]) ) throw new Exception('Tiene relación con abastecimiento combustible.');
    }

    public function destroyList(Request $request){
        
        $uaList = explode(',', $request -> get('uaList'));
        $errors = [];
    
        foreach ($uaList as $uaId) {
            try{
                $this -> handleVerifyUa($uaId);
                $this -> destroy($uaId);
            }catch(Exception $exception){
                $ua = Ua::where('id', $uaId) -> get();
                $ua[0]['error'] = $exception -> getMessage();
                array_push($errors, $ua[0]);
            }
        }
       
        $res = [ 'ok' => true, 'errors' => $errors ];

        return response() -> json($res);
    }

    public function destroyAll(){
        
        $uaList = Ua::where('concesionaria_id','=',$this -> getConsecionariaActual())->orderBy('id','asc')->get();
        $errors = [];
        foreach ($uaList as $ua) {
            try{
                $this -> handleVerifyUa($ua -> id);
                $this -> destroy($ua -> id);
            }catch(Exception $exception){
                $ua = Ua::where('id', $ua -> id) -> get();
                $ua[0]['error'] = $exception -> getMessage();
                array_push($errors, $ua[0]);
            }
        }

        $res = [ 'ok' => true, 'errors' => $errors ];

        return response() -> json($res);
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

    //PETICION GET QUE DEVUELVE TODOS LOS DATOS
    public function searchAutocomplete($query){
        // 
        $consulta = "SELECT id, codigo, descripcion, CONCAT(codigo, ' - ', descripcion) AS 'search'  
            FROM ua WHERE
            deleted_at IS NULL AND
            habilitada IS TRUE AND
            concesionaria_id = {$this -> getConsecionariaActual()} AND padre='N' and
            (codigo LIKE '%".$query."%' OR descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }
    
    public function searchAutocomplete2($query){
        // 
        $consulta = "SELECT id, codigo, descripcion, CONCAT(codigo, ' - ', descripcion) AS 'search'  
            FROM ua WHERE
            deleted_at IS NULL AND
            habilitada IS TRUE AND
            concesionaria_id = {$this -> getConsecionariaActual()} AND padre='S' and
            (codigo LIKE '%".$query."%' OR descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    public function searchAutocompleteTodos($query){
        // 
        $consulta = "SELECT id, codigo, descripcion, CONCAT(codigo, ' - ', descripcion) AS 'search',(select abreviatura from concesionaria where id=ua.concesionaria_id) as concesionaria
            FROM ua WHERE
            deleted_at IS NULL AND
            habilitada IS TRUE AND
            padre='N' and
            (codigo LIKE '%".$query."%' OR descripcion LIKE '%".$query."%')";
        $res = DB::select($consulta);
        
        return response() -> json($res);
    }

    //EXPORTAR E IMPORTAR EXCEL
    public function importExcel(Request $request){

        try{
            $file = $request -> file('ua-excel');
            Excel::import(new UaImport, $file);
            $res = ['ok' => true];

            return response() -> json($res);

        }catch(Exception $ex){
            $res = ['ok' => false, 'ex' => $ex->getMessage()];
       
            return response() -> json($res);
        }
    }

    public function exportExcel(){

        return Excel::download(new UaExport, 'ua-list.xlsx');
    }
    
    public function exportExcel2(){

        return Excel::download(new UaExport2, 'UA-Jerarquia-Reporte.xlsx');
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}

