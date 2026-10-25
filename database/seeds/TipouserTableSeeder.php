<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipouserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipouser')->insert([
            'descripcion' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now()
        ]);    

        DB::table('tipouser')->insert([
            'descripcion' => 'EMPLEADO',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('tipouser')->insert([
            'descripcion' => 'EMPLEADO5',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipouser')->insert([
            'descripcion' => 'EMPLEADO4',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipouser')->insert([
            'descripcion' => 'EMPLEADO3',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipouser')->insert([
            'descripcion' => 'EMPLEADO2',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipouser')->insert([
            'descripcion' => 'CONDUCTOR',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tipouser')->insert([
            'descripcion' => 'RESPONSABLE',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
