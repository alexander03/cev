<?php

namespace App\Imports;

use App\Vehiculo;
use App\Ua;
use App\Equipo;
use App\Dataantigua;
use Carbon\Carbon;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Excel;

class DataantiguaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        //ESCAPAR FILA
        if($row[0] === 'PLACA') return null; 

        $vehiculo = Vehiculo::where('placa','like',$row[0])->first();
        if(is_null($vehiculo)){
            $vehiculo_id =0;
        }else{
            $vehiculo_id = $vehiculo->id;
        }

        $equipo = Equipo::where('placa','like',$row[0])->first();
        if(is_null($equipo)){
            $equipo_id =0;
        }else{
            $equipo_id = $equipo->id;
        }
        $valor = $row[1];
        $valor2 = $row[2];
        $costo = $row[3];
        $tipo = $row[5];

        try{
            $fecha = Carbon::instance(Date::excelToDateTimeObject($row[4]))->format('Y-m-d');
        }catch(ErrorException $ex){
            $fecha = Carbon::createFromFormat('Y-m-d', $row[4]);
        }
        
        return new Dataantigua([
            'equipo_id' => $equipo_id,
            'vehiculo_id' => $vehiculo_id,
            'valor' => $valor,
            'valor2' => $valor2,
            'mes' => $fecha,
            'costo' => $costo,
            'tipo'  => $tipo,
        ]);
    }

}
