<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opcionmenu extends Model
{
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'opcionmenu';
    
    public function grupomenu()
    {
        return $this->belongsTo('App\Grupomenu', 'grupomenu_id');
    }
}
