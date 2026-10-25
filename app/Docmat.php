<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Docmat extends Model
{
    protected $table = 'docmat';
    protected $dates = ['deleted_at'];
    use SoftDeletes;

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo','vehiculo_id');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo','equipo_id');
    }

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }

    public function taller()
    {
        return $this->belongsTo('App\Taller','taller_id');
    }
}
