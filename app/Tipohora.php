<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipohora extends Model
{	
	use SoftDeletes;
    protected $table = 'tipohora';
    protected $dates = ['deleted_at'];

//    protected $fillable = [ 'codigo', 'descripcion'];

}
