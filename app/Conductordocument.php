<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conductordocument extends Model
{
    use SoftDeletes;
    protected $table = 'conductordocument';

    public function conductor() {
        return $this->belongsTo('App\Conductor');
    }

    public function scopegetlist($query, $conductor_id, $tipo) {
        return $query->where('conductor_id', $conductor_id)
            ->where(function($subquery) use ($tipo) {
                if($tipo !== 'all') $subquery->where('tipo', $tipo);
            })
            ->orderBy('tipo', 'DESC');
    }
}
