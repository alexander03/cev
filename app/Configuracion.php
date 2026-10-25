<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion extends Model
{
	

	protected $table = 'configuracion';
    use SoftDeletes;
    
    protected $fillable = ['nombre','descripcion','valor'];
}
