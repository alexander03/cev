<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalledocmat extends Model
{
    use LegacyDateSerialization;
    protected $table = 'detalledocmat';
    use SoftDeletes;

    public function docmat()
    {
        return $this->belongsTo('App\Docmat');
    }

}
