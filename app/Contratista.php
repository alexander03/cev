<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contratista extends Model
{
    protected $table = 'contratista';
    use SoftDeletes;
    protected $fillable = ['ruc', 'razonsocial','propietario','email','telefono'];

    public function scopegetAll($query) {
        return $query->select('id', 'razonsocial')->orderBy('razonsocial', 'ASC')->get();
    }
}
