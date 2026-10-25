<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipodocument extends Model
{
    use SoftDeletes;
    protected $table = 'equipodocument';
    protected $dates = ['deleted_at'];

    public function equipo()
    {
        return $this->belongsTo('App\Equipo');
    }

    public function user()
    {
        return $this->belongsTo('App\User','usercomentario_id');
    }
}
