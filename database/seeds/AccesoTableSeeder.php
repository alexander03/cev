<?php

use Illuminate\Database\Seeder;
use App\Opcionmenu;
class AccesoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        for ($i=1; $i <= Opcionmenu::all()->count(); $i++) { 
            DB::table('acceso')->insert([
            'opcionmenu_id' => $i,
            'tipouser_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        }
        
        DB::table('acceso')->insert([
            'opcionmenu_id' => 1,
            'tipouser_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('acceso')->insert([
            'opcionmenu_id' => 2,
            'tipouser_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);        
    }
}
