<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preciocombustible extends Model
{
    use SoftDeletes;
    protected $table = 'preciocombustible';
    protected $dates = ['deleted_at'];

    public function grifo() {
        return $this->belongsTo('App\Grifo', 'grifo_id');
    }
}
