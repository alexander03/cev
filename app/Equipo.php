<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'equipo';


    
	/*
	public function ua()
    {
        return $this->belongsTo('App\Ua');
    }
    */

    public function marca()
    {
        return $this->belongsTo('App\Brand');
    }
	
	public function contratista()
    {
        return $this->belongsTo('App\Contratista','contratista_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    public function ua()
    {
        return $this->belongsTo('App\Ua');
    } 

    public function uatrabajo()
    {
        return $this->belongsTo('App\Ua','uatrabajo_id');
    }

    public function unidad()
    {
        return $this->belongsTo('App\Unidad');
    }    
    
    public function controlesdiarios()
    {
        return $this->hasMany('App\Controldiario')->where('controldiario.deleted_at',null);
    }

    public function horometraje2()
    {
        return $this->belongsTo('App\Horometraje','horometraje_id');
    }

    public function carroceria()
    {
        return $this->belongsTo('App\Carroceria','carroceria_id');
    }

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }

}
