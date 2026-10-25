<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipouser extends Model
{
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'tipouser';

    public function accesos(){
    	return $this->hasMany('App\Acceso');
    }

    public function opcionesmenu(){
    	return $this->belongsToMany('App\Opcionmenu','acceso')->where('acceso.deleted_at',null)->orderBy('orden','asc');
    }

    public function scopegetAll($query) {
        return $query->select('id', 'descripcion')->orderBy('descripcion', 'ASC')->get();
    }

}
