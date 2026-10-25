<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'unidad';
    protected $fillable = ['descripcion'];

    public function scopegetAll($query) {
        return $query->select('id', 'descripcion')->orderBy('descripcion', 'ASC')->get();
    }

}
