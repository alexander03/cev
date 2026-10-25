<?php

use Illuminate\Database\Seeder;

class RepuestoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('repuesto')->insert([
            'codigo' => '1000001',
            'descripcion' => 'FRENO DE MANO',
            'unidad_id' => '1',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('repuesto')->insert([
            'codigo' => '1000002',
            'descripcion' => 'FAROS DELANTEROS',
            'unidad_id' => '2',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('repuesto')->insert([
            'codigo' => '1000003',
            'descripcion' => 'ALARMA DE RETROCESO',
            'unidad_id' => '2',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('repuesto')->insert([
            'codigo' => '100004',
            'descripcion' => 'PARACHOQUES',
            'unidad_id' => '2',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('repuesto')->insert([
            'codigo' => '100005',
            'descripcion' => 'PLUMILLAS',
            'unidad_id' => '2',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
