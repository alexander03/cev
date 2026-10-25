<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periodo extends Model
{
    use LegacyDateSerialization;
    protected $table = 'periodo'; 
    use SoftDeletes;
    
}
