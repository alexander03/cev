<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Lucía Mirian García Perl',
            'username' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
            'tipouser_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'name' => 'Gonzalo Perseo Miranda Altos',
            'username' => 'gonzalo',
            'email_verified_at' => now(),
            'password' => Hash::make('gonzalo'),
            'tipouser_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => now()
        ]);
        DB::table('users')->insert([
            'name' => 'Marta Garrido Leca',
            'username' => 'marta',
            'email_verified_at' => now(),
            'password' => Hash::make('marta'),
            'tipouser_id' => 7,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'name' => 'Alfonso Mejía llanos',
            'username' => 'alfonso',
            'email_verified_at' => now(),
            'password' => Hash::make('alfonso'),
            'tipouser_id' => 8,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
