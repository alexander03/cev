<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conductorconcesionaria extends Model
{
    use LegacyDateSerialization;
    protected $table = 'conductorconcesionaria';
    // use SoftDeletes;
    protected $fillable = ['conductor_id', 'concesionaria_id'];
}
