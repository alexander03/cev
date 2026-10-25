<?php

use Illuminate\Database\Seeder;

class RegManVehSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('regmanveh')->insert([
            'concesionaria_id' => 1,
            'ua_id' => 'ABCD1234',
            'cliente' => 'cliente de H2O',
            'ordencompra' => '10101010',
            'kmman' => 10,
            'kminicial' => 12,
            'kmfinal' => 13,
            'fechaentrada' => now(),
            'fechasalida' => now(),
            'tipomantenimiento' => 1,
            'telefono' => 154251452
        ]);

        DB::table('regmanveh')->insert([
            'concesionaria_id' => 2,
            'ua_id' => 'ABCD9999',
            'cliente' => 'cliente de CTO',
            'ordencompra' => '10101010',
            'kmman' => 100,
            'kminicial' => 120,
            'kmfinal' => 130,
            'fechaentrada' => now(),
            'fechasalida' => now(),
            'tipomantenimiento' => 1,
            'telefono' => 22222222
        ]);
        
        DB::table('descripcionregmanveh')->insert([
            'regmanveh_id' => 1,
            'cantidad' => 10,
            'monto' => 2504,
            'trabajo_id' => 1,
        ]);
        DB::table('descripcionregmanveh')->insert([
            'regmanveh_id' => 1,
            'cantidad' => 11,
            'monto' => 2503,
            'trabajo_id' => 2,
        ]);
        DB::table('descripcionregmanveh')->insert([
            'regmanveh_id' => 2,
            'cantidad' => 12,
            'monto' => 2502,
            'trabajo_id' => 2,
        ]);
        DB::table('descripcionregmanveh')->insert([
            'regmanveh_id' => 2,
            'cantidad' => 13,
            'monto' => 2501,
            'trabajo_id' => 1,
        ]);
        DB::table('descripcionregmanveh')->insert([
            'regmanveh_id' => 2,
            'cantidad' => 14,
            'monto' => 2500,
            'trabajo_id' => 2,
        ]);


//agrago UA para CTO
        DB::table('ua')->insert([
            'codigo' => 'ABCD9999',
            'descripcion' => 'Esta ua es prueba de CTO',
            'concesionaria_id' => 2,
            'habilitada' => true,
            'fecha_inicio' => now(),
            'fecha_fin' => '2021-09-12',
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
