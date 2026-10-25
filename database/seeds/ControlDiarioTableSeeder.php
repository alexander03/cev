<?php

use Illuminate\Database\Seeder;

class ControlDiarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('controldiario')->insert([
            'equipo_id' => 1,
            'ua_id' => 1,
            'turno' => 1,
            'horometro_inicial' => 4523.52,
            'horometro_final'  => 4541.12,
            'fecha' => '2020-8-20',
//            'hora_inicio' => '15:00',
            'hora_total' => 1.5,
//            'hora_fin' => '17:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('controldiario')->insert([
            'equipo_id' => 2,
            'turno' => 1,
            'horometro_inicial' => 4583.52,
            'horometro_final'  => 4602.12,
            'ua_id' => 4,
            'tipohora_id' => 2,
            'fecha' => '2020-8-20',
//            'hora_inicio' => '15:00',
            'hora_total' => 1.5,
//            'hora_fin' => '17:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);        
    }
}
