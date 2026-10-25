<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acceso extends Model
{
    use SoftDeletes;
    protected $table = 'acceso';
    protected $dates = ['deleted_at'];

    public function opcionmenu(){
    	return $this->belongsTo('App\Opcionmenu');
    }
    public function tipouser(){
    	return $this->belongsTo('App\Tipouser');
    }


}
