<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GrifoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     App\Grifo::create([
        'descripcion' => 'LEON DE ORO',
        'ubicacion'   => 'UBICACIÓN 001',
        'abastecimiento_id' => 1,
        'contacto' => 'CONTACTO PRUEBA 01',
        'telefono' => '555666444',
        'correo' => 'micorreo@hotmail.com'
    ]);   
     App\Grifo::create([
        'descripcion' => 'PECSA',
        'ubicacion'   => 'UBICACIÓN 002',
        'abastecimiento_id' => 2,
        'contacto' => 'CONTACTO PRUEBA 02',
        'telefono' => '222489321',
        'correo' => 'elCorreo@hgmail.com'

    ]); 
     App\Grifo::create([
        'descripcion' => 'PRIMAX',
        'ubicacion'   => 'UBICACIÓN 003',
        'abastecimiento_id' => 3,
        'contacto' => 'CONTACTO PRUEBA 03',
        'telefono' => '456789123',
        'correo' => 'empresa99@propiedad.com'
    ]);   
    }
}
