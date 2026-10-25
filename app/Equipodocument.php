<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipodocument extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'equipodocument';

    public function equipo()
    {
        return $this->belongsTo('App\Equipo');
    }

    public function user()
    {
        return $this->belongsTo('App\User','usercomentario_id');
    }
}
