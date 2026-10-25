<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipocambio extends Model
{
	

	protected $table = 'tipocambio';
    //use SoftDeletes;
    
    protected $fillable = ['fecha','precio'];
}
