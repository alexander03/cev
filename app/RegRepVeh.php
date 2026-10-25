<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Bitacora;
use App\User;

class RegRepVeh extends Model
{
    use SoftDeletes;
    protected $table = 'regrepveh';
    protected $fillable = ['id','concesionaria_id','ordencompra', 'cliente','estado','ua_id','kmman','kminicial','kmfinal','fechaentrada','fechasalida','tipomantenimiento','telefono','equipo_id','checklistvehicular_id','fecharegistro','fechamantenimiento','taller_id','diagnostico_id','numero','user_id'];
    protected $dates = ['deleted_at'];

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo','ua_id');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo','equipo_id');
    }

    public function taller()
    {
        return $this->belongsTo('App\Taller','taller_id');
    }

    public function diagnostico()
    {
        return $this->belongsTo('App\Diagnostico','diagnostico_id');
    }

    public function user($id)
    {
        $user = Bitacora::where('accion','like','crear')->where('tabla','like','regrepveh')->where('registro_id','=',$id)->first()->user;
        return $user;
        //return $this->belongsTo('App\User','user_id');
    }
}
