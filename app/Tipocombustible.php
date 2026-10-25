<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tipocombustible extends Model
{
 	use SoftDeletes;
 	protected $table = 'tipocombustible';
    protected $dates = ['deleted_at'];
}
