<?php

use Illuminate\Database\Seeder;

class GrupomenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupomenu')->insert([
            'descripcion' => 'MANTENIMIENTO',
            'icono' => 'content_paste',
            'orden' => 1
        ]);
        DB::table('grupomenu')->insert([
            'descripcion' => 'SISTEMA',
            'icono' => 'settings_power',
            'orden' => 2
        ]);
        DB::table('grupomenu')->insert([
            'descripcion' => 'PROCESOS',
            'icono' => 'dashboard',
            'orden' => 3
        ]);
    }
}
