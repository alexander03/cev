<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Carroceria extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'carroceria';
}
