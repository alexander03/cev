<?php

use Illuminate\Database\Seeder;

class TipohoraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     App\Tipohora::create(['codigo' => '01' , 'descripcion' => 'Abastecimiento']);   
     App\Tipohora::create(['codigo' => '02' , 'descripcion' => 'Reparación de llantas']); 
     App\Tipohora::create(['codigo' => '03' , 'descripcion' => 'Mantenimiento Mecanico']);   
     App\Tipohora::create(['codigo' => '04' , 'descripcion' => 'Parado Inoperativo']);       
 }
}
