<?php

use Illuminate\Database\Seeder;

class KilometrajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kilometraje')->insert([
            'descripcion' => 'TIPO 1',
            'limite_inf' => '1000',
            'limite_sup' => '2000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('kilometraje')->insert([
            'descripcion' => 'TIPO 2',
            'limite_inf' => '4000',
            'limite_sup' => '5000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
