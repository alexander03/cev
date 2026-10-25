<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkTipoCombustibleAbastecimientoCombustible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abastecimiento_combustible', function (Blueprint $table) {
            $table->foreign('tipocombustible_id')->references('id')->on('abastecimiento');
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
            $table->dropForeign('abastecimiento_combustible_tipocombustible_id_foreign');
        });
    }
}
