<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use LegacyDateSerialization;
	protected $table = 'bitacora';

	// protected $casts = [
	// 	'valor_anterior' => 'array',
	// 	'valor_posterior' => 'array'
	// ];
	public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
