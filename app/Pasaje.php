<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasaje extends Model
{
    use LegacyDateSerialization;
    protected $table = 'pasaje';
    use SoftDeletes;

    public function origen()
    {
        return $this->belongsTo('App\LugarPasaje','origen_id');
    }

    public function destino()
    {
        return $this->belongsTo('App\LugarPasaje','destino_id');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona');
    }

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }

    public function contratista()
    {
        return $this->belongsTo('App\Contratista');
    }

    public function empresa2()
    {
        return $this->belongsTo('App\Empresa');
    }

    public function ua()
    {
        return $this->belongsTo('App\Ua');
    }
}
