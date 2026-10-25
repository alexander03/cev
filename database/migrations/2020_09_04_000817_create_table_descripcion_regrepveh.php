<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateTableDescripcionRegRepVeh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descripcionregrepveh', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('regrepveh_id');
            $table->string('cantidad');
            $table->unsignedBigInteger('repuesto_id');
            $table->integer('monto');
            $table->foreign('repuesto_id')->references('id')->on('repuesto');
            $table->foreign('regrepveh_id')->references('id')->on('regrepveh');
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
        Schema::dropIfExists('descripcionregrepveh');
    }
}