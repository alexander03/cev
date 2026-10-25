<?php

use Illuminate\Database\Seeder;

class ConductorConcesionariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conductorconcesionaria')->insert([
            'conductor_id' => 1,
            'concesionaria_id' => 1
        ]);
        DB::table('conductorconcesionaria')->insert([
            'conductor_id' => 1,
            'concesionaria_id' => 2
        ]);
        DB::table('conductorconcesionaria')->insert([
            'conductor_id' => 2,
            'concesionaria_id' => 1
        ]);
        DB::table('conductorconcesionaria')->insert([
            'conductor_id' => 3,
            'concesionaria_id' => 1
        ]);
        DB::table('conductorconcesionaria')->insert([
            'conductor_id' => 4,
            'concesionaria_id' => 2
        ]);
    }
}
