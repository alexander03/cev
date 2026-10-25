<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
	protected $table = 'empresa';
    //use SoftDeletes;
    
    protected $fillable = ['razonsocial','ruc','user_id','direccion'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

}
