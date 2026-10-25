<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unidad')->insert([
            'descripcion' => 'MES',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('unidad')->insert([
            'descripcion' => 'VB',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('unidad')->insert([
            'descripcion' => 'METRO CÚBICO',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('unidad')->insert([
            'descripcion' => 'KILOMETRO',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('unidad')->insert([
            'descripcion' => 'GLB',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('unidad')->insert([
            'descripcion' => 'HORAS',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
