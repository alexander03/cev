<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taller extends Model
{
    use SoftDeletes;
    protected $table = 'taller';
    protected $dates = ['deleted_at'];
    protected $fillable = ['ruc','razonsocial','contacto'];

    public function scopegetAll($query) {
        return $query->select('id', 'ruc','razonsocial','contacto')->orderBy('ruc', 'ASC')->get();
    }

}
