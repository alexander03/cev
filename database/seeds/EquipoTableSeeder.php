<?php

use Illuminate\Database\Seeder;

class EquipoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('equipo')->insert([
//            'codigo' => '1234567FD',
            'ua_id' => 1,
            'descripcion' => 'GRUPO GENERADOR',
            'modelo' => 'P33-1',
            'placa' => '172084TB',
            'marca_id' => 1,
            'contratista_id' => 1,
            'anio' => '2010',
            'capacidad_carga' => 1223,
            'area_id' => 1,
            'concesionaria_id' => 1,
            'horas_min' => 152,
            'precio' => 4550,
            'moneda' => 1,
            'unidad_id' => 6,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('equipo')->insert([
//            'codigo' => '452SE1FG',
            'ua_id' => 4,
            'descripcion' => 'CAMIONETA',
            'modelo' => 'L200',
            'placa' => '456123FD',
            'marca_id' => 2,
            'contratista_id' => 2,
            'capacidad_carga' => 2342,
            'anio' => '2015',
            'area_id' => 1,
            'concesionaria_id' => 2,
            'horas_min' => 152,
            'precio' => 4550,
            'moneda' => 1,
            'unidad_id' => 6,
            'created_at' => now(),
            'updated_at' => now()
        ]);        
    }
}
