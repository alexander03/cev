<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agente extends Model
{
    use LegacyDateSerialization;
    protected $table = 'agente';
    use SoftDeletes;
    
    protected $fillable = ['nombres','apelidos','telefono','dni','user_id','concesionaria_id','garita_id'];

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria','concesionaria_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
