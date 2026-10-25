<?php

namespace App;

use App\Traits\LegacyDateSerialization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use LegacyDateSerialization;
    protected $table = 'persona';
    use SoftDeletes;
    
    protected $fillable = ['nombres','apelidos','dni','otros','mostrardni','contratista_id','ua_id'];

    public function contratista(){

        return $this->belongsTo(Contratista::class, 'contratista_id')->withTrashed();        
    }

    public function ua(){

        return $this->belongsTo(Ua::class, 'ua_id')->withTrashed();        
    }

}
