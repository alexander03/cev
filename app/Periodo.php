<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periodo extends Model
{
    protected $table = 'periodo'; 
    use SoftDeletes;
    
}
