<?php

namespace App;

use App\Traits\LegacyDateSerialization;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Checklistvehicular extends Model
{
    use LegacyDateSerialization;
    protected $table = 'checklistvehicular';
    use SoftDeletes;
    protected $fillable = [
        'fecha_registro', 'equipo_id','vehiculo_id', 'k_inicial',
        'k_final', 'lider_area', 'conductor_id', 'sistema_electronico',
        'sistema_mecanico', 'accesorios', 'documentos', 'observaciones',
        'hora_inicio', 'hora_fin','justificacion','uatrabajo_id'
    ];

    protected $casts = [
        'sistema_electrico' => 'array',
        'sistema_mecanico' => 'array',
        'accesorios' => 'array',
        'documentos' => 'array',
    ];

    public function scopegetFilter($query, $fecha_registro_inicial, $fecha_registro_final,$ua,$modelo,$conductor,$lider) {

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $query->leftjoin('equipo', 'equipo.id', '=', 'checklistvehicular.equipo_id')
            ->leftjoin('vehiculo', 'vehiculo.id', 'checklistvehicular.vehiculo_id')
            ->leftjoin('ua','ua.id','vehiculo.ua_id')
            ->leftjoin('ua as uatrabajo','uatrabajo.id','checklistvehicular.uatrabajo_id')
            ->leftjoin('ua as ua2','ua2.id','equipo.ua_id')
            ->join('conductor', 'conductor.id', 'checklistvehicular.conductor_id')
            ->leftjoin('bitacora','bitacora.registro_id','checklistvehicular.id')
            ->leftjoin('users','users.id','bitacora.user_id')
            ->where('bitacora.accion','like','crear')
            ->where('bitacora.tabla','like','checklistvehicular')
            ->where(function($subquery) use ($fecha_registro_inicial, $fecha_registro_final) {
                if ( $fecha_registro_inicial !== null ) $subquery->where('checklistvehicular.fecha_registro', '>=', $fecha_registro_inicial);
                if ( $fecha_registro_final !== null ) $subquery->where('checklistvehicular.fecha_registro', '<=', $fecha_registro_final);
            })
            /*->where(function($subquery) use ($idConcAct) {
                $subquery->where('checklistvehicular.concesionaria_id', $idConcAct);
            })*/
            ->where(function($subquery) use ($ua) {
                $subquery->where('ua.codigo', 'like','%'.$ua.'%')->orWhere('ua2.codigo','like','%'.$ua.'%');
            })
            ->where(function($subquery) use ($modelo) {
                if($modelo!="") $subquery->where('equipo.placa', 'like','%'.$modelo.'%')->orWhere('vehiculo.placa','like','%'.$modelo.'%')->orWhere('vehiculo.modelo','like','%'.$modelo.'%');
            })
            ->where(function($subquery) use ($conductor) {
                if($conductor!="") $subquery->where(DB::raw('concat(conductor.nombres,\' \',conductor.apellidos)'), 'like','%'.$conductor.'%');
            })
            ->where(function($subquery) use ($lider) {
                if($lider!="") $subquery->where('checklistvehicular.lider_area', 'like','%'.$lider.'%');
            })
            ->orderBy('checklistvehicular.fecha_registro', 'DESC')
            ->select('checklistvehicular.id', 'checklistvehicular.fecha_registro','checklistvehicular.incidentes','checklistvehicular.observaciones', 'equipo.placa as equipo_placa', 'equipo.descripcion as equipo_descripcion', 'vehiculo.placa as vehiculo_placa', 'vehiculo.modelo as vehiculo_modelo', 'checklistvehicular.k_inicial', 'checklistvehicular.k_final', 'checklistvehicular.lider_area', 'conductor.nombres as conductor_nombres', 'conductor.apellidos as conductor_apellidos', 'checklistvehicular.sistema_electrico',DB::raw('case when checklistvehicular.equipo_id then ua2.codigo else ua.codigo end as vehiculo_ua'),'checklistvehicular.hora_fin','checklistvehicular.hora_inicio','users.name as usuario',DB::raw('case when checklistvehicular.uatrabajo_id is null or checklistvehicular.uatrabajo_id =0 then null else uatrabajo.codigo end as vehiculo_uatrabajo'),'checklistvehicular.justificacion','vehiculo.saltarkm','checklistvehicular.updated_at');
    }

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo');
    }
    
    public function ua()
    {
        return $this->belongsTo('App\Ua');
    }

    public function uatrabajo()
    {
        return $this->belongsTo('App\Ua','uatrabajo_id');
    }

    public function conductor()
    {
        return $this->belongsTo('App\Conductor');
    }

}
