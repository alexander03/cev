<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'zona';
    protected $fillable = ['nombre'];

    public function scopegetAll($query) {
        return $query->select('id', 'nombre')->orderBy('nombre', 'ASC')->get();
    }

}
