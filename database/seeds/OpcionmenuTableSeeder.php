<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpcionmenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('opcionmenu')->insert([
            [
                'descripcion' => 'Ua',
                'link' => 'ua',
                'icono' => 'api',
                'orden' => 1,
                'grupomenu_id' => 1
            ],
            [
                'descripcion' => 'Unidades',
                'link' => 'unidad',
                'icono' => 'ac_unit',
                'orden' => 1,
                'grupomenu_id' => 1
            ],
            [
                'descripcion' => 'Responsable',
                'link' => 'responsable',
                'icono' => 'contacts',
                'orden' => 1,
                'grupomenu_id' => 1              
            ]
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Tipo de users',
            'link' => 'tipouser',
            'icono' => 'contacts',
            'orden' => 4,
            'grupomenu_id' => 2
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Marcas',
            'link' => 'marcas',
            'icono' => 'model_training',
            'orden' => 1,
            'grupomenu_id' => 1
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Repuestos',
            'link' => 'repuestos',
            'icono' => 'settings',
            'orden' => 2,
            'grupomenu_id' => 1
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Conductores',
            'link' => 'conductores',
            'icono' => 'face',
            'orden' => 3,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Carrocerías',
            'link' => 'carroceria',
            'icono' => 'face',
            'orden' => 3,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Combustibles',
            'link' => 'tipocombustible',
            'icono' => 'info',
            'orden' => 3,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Abastecimientos',
            'link' => 'abastecimientolugar',
            'icono' => 'face',
            'orden' => 3,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            ['descripcion' => 'Areas',
            'link' => 'areas',
            'icono' => 'info',
            'orden' => 4,
            'grupomenu_id' => 1],
            ['descripcion' => 'Contratistas',
            'link' => 'contratistas',
            'icono' => 'perm_identity',
            'orden' => 5,
            'grupomenu_id' => 1],
            ['descripcion' => 'Trabajos',
            'link' => 'trabajos',
            'icono' => 'play_for_work',
            'orden' => 6,
            'grupomenu_id' => 1],
            ['descripcion' => 'Concesionarias',
            'link' => 'concesionarias',
            'icono' => 'api',
            'orden' => 7,
            'grupomenu_id' => 1],
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Grupo Menu',
            'link' => 'grupomenu',
            'icono' => 'vertical_split',
            'orden' => 3,
            'grupomenu_id' => 2
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Opcion Menu',
            'link' => 'opcionmenu',
            'icono' => 'language',
            'orden' => 4,
            'grupomenu_id' => 2
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Tipo de horas',
            'link' => 'tipohora',
            'icono' => 'hourglass_empty',
            'orden' => 4,
            'grupomenu_id' => 1
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Vehículos',
            'link' => 'vehiculo',
            'icono' => 'directions_car',
            'orden' => 4,
            'grupomenu_id' => 1
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Ctrl de equipos',
            'link' => 'controldiario',
            'icono' => 'controldiario',
            'orden' => 4,
            'grupomenu_id' => 3
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Grifos',
            'link' => 'grifo',
            'icono' => 'toc',
            'orden' => 5,
            'grupomenu_id' => 1
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Equipos',
            'link' => 'equipo',
            'icono' => 'directions_car',
            'orden' => 6,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Report Kilometraje',
            'link' => 'reportkilometraje',
            'icono' => 'directions_car',
            'orden' => 6,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Kilometrajes',
            'link' => 'kilometrajes',
            'icono' => 'directions_car',
            'orden' => 7,
            'grupomenu_id' => 1
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Mant. corr. y prev.',
            'link' => 'mantcorrprev',
            'icono' => 'api',
            'orden' => 1,
            'grupomenu_id' => 3
        ]);

        DB::table('opcionmenu')->insert([
            'descripcion' => 'Abast. combustible',
            'link' => 'abastecimiento',
            'icono' => 'api',
            'orden' => 1,
            'grupomenu_id' => 3
        ]);
        

        //SISTEMA
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Usuario',
            'link' => 'user',
            'icono' => 'person',
            'orden' => 1,
            'grupomenu_id' => 2
        ]);

    
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Reg.Rep.Vehicular',
            'link' => 'regrepveh',
            'icono' => 'api',
            'orden' => 1,
            'grupomenu_id' => 3
        ]);
        DB::table('opcionmenu')->insert([
            'descripcion' => 'Reg.Man.Vehicular',
            'link' => 'regmanveh',
            'icono' => 'api',
            'orden' => 1,
            'grupomenu_id' => 3
        ]);
    }
}
