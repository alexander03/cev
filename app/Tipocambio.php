<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class Tipocambio extends Model
{
    use LegacyDateSerialization;
	

	protected $table = 'tipocambio';
    //use SoftDeletes;
    
    protected $fillable = ['fecha','precio'];
}
