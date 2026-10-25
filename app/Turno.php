<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
    use LegacyDateSerialization;
	protected $table = 'turno';
    use SoftDeletes;
    
    protected $fillable = ['nombre'];
}
