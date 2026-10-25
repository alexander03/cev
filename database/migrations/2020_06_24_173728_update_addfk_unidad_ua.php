<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateAddfkUnidadUa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('ua', function (Blueprint $table) {
            
        //     $table -> foreign('unidad_id') -> references('id') -> on('unidad') -> onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ua', function (Blueprint $table) {
            
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            //$table -> dropForeign('unidad_id');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}
