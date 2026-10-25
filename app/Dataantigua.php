<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dataantigua extends Model
{
    protected $table = 'dataantigua'; 
    use SoftDeletes;
    
    protected $fillable = ['mes','equipo_id','vehiculo_id','valor','valor2','costo','tipo'];
}
