<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbastecimientoCombustible extends Model{

    use SoftDeletes;
    protected $table = 'abastecimiento_combustible';
    protected $dates = [ 'deleted_at' ];
  
    protected $fillable = [ 'fecha_abastecimiento', 'grifo_id', 'tipo_combustible', 
                            'conductor_id', 'conductor_fake', 'ua_id', 'equipo_id', 'qtdgl', 'qtdl',
                            'km', 'abastecimiento_dia','contratista_id','contratistaabastecimiento_id','concesionaria_id' ];
    
    public function grifo(){

        return $this->belongsTo(Grifo::class, 'grifo_id');		
    }

    // public function conductor(){

    //     return $this->belongsTo(Conductor::class, 'conductor_id');		
    // }

    public function usuario(){

        return $this->belongsTo(User::class, 'user_id');		
    }
    
    public function ua(){

        return $this->belongsTo(Ua::class, 'ua_id');		
    }

    public function equipo(){

        return $this->belongsTo(Equipo::class, 'equipo_id');		
    }

    public function vehiculo(){

        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function abastecimiento(){

        return $this->belongsTo(Abastecimiento::class, 'abastecimiento_id');
    }

    public function tipoCombustible(){

        return $this->belongsTo(Tipocombustible::class, 'tipocombustible_id');
    }
    
    public function contratista(){

        return $this->belongsTo(Contratista::class, 'contratista_id');		
    }
}
