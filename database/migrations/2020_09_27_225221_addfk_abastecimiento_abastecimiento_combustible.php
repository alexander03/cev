<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkAbastecimientoAbastecimientoCombustible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abastecimiento_combustible', function (Blueprint $table) {
            $table->foreign('abastecimiento_id')->references('id')->on('abastecimiento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('abastecimiento_combustible', function (Blueprint $table) {
            $table->dropForeign('abastecimiento_combustible_abastecimiento_id_foreign');
        });
    }
}
