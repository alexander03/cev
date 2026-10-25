<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    protected $table = 'marca';
    use SoftDeletes;
    
    protected $fillable = ['descripcion'];

    public function scopegetFilter($query, $estado, $filter) {
        return $query
            ->where(function($subquery) use ($estado) {
                if($estado === 'activos') $subquery->whereNull('deleted_at');
                elseif($estado === 'desactivados') $subquery->whereNotNull('deleted_at');
            })
            ->where('descripcion', 'LIKE', '%'.strtoupper($filter).'%')
            ->orderBy('descripcion', 'ASC')->withTrashed();
    }
}
