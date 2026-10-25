<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculodocument extends Model
{
    use SoftDeletes;
    protected $table = 'vehiculodocument';
    protected $dates = ['deleted_at'];

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo');
    }

    public function user()
    {
        return $this->belongsTo('App\User','usercomentario_id');
    }
}
