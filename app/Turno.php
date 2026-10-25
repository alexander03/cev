<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
	protected $table = 'turno';
    use SoftDeletes;
    
    protected $fillable = ['nombre'];
}
