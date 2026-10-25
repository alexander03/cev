<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ua extends Model{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'ua';
    protected $fillable = ['codigo', 'descripcion', 'habilitada', 
                            'ua_padre_id', 'concesionaria_id',
                            'fecha_inicio', 'fecha_fin', 'padre', 'responsable_id'];
            
    public function unidad(){

        return $this->belongsTo(Unidad::class, 'unidad_id');		
    }

    public function concesionaria(){

        return $this->belongsTo(Concesionaria::class, 'concesionaria_id');        
    }

    public function uaPadre($id){

        return DB::select('select codigo, descripcion from ua where id = ?', [ $id ]);
    }
    
    public function uaHija($id){

        return DB::select('select codigo, descripcion from ua where ua_padre_id = ?', [ $id ]);
    }

    public function controlesdiarios()
    {
        return $this->hasMany('App\Controldiario')->where('controldiario.deleted_at',null);
    }

    public function equipos()
    {
        return $this->belongsToMany('App\Equipo','controldiario','ua_id','equipo_id')->whereNull('controldiario.deleted_at');
    }

    public function responsable(){
        
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }
}
