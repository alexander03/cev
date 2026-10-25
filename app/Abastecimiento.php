<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abastecimiento extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'abastecimiento';
}
