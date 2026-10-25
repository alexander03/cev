<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VelocidadVehiculo extends Model{
    use SoftDeletes;
    protected $table = 'velocidadvehiculo';
    protected $dates = ['deleted_at'];
    protected $fillable = ['vehiculo_id','concesionaria_id','fecha','zona_id','mapa','velocidad','localizacion','rumbo','latitud','longitud','calle'];
            
    public function vehiculo(){

        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');		
    }

    public function concesionaria(){
        
        return $this->belongsTo(Concesionaria::class, 'concesionaria_id');
    }

    public function zona(){

        return $this->belongsTo(Zona::class, 'zona_id');
    }
}
