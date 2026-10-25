<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbastecimientoCombustibleTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('abastecimiento_combustible')->insert([
            'fecha_abastecimiento' => now(),
            'grifo_id' => 1,
            'user_id' => 3,
            'conductor_fake' => null,
            'ua_id' => 2,
            'equipo_id' => 2,
            'vehiculo_id' => null,
            'qtdgl' => 20.6,
            'qtdl' => 30.6,
            'km' => 128.5,
            'abastecimiento_dia' => 21.33,
            'motivo' => 'Llenado de mi tanque que ya no poseía',
            'comprobante' => 'BOLETA',
            'numero_comprobante' => 'AAA1',
            'hora_inicio' => now(),
            'hora_fin' => '10:30:20',
            'abastecimiento_id' => 1,
            'tipocombustible_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('abastecimiento_combustible')->insert([
            'fecha_abastecimiento' => now(),
            'grifo_id' => 2,
            'user_id' => 3,
            'conductor_fake' => null,
            'ua_id' => 3,
            'equipo_id' => 2,
            'vehiculo_id' => null,
            'qtdgl' => 10.6,
            'qtdl' => 320.6,
            'km' => 18.5,
            'abastecimiento_dia' => 21.21,
            'motivo' => 'Llenado de mi tanque que ya no poseía',
            'comprobante' => 'BOLETA',
            'numero_comprobante' => 'DSAUI2',
            'hora_inicio' => now(),
            'hora_fin' => '10:30:20',
            'abastecimiento_id' => 1,
            'tipocombustible_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('abastecimiento_combustible')->insert([
            'fecha_abastecimiento' => now(),
            'grifo_id' => 1,
            'user_id' => 4,
            'conductor_fake' => null,
            'ua_id' => 2,
            'equipo_id' => 2,
            'vehiculo_id' => null,
            'qtdgl' => 200.6,
            'qtdl' => 320.6,
            'km' => 128.5,
            'abastecimiento_dia' => 2.36, 
            'motivo' => 'Llenado de mi tanque que ya no poseía',
            'comprobante' => 'BOLETA',
            'numero_comprobante' => '1DAS',
            'hora_inicio' => now(),
            'hora_fin' => '10:30:20',
            'abastecimiento_id' => 1,
            'tipocombustible_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
