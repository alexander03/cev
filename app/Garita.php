<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garita extends Model
{
    protected $table = 'garita';
    use SoftDeletes;
    
    protected $fillable = ['nombre','ubicacion','concesionaria_id','mostrar'];

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }
}
