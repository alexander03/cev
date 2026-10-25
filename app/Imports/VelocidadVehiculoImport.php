<?php

namespace App\Imports;

use App\Concesionaria;
use App\Vehiculo;
use App\Zona;
use App\VelocidadVehiculo;
use Carbon\Carbon;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Excel;

class VelocidadVehiculoImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        //ESCAPAR FILA
        if($row[0] === 'Alias') return null; 
        //VALIDACIONES DE CAMPOS VACÍOS
        if($row[0] === null || $row[1] === null 
            || $row[2] === null || $row[3] === null) return null;
        $fecha = $row[1];
        //$fecha = substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2).substr($fecha,10,10);
        $rumbo = $row[3];
        $latitud = $row[4];
        $longitud = $row[5];
        try{
            $fecha = Carbon::instance(Date::excelToDateTimeObject($row[1]))->format('Y-m-d H:i:s');
        }catch(ErrorException $ex){
            $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $row[1]);
        }
        $zona = Zona::where('nombre','like',trim($row[6]))->first();
        if(is_null($zona)){
            $zona = new Zona();
            $zona->nombre = trim($row[6]);
            $zona->save();
        }
        $vehiculo = Vehiculo::where('placa','like',$row[0])->first();
        if(!is_null($vehiculo)){
            $velocidadvehiculo = VelocidadVehiculo::where('vehiculo_id','=',$vehiculo->id)
                            ->where('fecha','=',$fecha)
                            ->where('velocidad','=',$row[2])
                            ->where('mapa','like',$row[26])
                            ->where('concesionaria_id','=',$vehiculo->concesionaria_id)
                            ->first();
            if(is_null($velocidadvehiculo)){
                return new VelocidadVehiculo([
                    'vehiculo_id' => $vehiculo->id,
                    'fecha' => $fecha,
                    'velocidad' => $row[2],
                    'localizacion' => $row[7],
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'rumbo' => $rumbo,
                    'zona_id' => $zona->id,
                    'mapa' => trim($row[26]),
                    'calle' => $row[8],
                    'concesionaria_id' => $vehiculo->concesionaria_id
                ]);
            }else{
                return $velocidadvehiculo;
            }
        }
    }

    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}
