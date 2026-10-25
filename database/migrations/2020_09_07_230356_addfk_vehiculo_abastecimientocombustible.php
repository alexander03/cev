<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkVehiculoAbastecimientocombustible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abastecimiento_combustible', function (Blueprint $table) {
            
            $table->foreign('vehiculo_id')->references('id')->on('vehiculo');
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
            //
        });
    }
}
