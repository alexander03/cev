<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detallepreciocombustible extends Model
{
    use SoftDeletes;
    protected $table = 'detallepreciocombustible';
    protected $dates = ['deleted_at'];

    public function preciocombustible()
    {
        return $this->belongsTo('App\Preciocombustible');
    }

    public function tipocombustible()
    {
        return $this->belongsTo('App\Tipocombustible');
    }

}
