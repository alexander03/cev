<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Diagnostico extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'diagnostico';

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo','vehiculo_id');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo','equipo_id');
    }

    public function taller()
    {
        return $this->belongsTo('App\Taller','taller_id');
    }

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }

    public function checklistvehicular()
    {
        return $this->belongsTo('App\Checklistvehicular','checklistvehicular_id');
    }

    public function scopeNumeroSigue($query)
    {
        $rs = $query->where('fecharegistro','<=',date("Y")."-12-31")->where('fecharegistro','>=',date("Y")."-01-01")->select(DB::raw("max((CASE WHEN numero IS NULL THEN 0 ELSE convert(substr(numero,7,4),SIGNED integer) END)*1) AS maximo"))->first();
        
        return "D".date("Y")."-".str_pad($rs->maximo + 1, 4, '0', STR_PAD_LEFT);
    }
}
