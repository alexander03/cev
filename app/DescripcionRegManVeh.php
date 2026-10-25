<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescripcionRegManVeh extends Model
{
    protected $table = 'descripcionregmanveh';
    protected $fillable = ['id','regmanveh_id', 'cantidad','monto','trabajo_id'];
}
