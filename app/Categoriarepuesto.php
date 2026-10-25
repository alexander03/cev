<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Categoriarepuesto extends Model
{
    use SoftDeletes;
    protected $table = 'categoriarepuesto';
    protected $dates = ['deleted_at'];
}
