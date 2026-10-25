<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taller extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'taller';
    protected $fillable = ['ruc','razonsocial','contacto'];

    public function scopegetAll($query) {
        return $query->select('id', 'ruc','razonsocial','contacto')->orderBy('ruc', 'ASC')->get();
    }

}
