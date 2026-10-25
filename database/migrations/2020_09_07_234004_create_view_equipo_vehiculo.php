<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewEquipoVehiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        $query = "CREATE VIEW view_equipo_vehiculo AS
            SELECT eq.id, eq.descripcion, modelo, placa, marca_id, 
                area_id, contratista_id, anio, 'e' as 'tipo', eq.concesionaria_id, ua.codigo as 'ua', ua.descripcion as 'ua_desc',
                eq.deleted_at FROM equipo as eq
            INNER JOIN ua ON ua.id = eq.ua_id
            UNION ALL 
            SELECT ve.id, modelo as 'descripcion', modelo, placa, marca_id, area_id, 
                contratista_id, anio, 'v' as 'tipo', ve.concesionaria_id, ua.codigo as 'ua', ua.descripcion as 'ua_desc',
                ve.deleted_at FROM vehiculo ve
                INNER JOIN ua ON ua.id = ve.ua_id;";

        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        DB::statement("DROP VIEW view_equipo_vehiculo");
    }
}
