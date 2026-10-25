<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piepagina extends Model
{
    use SoftDeletes;
    protected $table = 'piepagina';
    protected $dates = ['deleted_at'];
    
}