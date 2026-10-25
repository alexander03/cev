<?php

use Illuminate\Database\Seeder;

class UserConcesionariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('userconcesionaria')->insert([
            'user_id' => 1,
            'concesionaria_id' => 1,
            'estado'=>false,
            'created_at' => now()
        ]);
        DB::table('userconcesionaria')->insert([
            'user_id' => 1,
            'concesionaria_id' => 2,
            'estado'=>true,
            'created_at' => now()
        ]);

        DB::table('userconcesionaria')->insert([
            'user_id' => 2,
            'concesionaria_id' => 1,
            'estado'=>true,
            'created_at' => now()
        ]);
        DB::table('userconcesionaria')->insert([
            'user_id' => 2,
            'concesionaria_id' => 2,
            'estado'=>true,
            'created_at' => now()
        ]);

        DB::table('userconcesionaria')->insert([
            'user_id' => 3,
            'concesionaria_id' => 1,
            'estado'=>false,
            'created_at' => now()
        ]);
        DB::table('userconcesionaria')->insert([
            'user_id' => 3,
            'concesionaria_id' => 2,
            'estado'=>true,
            'created_at' => now()
        ]);

        DB::table('userconcesionaria')->insert([
            'user_id' => 4,
            'concesionaria_id' => 1,
            'estado'=>false,
            'created_at' => now()
        ]);
        DB::table('userconcesionaria')->insert([
            'user_id' => 4,
            'concesionaria_id' => 2,
            'estado'=>true,
            'created_at' => now()
        ]);
    }
}
