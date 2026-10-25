<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Concesionaria;
use App\Equipo;
use App\Vehiculo;

class RulePlacaExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $concesionaria_id = Concesionaria::getConcesionariaActual()->id;

        $equipo_placa = Equipo::where('placa', '=', $value)
                            ->where('concesionaria_id', '=', $concesionaria_id)->first();
        if ( $equipo_placa != null ) return true;

        $vehiculo_placa = Vehiculo::where('placa', '=', $value)
                                ->where('concesionaria_id', '=', $concesionaria_id)->first();
        if ( $vehiculo_placa != null ) return true;

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'La unidad placa ingresada es incorrecta';
    }
}
