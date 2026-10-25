<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Controldiariovehiculo extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'controldiariovehiculo';

    public function tipohora()
    {
        return $this->belongsTo('App\Tipohora');
    }
    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo');
    }

    public function ua()
    {
        return $this->belongsTo('App\Ua');
    }

    public function ua_origen()
    {
        return $this->belongsTo('App\Ua','id','uaorigen_id');
    }

    public function ua_destino()
    {
        return $this->belongsTo('App\Ua','id','uadestino_id');
    }
}
