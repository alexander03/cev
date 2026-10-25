<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grifo extends Model
{	
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'grifo';

    protected $fillable = ['descripcion'];

    public function abastecimiento()
    {
        return $this->belongsTo('App\Abastecimiento');
    }
}
