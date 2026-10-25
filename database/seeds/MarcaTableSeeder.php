<?php

use Illuminate\Database\Seeder;

class MarcaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marca')->insert([
            'descripcion' => 'MITSUBISHI',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('marca')->insert([
            'descripcion' => 'TOYOTA',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('marca')->insert([
            'descripcion' => 'HITACHI',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('marca')->insert([
            'descripcion' => 'VOLVO',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('marca')->insert([
            'descripcion' => 'SANY',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
