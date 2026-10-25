<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipohora extends Model
{	
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'tipohora';

//    protected $fillable = [ 'codigo', 'descripcion'];

}
