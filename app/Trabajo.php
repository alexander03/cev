<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    use LegacyDateSerialization;
	

	protected $table = 'trabajo';
    //use SoftDeletes;

    protected $fillable = ['descripcion'];
}
