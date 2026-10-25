<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horometro extends Model
{
    use LegacyDateSerialization;
    protected $table = 'horometro';
    use SoftDeletes;
    
    protected $fillable = ['fecha','inicio','fin','grifo_id'];

    public function grifo() {
        return $this->belongsTo('App\Grifo', 'grifo_id');
    }

    public function scopegetFilter($query, $fechainicio, $fechafin, $grifo_id) {
        return $query
            ->where(function($subquery) use ($fechainicio) {
                $subquery->where('fecha','>=',$fechainicio);
            })
            ->where(function($subquery) use ($fechafin) {
                $subquery->where('fecha','<=',$fechafin);
            })
            ->where(function($subquery) use ($grifo_id) {
                if($grifo_id!="0"){
                    $subquery->where('grifo_id','=',$grifo_id);
                }
            })
            ->orderBy('fecha', 'desc');
    }
}
