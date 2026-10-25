<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
	

	protected $table = 'area';
    //use SoftDeletes;
    
    protected $fillable = ['descripcion','nivel','areapadre_id','responsable','correo'];
}
