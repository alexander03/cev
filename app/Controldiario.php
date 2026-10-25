<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Controldiario extends Model
{
    use SoftDeletes;
    protected $table = 'controldiario';
    protected $dates = ['deleted_at'];

    public function tipohora()
    {
        return $this->belongsTo('App\Tipohora');
    }
    public function equipo()
    {
        return $this->belongsTo('App\Equipo');
    }

    public function ua()
    {
        return $this->belongsTo('App\Ua');
    }

    public function ua_origen()
    {
        return $this->belongsTo('App\Ua','uaorigen_id','id');
    }

    public function ua_destino()
    {
        return $this->belongsTo('App\Ua','uadestino_id','id');
    }
}
