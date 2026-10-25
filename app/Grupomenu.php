<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupomenu extends Model
{
    use LegacyDateSerialization;
	 use SoftDeletes;
    protected $table = 'grupomenu';

    public function opcionesmenu()
    {
        return $this->hasMany('App\Opcionmenu', 'grupomenu_id');
    }
    
}
