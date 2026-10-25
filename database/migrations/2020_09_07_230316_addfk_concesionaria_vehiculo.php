<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkConcesionariaVehiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehiculo', function (Blueprint $table) {
        
            $table->foreign('concesionaria_id')->references('id')->on('concesionaria');
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
            $table->dropForeign('vehiculo_concesionaria_id_foreign');
        });
    }
}
