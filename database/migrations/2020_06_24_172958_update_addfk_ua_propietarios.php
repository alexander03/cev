<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateAddfkUaPropietarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('propietarios', function (Blueprint $table) {
            
            $table -> foreign('ua_id') -> references('id')-> on('ua') -> onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('propietarios', function (Blueprint $table) {
            
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            // $table -> dropForeign('ua_id');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}
