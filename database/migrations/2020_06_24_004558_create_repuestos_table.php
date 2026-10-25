<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repuesto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 7)->unique();
            $table->string('descripcion')->unique();
            $table->unsignedBigInteger('unidad_id');
            // $table->foreign('unidad_id')->references('id')->on('unidad');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repuesto');
    }
}
