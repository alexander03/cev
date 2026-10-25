<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDescripcionregmanveh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descripcionregmanveh', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('regmanveh_id');
            $table->string('cantidad');
            $table->unsignedBigInteger('trabajo_id');
            $table->integer('monto');
            $table->foreign('trabajo_id')->references('id')->on('trabajo');
            $table->foreign('regmanveh_id')->references('id')->on('regmanveh');
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
        Schema::dropIfExists('descripcionregmanveh');
    }
}