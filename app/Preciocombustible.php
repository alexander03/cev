<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preciocombustible extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'preciocombustible';

    public function grifo() {
        return $this->belongsTo('App\Grifo', 'grifo_id');
    }
}
