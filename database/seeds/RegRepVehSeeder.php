<?php

use Illuminate\Database\Seeder;

class RegRepVehSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regrepveh')->insert([
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

        DB::table('regrepveh')->insert([
            'concesionaria_id' => 2,
            'ua_id' => 'ABCD9999',
            'cliente' => 'cliente de CTO',
            'ordencompra' => '20202020',
            'kmman' => 100,
            'kminicial' => 120,
            'kmfinal' => 130,
            'fechaentrada' => now(),
            'fechasalida' => now(),
            'tipomantenimiento' => 1,
            'telefono' => 22222222
        ]);
        
        DB::table('descripcionregrepveh')->insert([
            'regrepveh_id' => 1,
            'cantidad' => 10,
            'monto' => 2504,
            'repuesto_id' => 1,
        ]);
        DB::table('descripcionregrepveh')->insert([
            'regrepveh_id' => 1,
            'cantidad' => 11,
            'monto' => 2503,
            'repuesto_id' => 2,
        ]);
        DB::table('descripcionregrepveh')->insert([
            'regrepveh_id' => 2,
            'cantidad' => 12,
            'monto' => 2502,
            'repuesto_id' => 2,
        ]);
        DB::table('descripcionregrepveh')->insert([
            'regrepveh_id' => 2,
            'cantidad' => 13,
            'monto' => 2501,
            'repuesto_id' => 1,
        ]);
        DB::table('descripcionregrepveh')->insert([
            'regrepveh_id' => 2,
            'cantidad' => 14,
            'monto' => 2500,
            'repuesto_id' => 2,
        ]);
    }
}
