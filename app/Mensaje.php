<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{	
	use SoftDeletes;
    protected $table = 'mensaje';
    protected $dates = ['deleted_at'];

    protected $fillable = ['descripcion','fechainicio','fechafin','tipuser_id'];

    public function tipouser()
    {
        return $this->belongsTo('App\Tipouser');
    }
}
