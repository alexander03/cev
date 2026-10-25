<?php

use App\AbastecimientoCombustible;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AbastecimientoTableSeeder::class,
            CarroceriaTableSeeder::class,
            ConcesionariaTableSeeder::class,
            TipouserTableSeeder::class,
            UsersTableSeeder::class,
            MarcaTableSeeder::class,
            GrupomenuTableSeeder::class,
            OpcionmenuTableSeeder::class,
            UnidadTableSeeder::class,
            RepuestoTableSeeder::class,
            ContratistaTableSeeder::class,
            ConductorTableSeeder::class,
            ConductorConcesionariaSeeder::class,
            AreaTableSeeder::class,
            TrabajoTableSeeder::class,
            TipohoraTableSeeder::class,
            UaTableSeeder::class,
            GrifoTableSeeder::class,
            KilometrajeSeeder::class,
            EquipoTableSeeder::class,
            VehiculoTableSeeder::class,
            // ControlDiarioTableSeeder::class, //FALTA CAMPOS ??? NO FUNCA
            AccesoTableSeeder::class,
            VehiculoDocumentTableSeeder::class,
            ChecklistvehicularTableSeeder::class,
            AbastecimientoCombustibleTableSeeder::class,
            UserConcesionariaSeeder::class,
            RegRepVehSeeder::class,
            RegManVehSeeder::class,
            TipoCombustibleTableSeeder::class
        ]);
    }
}
