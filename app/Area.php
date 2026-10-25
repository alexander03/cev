<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use LegacyDateSerialization;
	

	protected $table = 'area';
    //use SoftDeletes;
    
    protected $fillable = ['descripcion','nivel','areapadre_id','responsable','correo'];
}
