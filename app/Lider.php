<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lider extends Model
{	
    use LegacyDateSerialization;
	use SoftDeletes;
    protected $table = 'lider';

    protected $fillable = [ 'nombres', 'correo'];

}
