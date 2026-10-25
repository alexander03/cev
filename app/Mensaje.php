<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{	
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'mensaje';

    protected $fillable = ['descripcion','fechainicio','fechafin','tipuser_id'];

    public function tipouser()
    {
        return $this->belongsTo('App\Tipouser');
    }
}
