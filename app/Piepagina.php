<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piepagina extends Model
{
    use LegacyDateSerialization;
    use SoftDeletes;
    protected $table = 'piepagina';
    
}