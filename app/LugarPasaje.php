<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LugarPasaje extends Model
{
    protected $table = 'lugarpasaje';
    use SoftDeletes;
    
    protected $fillable = ['nombre'];

}