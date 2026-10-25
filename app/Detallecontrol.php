<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detallecontrol extends Model
{
    use LegacyDateSerialization;
    protected $table = 'detallecontrol';
    use SoftDeletes;

    public function control()
    {
        return $this->belongsTo('App\Control');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona');
    }

    public function vehiculo2()
    {
        return $this->belongsTo('App\Vehiculo');
    }

    public function contratista()
    {
        return $this->belongsTo('App\Contratista')->withTrashed();
    }

    public function empresa2()
    {
        return $this->belongsTo('App\Empresa');
    }
}
