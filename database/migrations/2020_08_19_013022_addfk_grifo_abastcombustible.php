<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddfkGrifoAbastcombustible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abastecimiento_combustible', function (Blueprint $table) {
        
            $table->foreign('grifo_id')->references('id')->on('grifo');
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
