<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use SoftDeletes;
    protected $table = 'zona';
    protected $dates = ['deleted_at'];
    protected $fillable = ['nombre'];

    public function scopegetAll($query) {
        return $query->select('id', 'nombre')->orderBy('nombre', 'ASC')->get();
    }

}
