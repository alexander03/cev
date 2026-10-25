<?php

namespace App;

use App\Traits\LegacyDateSerialization;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class DetalleChecklistvehicular extends Model
{
    use LegacyDateSerialization;
    protected $table = 'detallechecklistvehicular';
    use SoftDeletes;
    protected $fillable = [
        'checklistvehicular_id',  'km_inicial',
        'km_final',
        'hora_inicio', 'hora_fin','justificacion','ua_id'
    ];
    
    public function ua()
    {
        return $this->belongsTo('App\Ua');
    }

    public function checklistvehicular()
    {
        return $this->belongsTo('App\Checklistvehicular','checklistvehicular_id');
    }

}
