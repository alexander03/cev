<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class UserConcesionaria extends Model
{
    use LegacyDateSerialization;
    protected $table = 'userconcesionaria';
    //use SoftDeletes;
    
    //protected $fillable = ['concesionaria_id', 'user_id','estado'];

    public function concesionaria()
    {
        return $this->belongsTo('App\Concesionaria');
    }
    
}

