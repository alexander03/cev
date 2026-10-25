<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detallepreciocombustible extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'detallepreciocombustible';

    public function preciocombustible()
    {
        return $this->belongsTo('App\Preciocombustible');
    }

    public function tipocombustible()
    {
        return $this->belongsTo('App\Tipocombustible');
    }

}
