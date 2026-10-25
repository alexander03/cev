<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ua')->insert([
            'codigo' => 'ABCD1234',
            'descripcion' => 'Esta ua es prueba 01',
            'concesionaria_id' => 1,
            'habilitada' => true,
            'fecha_inicio' => now(),
            'fecha_fin' => '2021-09-12',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('ua')->insert([
            'codigo' => 'ABCD1233',
            'descripcion' => 'Esta ua es prueba 02',
            'concesionaria_id' => 1,
            'habilitada' => true,
            'fecha_inicio' => now(),
            'fecha_fin' => '2021-09-12',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('ua')->insert([
            'codigo' => 'ABCD1232',
            'descripcion' => 'Esta ua es prueba 03',
            'concesionaria_id' => 1,
            'habilitada' => true,
            'fecha_inicio' => now(),
            'fecha_fin' => '2021-09-12',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('ua')->insert([
            'codigo' => 'ABCD1231',
            'descripcion' => 'Esta ua es prueba 04',
            'concesionaria_id' => 1,
            'habilitada' => true,
            'fecha_inicio' => now(),
            'fecha_fin' => '2021-09-12',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
