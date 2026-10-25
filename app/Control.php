<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Control extends Model
{
    protected $table = 'control';
    protected $dates = ['deleted_at'];
    use SoftDeletes;

    public function garita()
    {
        return $this->belongsTo('App\Garita');
    }

    public function agente()
    {
        return $this->belongsTo('App\Agente')->withTrashed();
    }

    public function origen2()
    {
        return $this->belongsTo('App\Lugar','origen_id');
    }

    public function destino2()
    {
        return $this->belongsTo('App\Lugar','destino_id');
    }

    public function turno()
    {
        return $this->belongsTo('App\Turno');
    }

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }
}
