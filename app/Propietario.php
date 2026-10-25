<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propietario extends Model
{
    use SoftDeletes;
    protected $table = 'propietarios';
    protected $dates = ['deleted_at'];
    protected $fillable = ['descripcion', 'ua_id', 'fecha_contrato', 
                            'fecha_llegada', 'fecha_salida', 'hra', 'hrb', 'hrc', 'km', 
                            'observacion', 'status', 'ubicacion'];

    public function ua(){

        return $this->belongsTo(Ua::class, 'ua_id');		
    }
}