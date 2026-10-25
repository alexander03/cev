<?php

use Illuminate\Database\Seeder;

class ContratistaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contratista')->insert([
            'ruc' => '12345678901',
            'razonsocial' => 'EMPRESA LAGARITA SAC',
            'propietario' => 'Propietario 1',
            'email' => 'email@emailprueba1.com',
            'telefono' => '15368945',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('contratista')->insert([
            'ruc' => '74679823478',
            'razonsocial' => 'GRUPO MIMASCOT SRL',
            'propietario' => 'Propietario 2',
            'email' => 'email2@emailprueba2.com',
            'telefono' => '25368945',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('contratista')->insert([
            'ruc' => '92347859878',
            'razonsocial' => 'CONVENCIONES POLVOS ROSADOS SAA',
            'propietario' => 'Propietario 4',
            'email' => 'email3@emailprueba3.com',
            'telefono' => '35368945',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
