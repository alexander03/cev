<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Rules\PasswordRule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        auth()->user()->update(['name' => $request->get('name')]);

        return back()->withStatus(__('Perfil actualizado con éxito'));
    }

    
    public function password(Request $request)
    {
        
        $rules_old_password = [
            'old_password' => new PasswordRule,
        ];
        
        $validator_old_password = Validator::make($request->all(), $rules_old_password);
        if($validator_old_password->fails()) return back()->withErrors($validator_old_password->errors());

        $rules_password = [
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ];

        $msg = [
            'password_confirmation.same' => 'Las contraseñas no coinciden'
        ];
        
        $validator_password = Validator::make($request->all(), $rules_password, $msg);
        if($validator_password->fails()) return back()->withErrors($validator_password->errors());

        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withStatusPassword(__('Contraseña actualizada con éxito'));
    }
}
