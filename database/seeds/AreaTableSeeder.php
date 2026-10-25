<?php

use Illuminate\Database\Seeder;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('area')->insert([

	        ['descripcion' => 'O&M',
            'nivel' => 1,
            'areapadre_id' => null,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'PRODUCCION',
            'nivel' => 1,
            'areapadre_id' => null,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'RECURSOS HUMANOS',
            'nivel' => 1,
            'areapadre_id' => null,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'EMPAQUETADO',
            'nivel' => 2,
            'areapadre_id' => 2,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'RECEPCION MATERIALES',
            'nivel' => 2,
            'areapadre_id' => 2,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'CONTRATACION PERSONAL',
            'nivel' => 2,
            'areapadre_id' => 3,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'CAPACITACION',
            'nivel' => 2,
            'areapadre_id' => 3,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'RECEPCION MAQUINARIA',
            'nivel' => 3,
            'areapadre_id' => 5,
            'created_at' => now(),
            'updated_at' => now()],

            ['descripcion' => 'RECEPCION ARTICULOS OFICINA',
            'nivel' => 3,
            'areapadre_id' => 5,
            'created_at' => now(),
            'updated_at' => now()],

        ]);
    }
}
