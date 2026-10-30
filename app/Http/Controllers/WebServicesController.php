<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ua;
use App\Brand;
use App\Area;
use App\Contratista;
use App\Concesionaria;
use App\Responsable;
use App\Vehiculo;
use App\User;
use App\UserConcesionaria;
use App\Conductor;
use App\Conductorconcesionaria;
use App\Grifo;
use App\Equipo;
use App\Unidad;
use App\Kilometraje;
use App\Carroceria;
use App\Abastecimiento;
use App\Tipocombustible;
use App\AbastecimientoCombustible;
use App\Checklistvehicular;
use App\DetalleChecklistvehicular;
use App\Control;
use App\Detallecontrol;
use App\Bitacora;
use App\Tipouser;
use App\Persona;
use App\Agente;
use App\Empresa;
use Illuminate\Support\Facades\DB;
use App\Librerias\Libreria;
use DateTime;
use Mail;
use Illuminate\Support\Facades\Log;
use App\Events\UserHasCreatedOrDeleted;
use App\Events\UserHasEdited;

class WebServicesController extends Controller{
    
    public function ua(){
        $data =  Ua::orderBy("ua.id", "ASC")->get();
        return json_encode(array("ua" => $data));
    }
    
    public function concesionaria(){
        $data =  Concesionaria::orderBy("concesionaria.id", "ASC")->get();
        return json_encode(array("concesionaria" => $data));
    }

    public function responsable(){
        $data =  Responsable::orderBy("responsable.id", "ASC")->get();
        return json_encode(array("responsable" => $data));
    }
    
    public function marca(){
        $data =  Brand::orderBy("marca.id", "ASC")->get();
        return json_encode(array("marca" => $data));
    }
    
    public function area(){
        $data =  Area::orderBy("area.id", "ASC")->get();
        return json_encode(array("area" => $data));
    }
    
    public function vehiculo(Request $request){
        $error = DB::transaction(function() use($request){
            $vehiculo = Vehiculo::where('placa','like',$request->input('placa'))->first();
            if(is_null($vehiculo)){
                $vehiculo = new Vehiculo();
            }
            //$vehiculo->ua               = strtoupper($request->input('ua'));
            $vehiculo->ua_id                  = $request->input('ua_id');
            $vehiculo->uatrabajo_id           = $request->input('uatrabajo_id');
            $vehiculo->modelo                 = strtoupper($request->input('modelo'));
            $vehiculo->marca_id               = $request->input('marca_id');
            $vehiculo->anio                   = $request->input('anio');
            $vehiculo->contratista_id         = $request->input('contratista_id');
            /*            
            $vehiculo->unidad                = $request->input('unidad'); 
            $vehiculo->fechavencimientosoat = $request->input('fechavencimientosoat');
            $vehiculo->fechavencimientogps  = $request->input('fechavencimientogps');
            $vehiculo->fechavencimientortv  = $request->input('fechavencimientortv');*/
            //$vehiculo->concesionaria_id       =  $this->concesionariaActual(); 
            $vehiculo->concesionaria_id       = $request->input('concesionaria_id'); 
            $vehiculo->area_id                = $request->input('area_id');
            $vehiculo->tipocombustible_id     = $request->input('tipocombustible_id');
            $vehiculo->placa                  = $request->input('placa');
            $vehiculo->motor                  = $request->input('motor');
            $vehiculo->asientos               = $request->input('asientos');
            $vehiculo->chasis                 = $request->input('chasis');
            $vehiculo->carroceria_id          = $request->input('carroceria_id');
            $vehiculo->color                  = $request->input('color');
            $vehiculo->kilometraje_ini        = $request->input('kilometraje_ini');
            $vehiculo->kilometraje_act        = $request->input('kilometraje_act')+0;
            $vehiculo->kilometraje_id         = $request->input('kilometraje_id');
            $vehiculo->decimales     = $request->input('decimales');
            $vehiculo->precio = $request->input('precio');
            $vehiculo->estado = $request->input('estado');
            $vehiculo->saltarkm  = $request->input('saltarkm');

            $vehiculo->save();


            event( new UserHasCreatedOrDeleted($vehiculo->id,'vehiculo', $request->input('user_id'),'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }
    
    public function usuario(Request $request){
        $error = DB::transaction(function() use($request){
            $user = User::find($request->input('id'));
            if(is_null($user)){
                $user = new User();
            }
            $user->tipouser_id= $request->input('tipouser_id');
            $user->name= mb_strtoupper($request->input('name'), 'utf-8');
            $user->username= $request->input('username');
            $user->correo= $request->input('correo');
            $user->password= $request->input('password');
            $user->save();

            event( new UserHasCreatedOrDeleted($user->id,'users', $request->input('user_id'),'crear'));

            $userConcesionarias = $user->getConcesionarias;

            $concesionarias = json_decode($request->input('concesionarias'));
            if($concesionarias != null) {
                foreach ($concesionarias as $con) {
                    $existeConcesionaria = false;
                    if(!is_null($userConcesionarias)){
                        foreach ($userConcesionarias as $usercon) {
                            if($con->id == $usercon->id) $existeConcesionaria = true;
                        }
                    }
                    if($existeConcesionaria === true ){ //EXISTE LA CONCESIONARIA
                        $userconcesionaria = UserConcesionaria::where("concesionaria_id","=",$con->id)->where('user_id','=',$user->id)->first();
                        $userconcesionaria->estado = $con->estado;
                        $userconcesionaria->save();
                    } else { //NO EXISTE LA CONCESIONARIA
                        $userconcesionaria = new UserConcesionaria();
                        $userconcesionaria->user_id = $user->id;
                        $userconcesionaria->concesionaria_id = $con->id;
                        $userconcesionaria->estado = $con->estado;
                        $userconcesionaria->save();
                    }

                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }
    
    public function userconcesionaria(){
        $data =  Userconcesionaria::orderBy("userconcesionaria.id", "ASC")->get();
        return json_encode(array("userconcesionaria" => $data));
    }
    
    public function conductor(Request $request){
        $error = DB::transaction(function() use($request){
            $tipo_conductor = Tipouser::where('descripcion', 'CONDUCTOR')->first();
            $user = new User();
            $user->tipouser_id= $tipo_conductor->id;
            $user->name = mb_strtoupper($request->input('name'), 'utf-8');
            $user->username= $request->input('username');
            $user->password= $request->input('password');
            $user->concesionaria_id = $request->input('concesionaria_id');
            $user->save();

            event( new UserHasCreatedOrDeleted($user->id,'user', $request->input('user_id'),'crear'));

            $userconcesionaria = new UserConcesionaria();
            $userconcesionaria->user_id = $user->id;
            $userconcesionaria->concesionaria_id = $request->input('concesionaria_id');
            $userconcesionaria->save();

            event( new UserHasCreatedOrDeleted($userconcesionaria->id,'userconcesionaria', $request->input('user_id'),'crear'));

            $conductor = new Conductor();
            $conductor->dni= $request->input('dni');
            $conductor->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $conductor->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $conductor->licencia= mb_strtoupper($request->input('licencia'), 'utf-8');
            $conductor->categoria= $request->input('categoria');
            $conductor->fechavencimiento= mb_strtoupper($request->input('fechavencimiento'), 'utf-8');
            $conductor->contratista_id= mb_strtoupper($request->input('contratista_id'), 'utf-8');
            $conductor->user_id = $user->id;
            $conductor->lider_id = $request->input('lider_id');
            $conductor->tipo = $request->input('tipo');
            $conductor->save();

            event( new UserHasCreatedOrDeleted($conductor->id,'conductor', $request->input('user_id'),'crear'));

            $conductorconcesionaria = new Conductorconcesionaria();
            $conductorconcesionaria->conductor_id = $conductor->id;
            $conductorconcesionaria->concesionaria_id = $concesionariaActual->id;
            $conductorconcesionaria->save();

            event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', $request->input('user_id'),'crear'));                

        });
        return is_null($error) ? "OK" : $error;
    }
    
    public function conductorconcesionaria(){
        $conductorconcesionaria = new Conductorconcesionaria();
        $conductorconcesionaria->conductor_id = $request->input('conductor_id');
        $conductorconcesionaria->concesionaria_id = $request->input('concesionaria_id');
        $conductorconcesionaria->save();

        event( new UserHasCreatedOrDeleted($conductorconcesionaria->id,'conductorconcesionaria', $request->input('user_id'),'crear'));
    }
    
    public function grifo(){
        $data =  Grifo::orderBy("grifo.id", "ASC")->get();
        return json_encode(array("grifo" => $data));
    }
    
    public function equipo(Request $request){
        $error = DB::transaction(function() use($request){
            $equipo = new Equipo();
//            $equipo->codigo             = strtoupper($request->input('codigo'));
            $equipo->ua_id                = $request->input('ua_id');
            if($request->input('uatrabajo_id')!=""){
                $equipo->uatrabajo_id         = $request->input('uatrabajo_id');
            }
            $equipo->descripcion          = strtoupper($request->input('descripcion'));
            $equipo->modelo               = strtoupper($request->input('modelo'));
            $equipo->marca_id             = $request->input('marca_id');
            $equipo->anio                 = $request->input('anio');
            $equipo->placa                = Libreria::getParam($request->input('placa'),'');
            $equipo->capacidad_carga      = $request->input('capacidad_carga');
            $equipo->contratista_id       = $request->input('contratista_id');
            //$equipo->concesionaria_id     =  $this->consecionariaActual();
            $equipo->concesionaria_id     = $request->input('concesionaria_id');

            $equipo->horas_min            = $request->input('horas_min');
            $equipo->precio               = $request->input('precio');
            $equipo->moneda               = $request->input('moneda');
            $equipo->unidad_id            = $request->input('unidad_id');
            $equipo->mantenimiento        = $request->input('mantenimiento');
            $equipo->horometraje_id       = $request->input('horometraje_id');
            $equipo->horometroinicial     = $request->input('horometroinicial');
            $equipo->decimales     = $request->input('decimales')+0;
            $equipo->estado               = $request->input('estado');
            $equipo->saltarhoro  = $request->input('saltarhoro');
            
            if($request->input('area_id') != 0){
                $equipo->area_id                  = $request->input('area_id');
            }

            $equipo->save();

            event( new UserHasCreatedOrDeleted($equipo->id,'equipo', $request->input('user_id'),'crear'));
        });
    }

    public function unidad(){
        $data =  Unidad::orderBy("unidad.id", "ASC")->get();
        return json_encode(array("unidad" => $data));
    }
    
    public function abastecimiento(){
        $data =  Abastecimiento::orderBy("abastecimiento.id", "ASC")->get();
        return json_encode(array("abastecimiento" => $data));
    }
    
    public function abastecimientocombustible(Request $request){
        $dat = array();
        // return json_encode(array("respuesta"=>"ERROR", "msg"=>"asdasd"));
        try {
            $error = DB::transaction(function () use ($request,  &$dat) {
                $abastecimiento = new AbastecimientoCombustible();
                $abastecimiento -> fecha_abastecimiento = $request -> input('fecha_abastecimiento');
                $abastecimiento -> grifo_id = $request->input('grifo_id');
                $abastecimiento -> user_id =$request -> input('user_id');
                $abastecimiento -> ua_id = $request -> input('ua_id');
                
                if($request -> input('equipo_id')){
                    $abastecimiento -> equipo_id = $request -> input('equipo_id');
                }
                if($request -> input('vehiculo_id')){
                    $abastecimiento -> vehiculo_id = $request -> input('vehiculo_id');
                }
                $abastecimiento -> qtdgl = $request -> input('qtdgl');
                $abastecimiento -> qtdl = $request -> input('qtdl');
                $abastecimiento -> km = $request -> input('km');
                $abastecimiento -> abastecimiento_dia = $request -> input('abastecimiento_dia');
                $abastecimiento -> motivo = $request -> input('motivo');
                $abastecimiento -> comprobante = $request -> input('comprobante');
                $abastecimiento -> numero_comprobante = $request -> input('numero_comprobante');
                $abastecimiento -> hora_inicio = $request -> input('hora_inicio');
                $abastecimiento -> hora_fin = $request -> input('hora_fin');
                $abastecimiento -> abastecimiento_id = $request -> input('abastecimiento_id');
                $abastecimiento -> tipocombustible_id = $request -> input('tipocombustible_id');
                $abastecimiento -> contratista_id = $request -> input('contratista_id');
                $abastecimiento -> save();
    
                event( new UserHasCreatedOrDeleted($abastecimiento->id,'abastecimiento_combustible', $request -> input('user_id'),'crear'));

                $dat[0] = array("respuesta" => "OK");
            });
        } catch (\Exception $e) {
            Log::error(json_encode(array("respuesta" => "ERROR", "msg" => $e->getMessage())));
            return json_encode(array("respuesta" => "ERROR", "msg" => $e->getMessage()));
        }
        return is_null($error) ? json_encode($dat[0]) : $error;
    }
    
    public function tipocombustible(){
        $data =  Tipocombustible::orderBy("tipocombustible.id", "ASC")->get();
        return json_encode(array("tipocombustible" => $data));
    }
    
    public function kilometraje(){
        $data =  Kilometraje::orderBy("kilometraje.id", "ASC")->get();
        return json_encode(array("kilometraje" => $data));
    }
    
    public function carroceria(){
        $data =  Carroceria::orderBy("carroceria.id", "ASC")->get();
        return json_encode(array("carroceria" => $data));
    }
    
    public function checklist(){
        $lista =  Checklistvehicular::where('sincronizado','like','N')->withTrashed()->orderBy("checklistvehicular.id", "ASC")->limit(10)->get();
        $data = array();
        foreach ($lista as $key => $value) {
            $dat = $value;
            $dat->detalle = DetalleChecklistvehicular::where('checklistvehicular_id','=',$value->id)->get();
            $dat->user_id = Bitacora::where('tabla','like','checklistvehicular')->where('registro_id','=',$value->id)->first()->user_id;
            $data[] = $dat;
            $val = Checklistvehicular::withTrashed()->find($value->id);
            $val->sincronizado = 'S';
            $val->save();
        }
        return json_encode($data);
    }

    public function control(){
        $lista =  Control::where('sincronizado','like','N')->orderBy("control.id", "ASC")->limit(10)->get();
        $data = array();
        foreach ($lista as $key => $value) {
            $dat = $value;
            $dat->detalle = Detallecontrol::where('control_id','=',$value->id)->get();
            $dat->user_id = Bitacora::where('tabla','like','control')->where('registro_id','=',$value->id)->first()->user_id;
            $data[] = $dat;
            $val = Control::find($value->id);
            $val->sincronizado = 'S';
                $val->save();
            }
            return json_encode($data);
        }

        public function agente(Request $request){
            $error = DB::transaction(function() use($request){

                $tipo_conductor = Tipouser::where('descripcion', 'AGENTE')->first();

                $vacio = Agente::where('dni','like',$request->input('dni'))->first();
                if(is_null($vacio)){

                    $user = new User();
                    $user->tipouser_id= $tipo_conductor->id;
                    $user->name = mb_strtoupper($request->input('name'), 'utf-8');
                    $user->username= $request->input('username');
                    $user->password= $request->input('password');
                    $user->concesionaria_id = $request->input('concesionaria_id');
                    $user->save();

                    event( new UserHasCreatedOrDeleted($user->id,'user', $request->input('user_id'),'crear'));

                    $userconcesionaria = new UserConcesionaria();
                    $userconcesionaria->user_id = $user->id;
                    $userconcesionaria->concesionaria_id = $request->input('concesionaria_id');
                    $userconcesionaria->save();

                    event( new UserHasCreatedOrDeleted($userconcesionaria->id,'userconcesionaria', $request->input('user_id'),'crear'));

                    $obj = new Agente();
                    $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
                    $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
                    $obj->telefono= mb_strtoupper($request->input('telefono'), 'utf-8');
                    $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
                    $obj->concesionaria_id = $request->input('concesionaria_id');
                    $obj->user_id = $user->id;
                    if($request->input('garita_id')!=""){
                        $obj->garita_id = json_encode($request->input('garita_id'));
                    }
                    $obj->save();
                    event( new UserHasCreatedOrDeleted($obj->id,'agente', $request->input('user_id'),'crear'));
                }else{
                    $obj = $vacio;
                    $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
                    $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
                    $obj->telefono= mb_strtoupper($request->input('telefono'), 'utf-8');
                    $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
                    $obj->concesionaria_id = $request->input('concesionaria_id');
                    $obj->user_id = $user->id;
                    if($request->input('garita_id')!=""){
                        $obj->garita_id = json_encode($request->input('garita_id'));
                    }
                    $obj->save();
                }
            
        });
        return is_null($error) ? "OK" : $error;
    }

    public function persona (Request $request){
        $error = DB::transaction(function() use($request){
            $obj = Persona::find($request->input('id'));
            if(is_null($obj)){
                $obj = new Persona();
                $obj->id = $request->input('id');
            }
            $obj->nombres= mb_strtoupper($request->input('nombres'), 'utf-8');
            $obj->apellidos= mb_strtoupper($request->input('apellidos'), 'utf-8');
            $obj->dni= mb_strtoupper($request->input('dni'), 'utf-8');
            $obj->otros= mb_strtoupper($request->input('otros'), 'utf-8');
            $obj->mostrardni= mb_strtoupper($request->input('mostrardni'), 'utf-8');
            $obj->contratista = $request->input('contratista');
            $obj->contratista_id = $request->input('contratista_id');
            $obj->empresa_id = $request->input('empresa_id');
            $obj->correo = $request->input('correo');
            $obj->ua_id = $request->input('ua_id');
            
            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'persona', $request->input('user_id'),'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminarPersona(Request $request){
        $error = DB::transaction(function() use($request){    
            $obj = Persona::find($id);
            event( new UserHasCreatedOrDeleted($obj->id,'persona', $request->input('user_id'),'eliminar'));
            $obj->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function empresa (Request $request){
        $error = DB::transaction(function() use($request){

            $obj = new Empresa();
            $obj->razonsocial= strtoupper($request->input('razonsocial'));
            $obj->ruc= strtoupper($request->input('ruc'));
            $obj->user_id = 1;

            $obj->save();
            event( new UserHasCreatedOrDeleted($obj->id,'empresa', $request->input('user_id'),'crear'));
        });
        return is_null($error) ? "OK" : $error;
    }

    public function contratista (Request $request){
        $error = DB::transaction(function() use($request){
            $contratista = new Contratista();
            $contratista->razonsocial= strtoupper($request->input('razonsocial'));
            $contratista->ruc= strtoupper($request->input('ruc'));
            $contratista->propietario= strtoupper($request->input('propietario'));
            $contratista->email= strtoupper($request->input('email'));
            $contratista->telefono= strtoupper($request->input('telefono'));
            $contratista->propietario2= strtoupper($request->input('propietario2'));
            $contratista->email2= strtoupper($request->input('email2'));
            $contratista->telefono2= strtoupper($request->input('telefono2'));
            $contratista->propietario3= strtoupper($request->input('propietario3'));
            $contratista->email3= strtoupper($request->input('email3'));
            $contratista->telefono3= strtoupper($request->input('telefono3'));
            $contratista->save();
            event( new UserHasCreatedOrDeleted($contratista->id,'contratista', auth()->user()->id,'crear'));
            
        });
        return is_null($error) ? "OK" : $error;
    }
}
