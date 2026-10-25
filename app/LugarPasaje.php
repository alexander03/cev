<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LugarPasaje extends Model
{
    use LegacyDateSerialization;
    protected $table = 'lugarpasaje';
    use SoftDeletes;
    
    protected $fillable = ['nombre'];

}