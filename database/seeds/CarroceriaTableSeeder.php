<?php

use Illuminate\Database\Seeder;

class CarroceriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('carroceria')->insert([
            'descripcion' => 'SUV',
            'created_at' => now(),
            'updated_at' => now()
        ]);   
        DB::table('carroceria')->insert([
            'descripcion' => 'PICK UP',
            'created_at' => now(),
            'updated_at' => now()
        ]);   

    }
}
