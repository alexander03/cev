<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class DescripcionRegManVeh extends Model
{
    use LegacyDateSerialization;
    protected $table = 'descripcionregmanveh';
    protected $fillable = ['id','regmanveh_id', 'cantidad','monto','trabajo_id'];
}
