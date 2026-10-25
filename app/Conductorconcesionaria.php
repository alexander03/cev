<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conductorconcesionaria extends Model
{
    protected $table = 'conductorconcesionaria';
    // use SoftDeletes;
    protected $fillable = ['conductor_id', 'concesionaria_id'];
}
