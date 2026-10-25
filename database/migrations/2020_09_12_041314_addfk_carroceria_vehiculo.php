<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkCarroceriaVehiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehiculo', function (Blueprint $table) {
             $table->foreign('carroceria_id')->references('id')->on('carroceria');;
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
             $table->dropForeign('vehiculo_carroceria_id_foreign');
        });
    }
}
