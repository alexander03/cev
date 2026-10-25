<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kilometraje extends Model
{
    use SoftDeletes;
    protected $table = 'kilometraje';
    protected $fillable = ['descripcion', 'limite_inf', 'limite_sup'];

    public function scopegetFilter($query, $estado, $filter) {
        return $query
            ->where(function($subquery) use ($estado) {
                if($estado === 'activos') $subquery->whereNull('deleted_at');
                else if($estado === 'desactivados') $subquery->whereNotNull('deleted_at');
            })
            ->where('descripcion', 'LIKE', '%'.strtoupper($filter).'%')
            ->orderBy('descripcion', 'ASC')->withTrashed();
    }
}
