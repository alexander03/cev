<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropietariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('propietarios')->insert([
            'descripcion' => 'Este propietario es prueba 01',
            'fecha_llegada' => '20/06/20',
            'fecha_salida' => now(),
            'fecha_contrato' => '20/06/20',
            'status' => 'Habilitado',
            'hra' => 'prueba hra 01',
            'hrb' => 'pruebb hrb 01',
            'hrc' => 'pruebc hrc 01',
            'km' => 25000,
            'observacion' => 'alguna obs 01',
            'ubicacion' => 'Argenta 01',
            'ua_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('propietarios')->insert([
            'descripcion' => 'Este propietario es prueba 02',
            'fecha_llegada' => '20/06/20',
            'fecha_salida' => now(),
            'fecha_contrato' => '20/06/20',
            'status' => 'Habilitado',
            'hra' => 'prueba hra 02',
            'hrb' => 'pruebb hrb 02',
            'hrc' => 'pruebc hrc 02',
            'km' => 25000,
            'observacion' => 'alguna obs 02',
            'ubicacion' => 'Argenta 02',
            'ua_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('propietarios')->insert([
            'descripcion' => 'Este propietario es prueba 03',
            'fecha_llegada' => '20/06/20',
            'fecha_salida' => now(),
            'fecha_contrato' => '20/06/20',
            'status' => 'Habilitado',
            'hra' => 'prueba hra 03',
            'hrb' => 'pruebb hrb 03',
            'hrc' => 'pruebc hrc 03',
            'km' => 25000,
            'observacion' => 'alguna obs 03',
            'ubicacion' => 'Argenta 03',
            'ua_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('propietarios')->insert([
            'descripcion' => 'Este propietario es prueba 04',
            'fecha_llegada' => '20/06/20',
            'fecha_salida' => now(),
            'fecha_contrato' => '20/06/20',
            'status' => 'Habilitado',
            'hra' => 'prueba hra 04',
            'hrb' => 'pruebb hrb 04',
            'hrc' => 'pruebc hrc 04',
            'km' => 25000,
            'observacion' => 'alguna obs 04',
            'ubicacion' => 'Argenta 04',
            'ua_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
