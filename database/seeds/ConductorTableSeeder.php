<?php

use Illuminate\Database\Seeder;

class ConductorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conductor')->insert([
            'nombres' => 'ELISEO AURELIO',
            'apellidos' => 'RIOS OBANDO',
            'dni' => '17811439',
            'categoria' => 'A-IIb',
            'licencia' => 'D-17811439',
            'fechavencimiento' => '2021-02-15',
            'contratista_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
            ]);
            DB::table('conductor')->insert([
            'nombres' => 'EDMUNDO',
            'apellidos' => 'VILLENA FIESTAS',
            'dni' => '17974640',
            'categoria' => 'A-IIb',
            'licencia' => 'D-17974640',
            'fechavencimiento' => '2021-10-09',
            'contratista_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('conductor')->insert([
            'nombres' => 'JORGE LUIS',
            'apellidos' => 'GAMARRA AYALA',
            'dni' => '16691239',
            'categoria' => 'A-IIIc',
            'licencia' => 'C-16691239',
            'fechavencimiento' => '2021-08-09',
            'contratista_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('conductor')->insert([
            'nombres' => 'BERNARDO RAMIRO',
            'apellidos' => 'PEREZ DIAZ',
            'dni' => '16732333',
            'categoria' => 'A-IIIc',
            'licencia' => 'B-16732333',
            'fechavencimiento' => '2025-01-24',
            'contratista_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
