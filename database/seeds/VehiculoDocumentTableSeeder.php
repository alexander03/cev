<?php

use Illuminate\Database\Seeder;

class VehiculoDocumentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vehiculodocument')->insert([
            'vehiculo_id' => 1,
            'fecha' => '2020-10-12',
            'tipo' => 'SOAT',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('vehiculodocument')->insert([
            'vehiculo_id' => 1,
            'fecha' => '2020-10-20',
            'tipo' => 'GPS',
            'created_at' => now(),
            'updated_at' => now()
        ]); 
        DB::table('vehiculodocument')->insert([
            'vehiculo_id' => 2,
            'fecha' => '2020-12-12',
            'tipo' => 'SOAT',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('vehiculodocument')->insert([
            'vehiculo_id' => 2,
            'fecha' => '2020-11-20',
            'tipo' => 'GPS',
            'created_at' => now(),
            'updated_at' => now()
        ]); 
    }
}
