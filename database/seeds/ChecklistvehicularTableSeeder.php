<?php

use Illuminate\Database\Seeder;
use App\Checklistvehicular;

class ChecklistvehicularTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon\Carbon::now('America/Lima')->toDateTimeString();
        Checklistvehicular::create([
            'fecha_registro' => $now,
            'equipo_id' => 1,
            'k_inicial' => 54.00,
            'k_final' => 80.00,
            'lider_area' => 'OSCAR MARIANO DEL PORTAL',
            'conductor_id' => 1,
            'concesionaria_id' => 1,
            'sistema_electrico' => [
                ['orden' => 1,'id' => 'freno_emergencia', 'titulo' => 'Freno de emergencia', 'estado' => true],
                ['orden' => 2,'id' => 'funcionamiento_tablero', 'titulo' => 'Funcionamiento de tablero','estado' => false],
                ['orden' => 3,'id' => 'estado_bateria_funcionamiento', 'titulo' => 'Estado de batería y funcionamiento', 'estado' => false],
                ['orden' => 4,'id' => 'funcionamiento_claxon', 'titulo' => 'Funcionamiento de claxon', 'estado' => false],
                ['orden' => 5,'id' => 'luces_retroceso_pirata', 'titulo' => 'Luces de retroceso pirata','estado' => false],
                ['orden' => 6,'id' => 'luces_direccional', 'titulo' => 'Luces direccional','estado' => false],
                ['orden' => 7,'id' => 'faros_neblineros', 'titulo' => 'Faros neblineros','estado' => false],
                ['orden' => 8,'id' => 'faros_delanteros', 'titulo' => 'Faros delanteros','estado' => false],
                ['orden' => 9,'id' => 'faros_posteriores', 'titulo' => 'Faros posteriores','estado' => false],
                ['orden' => 10,'id' => 'alarma_retroceso', 'titulo' => 'Alarma de retroceso','estado' => false],
            ],
            'sistema_mecanico' => [
                ['orden' => 1, 'id' => 'nivel_liquido_freno', 'titulo' => 'Nivel liquido de freno', 'estado' => true],
                ['orden' => 2, 'id' => 'sistema_direccion', 'titulo' => 'Sistema de dirección', 'estado' => true],
                ['orden' => 3, 'id' => 'palancas_cambios', 'titulo' => 'Palancas de cambios', 'estado' => true],
                ['orden' => 4, 'id' => 'estado_neumaticos', 'titulo' => 'Estado de neumáticos', 'estado' => true],
                ['orden' => 5, 'id' => 'llantas_repuesto', 'titulo' => 'Llantas de repuesto', 'estado' => false],
                ['orden' => 6, 'id' => 'ajustes_tuercas', 'titulo' => 'Ajustes de tuercas', 'estado' => false],
                ['orden' => 7, 'id' => 'presion_llantas_libras', 'titulo' => 'Presion de llantas en libras', 'estado' => false],
                ['orden' => 8, 'id' => 'cinturon_seguridad_conductor', 'titulo' => 'Cinturon de seguridad conductor', 'estado' => true],
                ['orden' => 9, 'id' => 'cinturon_seguridad_pasajeros', 'titulo' => 'Cinturon de seguridad pasajeros', 'estado' => true],
                ['orden' => 10, 'id' => 'suspension', 'titulo' => 'Suspensión', 'estado' => true],

                ['orden' => 11, 'id' => 'sistema_freno', 'titulo' => 'Sistema de freno', 'estado' => true],
                ['orden' => 12, 'id' => 'pernos_neumaticos', 'titulo' => 'Pernos de neumáticos', 'estado' => true],
                ['orden' => 13, 'id' => 'nivel_aceite', 'titulo' => 'Nivel de aceite', 'estado' => true],
                ['orden' => 14, 'id' => 'espejos_int_ext', 'titulo' => 'Espejos int y ext', 'estado' => true],
                ['orden' => 15, 'id' => 'parachoques', 'titulo' => 'Parachoques', 'estado' => true],
                ['orden' => 16, 'id' => 'parabrisas_ventanas', 'titulo' => 'Parabrisas y ventanas', 'estado' => true],
                ['orden' => 17, 'id' => 'puertas_cabina', 'titulo' => 'Puertas de cabina', 'estado' => true],
                ['orden' => 18, 'id' => 'puertas_tolva', 'titulo' => 'Puertas de tolva', 'estado' => true],
                ['orden' => 19, 'id' => 'plumillas', 'titulo' => 'Plumillas', 'estado' => true],
                ['orden' => 20, 'id' => 'estado_carroceria', 'titulo' => 'Estado de carrocería', 'estado' => true],

            ],
            'accesorios' => [
                ['orden' => 1, 'id' => 'estuche_herramientas', 'titulo' => 'Estuche de herramientas', 'estado' => true],
                ['orden' => 2, 'id' => 'estado_carga_extintor', 'titulo' => 'Estado y carga de extintor', 'estado' => true],
                ['orden' => 3, 'id' => 'botiquin', 'titulo' => 'Botiquín', 'estado' => true],
                ['orden' => 4, 'id' => 'cable_remolque', 'titulo' => 'Cable de remolque', 'estado' => true],
                ['orden' => 5, 'id' => 'tacos_seguridad_cuña_2', 'titulo' => 'Tacos de seguridad cuña(2)', 'estado' => true],
                ['orden' => 6, 'id' => 'llave_ruedas', 'titulo' => 'Llave de ruedas', 'estado' => true],
                ['orden' => 7, 'id' => 'kit_antiderrames', 'titulo' => 'Kit antiderrames', 'estado' => true],
                ['orden' => 8, 'id' => 'limpieza_unidad', 'titulo' => 'Limpieza de la unidad', 'estado' => true],
            ],
            'documentos' => [
                ['orden' => 1,'id' => 'tarjeta_propiedad', 'titulo' => 'Tarjeta de propiedad', 'estado' => true],
                ['orden' => 2,'id' => 'soat', 'titulo' => 'SOAT', 'estado' => true],
                ['orden' => 3,'id' => 'licencia_conducir', 'titulo' => 'Licencia de conducir', 'estado' => true],
                ['orden' => 4,'id' => 'revision_tecnica', 'titulo' => 'Revisión técnica', 'estado' => true],
            ],
            'observaciones' => 'Los faroles delanteras necesitan una buena limpieza',
            'created_at' => $now,
        ]);
        Checklistvehicular::create([
            'fecha_registro' => $now,
            'equipo_id' => 2,
            'k_inicial' => 100.00,
            'k_final' => 220.00,
            'lider_area' => 'JOSE ESTEFAN HURTADO MONTERO',
            'conductor_id' => 2,
            'concesionaria_id' => 2,
            'sistema_electrico' => [
                ['orden' => 1,'id' => 'freno_emergencia', 'titulo' => 'Freno de emergencia', 'estado' => true],
                ['orden' => 2,'id' => 'funcionamiento_tablero', 'titulo' => 'Funcionamiento de tablero','estado' => false],
                ['orden' => 3,'id' => 'estado_bateria_funcionamiento', 'titulo' => 'Estado de batería y funcionamiento', 'estado' => false],
                ['orden' => 4,'id' => 'funcionamiento_claxon', 'titulo' => 'Funcionamiento de claxon', 'estado' => false],
                ['orden' => 5,'id' => 'luces_retroceso_pirata', 'titulo' => 'Luces de retroceso pirata','estado' => false],
                ['orden' => 6,'id' => 'luces_direccional', 'titulo' => 'Luces direccional','estado' => false],
                ['orden' => 7,'id' => 'faros_neblineros', 'titulo' => 'Faros neblineros','estado' => false],
                ['orden' => 8,'id' => 'faros_delanteros', 'titulo' => 'Faros delanteros','estado' => false],
                ['orden' => 9,'id' => 'faros_posteriores', 'titulo' => 'Faros posteriores','estado' => false],
                ['orden' => 10,'id' => 'alarma_retroceso', 'titulo' => 'Alarma de retroceso','estado' => false],
            ],
            'sistema_mecanico' => [
                ['orden' => 1, 'id' => 'nivel_liquido_freno', 'titulo' => 'Nivel liquido de freno', 'estado' => true],
                ['orden' => 2, 'id' => 'sistema_direccion', 'titulo' => 'Sistema de dirección', 'estado' => true],
                ['orden' => 3, 'id' => 'palancas_cambios', 'titulo' => 'Palancas de cambios', 'estado' => true],
                ['orden' => 4, 'id' => 'estado_neumaticos', 'titulo' => 'Estado de neumáticos', 'estado' => true],
                ['orden' => 5, 'id' => 'llantas_repuesto', 'titulo' => 'Llantas de repuesto', 'estado' => false],
                ['orden' => 6, 'id' => 'ajustes_tuercas', 'titulo' => 'Ajustes de tuercas', 'estado' => false],
                ['orden' => 7, 'id' => 'presion_llantas_libras', 'titulo' => 'Presion de llantas en libras', 'estado' => false],
                ['orden' => 8, 'id' => 'cinturon_seguridad_conductor', 'titulo' => 'Cinturon de seguridad conductor', 'estado' => true],
                ['orden' => 9, 'id' => 'cinturon_seguridad_pasajeros', 'titulo' => 'Cinturon de seguridad pasajeros', 'estado' => true],
                ['orden' => 10, 'id' => 'suspension', 'titulo' => 'Suspensión', 'estado' => true],

                ['orden' => 11, 'id' => 'sistema_freno', 'titulo' => 'Sistema de freno', 'estado' => true],
                ['orden' => 12, 'id' => 'pernos_neumaticos', 'titulo' => 'Pernos de neumáticos', 'estado' => true],
                ['orden' => 13, 'id' => 'nivel_aceite', 'titulo' => 'Nivel de aceite', 'estado' => true],
                ['orden' => 14, 'id' => 'espejos_int_ext', 'titulo' => 'Espejos int y ext', 'estado' => true],
                ['orden' => 15, 'id' => 'parachoques', 'titulo' => 'Parachoques', 'estado' => true],
                ['orden' => 16, 'id' => 'parabrisas_ventanas', 'titulo' => 'Parabrisas y ventanas', 'estado' => true],
                ['orden' => 17, 'id' => 'puertas_cabina', 'titulo' => 'Puertas de cabina', 'estado' => true],
                ['orden' => 18, 'id' => 'puertas_tolva', 'titulo' => 'Puertas de tolva', 'estado' => true],
                ['orden' => 19, 'id' => 'plumillas', 'titulo' => 'Plumillas', 'estado' => true],
                ['orden' => 20, 'id' => 'estado_carroceria', 'titulo' => 'Estado de carrocería', 'estado' => true],

            ],
            'accesorios' => [
                ['orden' => 1, 'id' => 'estuche_herramientas', 'titulo' => 'Estuche de herramientas', 'estado' => true],
                ['orden' => 2, 'id' => 'estado_carga_extintor', 'titulo' => 'Estado y carga de extintor', 'estado' => true],
                ['orden' => 3, 'id' => 'botiquin', 'titulo' => 'Botiquín', 'estado' => true],
                ['orden' => 4, 'id' => 'cable_remolque', 'titulo' => 'Cable de remolque', 'estado' => true],
                ['orden' => 5, 'id' => 'tacos_seguridad_cuña_2', 'titulo' => 'Tacos de seguridad cuña(2)', 'estado' => true],
                ['orden' => 6, 'id' => 'llave_ruedas', 'titulo' => 'Llave de ruedas', 'estado' => true],
                ['orden' => 7, 'id' => 'kit_antiderrames', 'titulo' => 'Kit antiderrames', 'estado' => true],
                ['orden' => 8, 'id' => 'limpieza_unidad', 'titulo' => 'Limpieza de la unidad', 'estado' => true],
            ],
            'documentos' => [
                ['orden' => 1,'id' => 'tarjeta_propiedad', 'titulo' => 'Tarjeta de propiedad', 'estado' => true],
                ['orden' => 2,'id' => 'soat', 'titulo' => 'SOAT', 'estado' => true],
                ['orden' => 3,'id' => 'licencia_conducir', 'titulo' => 'Licencia de conducir', 'estado' => true],
                ['orden' => 4,'id' => 'revision_tecnica', 'titulo' => 'Revisión técnica', 'estado' => true],
            ],
            'observaciones' => 'Necesita un cambio en los cinturones de seguridad traseras',
            'created_at' => $now,
        ]);
    }
}
