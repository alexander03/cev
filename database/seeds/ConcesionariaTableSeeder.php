<?php

use Illuminate\Database\Seeder;

class ConcesionariaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('concesionaria')->insert([
            'ruc' => '20523611250',
            'razonsocial' => 'H2OLMOS S.A.',
            'abreviatura' => 'H2OLMOS',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('concesionaria')->insert([
            'ruc' => '20509093521',
            'razonsocial' => 'Concesionaria Trasvase Olmos S.A.',
            'abreviatura' => 'C.T.O.',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
