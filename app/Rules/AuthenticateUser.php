<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AuthenticateUser implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $user;

    public function __construct($user){
        $this -> user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){
        //*Verificar Auth
        $user = $this -> user;
        $userAuth = User::select('*')
            -> where([
                [ 'username', 'LIKE', "%{$user}%" ],
                [ 'tipouser_id', '=', 7 ]
            ]) 
            -> orWhere('tipouser_id', '=', 8)
            -> get();
        if(!isset($userAuth[0])) return false;

        $verifyPass = Hash::check($value, $userAuth[0] -> password);
        if(!$verifyPass) return false;
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(){
        return 'Credenciales incorrectas, o no es un supervisor o conductor.';
    }
}
