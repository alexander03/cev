<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkRepuestosUnidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repuesto', function (Blueprint $table) {
            
            $table->foreign('unidad_id')->references('id')->on('unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repuesto', function (Blueprint $table) {
            //
        });
    }
}
