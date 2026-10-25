<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opcionmenu extends Model
{
	use SoftDeletes;
    protected $table = 'opcionmenu';
    protected $dates = ['deleted_at'];
    
    public function grupomenu()
    {
        return $this->belongsTo('App\Grupomenu', 'grupomenu_id');
    }
}
