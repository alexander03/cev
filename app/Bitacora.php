<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
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
