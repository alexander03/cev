<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion extends Model
{
    use LegacyDateSerialization;
	

	protected $table = 'configuracion';
    use SoftDeletes;
    
    protected $fillable = ['nombre','descripcion','valor'];
}
