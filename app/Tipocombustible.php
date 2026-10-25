<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tipocombustible extends Model
{
    use LegacyDateSerialization;
 	use SoftDeletes;
 	protected $table = 'tipocombustible';
}
