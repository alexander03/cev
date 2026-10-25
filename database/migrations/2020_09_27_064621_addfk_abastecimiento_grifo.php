<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkAbastecimientoGrifo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grifo', function (Blueprint $table) {
            $table->unsignedBigInteger('abastecimiento_id');
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
        Schema::table('grifo', function (Blueprint $table) {
            $table->dropForeign('grifo_abastecimiento_id_foreign');
        });
    }
}
