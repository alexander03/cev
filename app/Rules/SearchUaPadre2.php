<?php

namespace App\Rules;

use App\Ua;
use App\Concesionaria;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class SearchUaPadre2 implements Rule{

    private $nombAtt = "";

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){

        $this -> nombAtt = $attribute;

        if($value){
            try{
                $uaDB =  Ua::where('codigo', $value)->where('padre','S') -> where('concesionaria_id','=',$this -> getConsecionariaActual()) -> get();
                if( !$uaDB -> isEmpty() ) return true;
                else return false; 

            }catch(Exception $error){
                return false; 
            }
        }
    
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(){

        return 'No se encontro una UA con ese código.';
    }
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
    }
}