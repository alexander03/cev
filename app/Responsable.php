<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responsable extends Model
{
    use SoftDeletes;
    protected $table = 'responsable';
    protected $dates = ['deleted_at'];
}
