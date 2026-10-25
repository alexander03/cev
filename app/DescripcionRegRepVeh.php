<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class DescripcionRegRepVeh extends Model
{
    use LegacyDateSerialization;
    protected $table = 'descripcionregrepveh';
    protected $fillable = ['id','regrepveh_id', 'cantidad','monto','repuesto_id'];

}

