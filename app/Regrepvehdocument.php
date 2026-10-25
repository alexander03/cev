<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regrepvehdocument extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'regrepvehdocument';

    public function regrepveh() {
        return $this->belongsTo('App\Regrepveh');
    }

    public function scopegetlist($query, $regrepveh_id, $tipo) {
        return $query->where('regrepveh_id', $regrepveh_id)
            ->where(function($subquery) use ($tipo) {
                if($tipo !== 'all') $subquery->where('tipo', $tipo);
            })
            ->orderBy('tipo', 'DESC');
    }
}
