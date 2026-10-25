<?php

namespace App\Rules;

use App\Conductor;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class SearchConductorRule implements Rule{

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
                $conductorDB =  Conductor::where('dni', $value) -> get();
                if( !$conductorDB -> isEmpty() ) return true;
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

        return 'No se encontro un conductor con ese dni';
    }
}
