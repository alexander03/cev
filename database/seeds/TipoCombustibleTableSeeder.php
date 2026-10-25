<?php

use Illuminate\Database\Seeder;

class TipoCombustibleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipocombustible')->insert([
            'descripcion' => 'tipocombustible prueba 1',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipocombustible')->insert([
            'descripcion' => 'tipocombustible prueba 2',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipocombustible')->insert([
            'descripcion' => 'abastecimiento prueba 3',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
