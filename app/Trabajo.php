<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
	

	protected $table = 'trabajo';
    //use SoftDeletes;

    protected $fillable = ['descripcion'];
}
