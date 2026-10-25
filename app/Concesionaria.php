<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concesionaria extends Model
{
    protected $table = 'concesionaria';
    //use SoftDeletes;
    protected $fillable = ['ruc', 'razonsocial', 'abreviatura'];

    public function scopegetAll($query) {
        return $query->select('id', 'ruc', 'razonsocial', 'abreviatura')->orderBy('razonsocial', 'ASC')->get();
    }

    public function scopegetConcesionariaActual($query) {
        $concesionariaAct = $query->join('users','users.concesionaria_id','=','concesionaria.id')
                            ->where('users.id','=',auth()->user()->id)
                            ->select('concesionaria.id','concesionaria.razonsocial')->first();
        return $concesionariaAct;
    }

}