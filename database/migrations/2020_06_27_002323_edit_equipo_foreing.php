<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditEquipoForeing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipo', function (Blueprint $table) {

            $table->foreign('marca_id')->references('id')->on('marca')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('contratista_id')->references('id')->on('contratista')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('area_id')->references('id')->on('area');
            $table->foreign('ua_id')->references('id')->on('ua')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   

        Schema::table('equipo', function (Blueprint $table) {

            $table->dropForeign('equipo_marca_id_foreign');
            $table->dropForeign('equipo_contratista_id_foreign');
            $table->dropForeign('equipo_area_id_foreign');
            $table->dropForeign('equipo_ua_id_foreign');
        });
    }
}
