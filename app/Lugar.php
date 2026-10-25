<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lugar extends Model
{
    use LegacyDateSerialization;
    protected $table = 'lugar';
    use SoftDeletes;
    
    protected $fillable = ['nombre'];

    public function garita()
    {
        return $this->belongsTo('App\Garita', 'garita_id');
    }

}