<?php

namespace App\Rules;

use App\Grifo;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class SearchGrifoRule implements Rule{

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
                $grifoDB =  Grifo::where('descripcion', $value) -> get();
                if( !$grifoDB -> isEmpty() ) return true;
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

        return 'No se encontro una grifo con esa descripción';
    }
}
