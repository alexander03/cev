<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regmanvehdocument extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'regmanvehdocument';

    public function regmanveh() {
        return $this->belongsTo('App\regmanveh');
    }

    public function scopegetlist($query, $regmanveh_id, $tipo) {
        return $query->where('regmanveh_id', $regmanveh_id)
            ->where(function($subquery) use ($tipo) {
                if($tipo !== 'all') $subquery->where('tipo', $tipo);
            })
            ->orderBy('tipo', 'DESC');
    }
}
