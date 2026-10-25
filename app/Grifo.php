<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grifo extends Model
{	
	use SoftDeletes;
    protected $table = 'grifo';
    protected $dates = ['deleted_at'];

    protected $fillable = ['descripcion'];

    public function abastecimiento()
    {
        return $this->belongsTo('App\Abastecimiento');
    }
}
