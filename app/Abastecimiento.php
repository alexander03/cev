<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abastecimiento extends Model
{
    use SoftDeletes;
    protected $table = 'abastecimiento';
    protected $dates = ['deleted_at'];
}
