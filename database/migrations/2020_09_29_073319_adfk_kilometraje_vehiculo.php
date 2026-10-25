<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdfkKilometrajeVehiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehiculo', function (Blueprint $table) {
            $table->unsignedBigInteger('kilometraje_id');
            $table->foreign('kilometraje_id')->references('id')->on('kilometraje');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehiculo', function (Blueprint $table) {
            $table->dropForeign('grifo_kilometraje_id_foreign');
        });
    }
}
