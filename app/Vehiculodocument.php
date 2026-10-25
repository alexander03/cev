<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculodocument extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'vehiculodocument';

    public function vehiculo()
    {
        return $this->belongsTo('App\Vehiculo');
    }

    public function user()
    {
        return $this->belongsTo('App\User','usercomentario_id');
    }
}
