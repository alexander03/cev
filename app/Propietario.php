<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propietario extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'propietarios';
    protected $fillable = ['descripcion', 'ua_id', 'fecha_contrato', 
                            'fecha_llegada', 'fecha_salida', 'hra', 'hrb', 'hrc', 'km', 
                            'observacion', 'status', 'ubicacion'];

    public function ua(){

        return $this->belongsTo(Ua::class, 'ua_id');		
    }
}