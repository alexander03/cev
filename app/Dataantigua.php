<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dataantigua extends Model
{
    use LegacyDateSerialization;
    protected $table = 'dataantigua'; 
    use SoftDeletes;
    
    protected $fillable = ['mes','equipo_id','vehiculo_id','valor','valor2','costo','tipo'];
}
