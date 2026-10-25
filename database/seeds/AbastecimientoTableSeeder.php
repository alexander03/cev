<?php

use Illuminate\Database\Seeder;

class AbastecimientoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('abastecimiento')->insert([
            'descripcion' => 'abastecimiento prueba 1',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('abastecimiento')->insert([
            'descripcion' => 'abastecimiento prueba 2',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('abastecimiento')->insert([
            'descripcion' => 'abastecimiento prueba 3',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
