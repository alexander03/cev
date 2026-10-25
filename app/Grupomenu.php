<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupomenu extends Model
{
	 use SoftDeletes;
    protected $table = 'grupomenu';
    protected $dates = ['deleted_at'];

    public function opcionesmenu()
    {
        return $this->hasMany('App\Opcionmenu', 'grupomenu_id');
    }
    
}
