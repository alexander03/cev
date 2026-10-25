<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalledocmat extends Model
{
    protected $table = 'detalledocmat';
    protected $dates = ['deleted_at'];
    use SoftDeletes;

    public function docmat()
    {
        return $this->belongsTo('App\Docmat');
    }

}
