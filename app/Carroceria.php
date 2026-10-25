<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Carroceria extends Model
{
    use SoftDeletes;
    protected $table = 'carroceria';
    protected $dates = ['deleted_at'];
}
