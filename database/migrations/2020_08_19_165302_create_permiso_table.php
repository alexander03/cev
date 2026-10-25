<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permiso', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opcionmenu_id');
            $table->foreign('opcionmenu_id')->references('id')->on('opcionmenu');
            $table->unsignedBigInteger('tipouser_id');
            $table->foreign('tipouser_id')->references('id')->on('tipouser');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permiso');
    }
}
