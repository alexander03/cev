<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lider extends Model
{	
	use SoftDeletes;
    protected $table = 'lider';
    protected $dates = ['deleted_at'];

    protected $fillable = [ 'nombres', 'correo'];

}
