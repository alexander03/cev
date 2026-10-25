<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRegrepveh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regrepveh', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('concesionaria_id');;
            $table->string('cliente',250);
            $table->string('ordencompra',250);
            $table->string('ua_id');
            $table->integer('kmman');
            $table->integer('kminicial');
            $table->integer('kmfinal');
            $table->date('fechaentrada')->nullable(); 
            $table->date('fechasalida')->nullable();  
            $table->integer('tipomantenimiento');
            $table->integer('telefono');
            $table->boolean('especial')->default(0);
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
        Schema::dropIfExists('regrepveh');
    }
}
