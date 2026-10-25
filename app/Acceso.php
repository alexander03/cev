<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acceso extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'acceso';

    public function opcionmenu(){
    	return $this->belongsTo('App\Opcionmenu');
    }
    public function tipouser(){
    	return $this->belongsTo('App\Tipouser');
    }


}
