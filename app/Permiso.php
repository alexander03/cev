<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use SoftDeletes;
    protected $table = 'permiso';
    protected $dates = ['deleted_at'];

    public function opcionmenu(){
    	return $this->belongsTo('App\Opcionmenu');
    }
    public function tipouser(){
    	return $this->belongsTo('App\Tipouser');
    }
}
