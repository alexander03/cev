<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SelectDifZero implements Rule
{
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
        if($value == 0) return false;
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(){

        return 'Su ' . $this -> nombAtt . ' es requerido';
    }
}