<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserConcesionaria;
use App\Concesionaria;
use Illuminate\Support\Facades\DB;

class UserConcesionariaController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function actual($query) {

        $lista= Concesionaria::join('users', 'concesionaria.id', '=', 'users.concesionaria_id')
                ->where('users.id','=',auth()->user()->id)
                ->select('concesionaria.*');
        return $lista;
        /*if(count($lista)>0){
            return $lista[0]->razonsocial;
        }else{
            return "Sin Concesionaria";
        }*/
    }


    public function devolverConce($query) {

        return UserConcesionaria::join('users', 'userconcesionaria.user_id', '=', 'users.id')
        	->join('concesionaria', 'userconcesionaria.concesionaria_id', '=', 'concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->where('userconcesionaria.estado','=',true)
            ->orderBy('concesionaria.id', 'ASC')
            ->select('concesionaria.id as id', 'concesionaria.razonsocial as razonsocial');
    }

    public function concesionaria($id) {

        DB::table('users')
                ->where('id', auth()->user()->id)
                ->update(['concesionaria_id' => $id]);
        
        DB::table('userconcesionaria')
                ->where('user_id', auth()->user()->id)
                ->where('concesionaria_id', $id)
                ->update(['estado' => true]);
        //return redirect()->route('home');
    }


}