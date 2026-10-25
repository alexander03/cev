<?php

use Illuminate\Database\Seeder;

class TrabajoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('trabajo')->insert([

	        ['descripcion' => 'trabajo de prueba 1',
	        'created_at' => now(),
	        'updated_at' => now()],

	        ['descripcion' => 'segundo trabajo de prueba',
	        'created_at' => now(),
	        'updated_at' => now()],

        ]);
    }
}
