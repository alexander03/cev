<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistvehicularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklistvehicular', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_registro');
            $table->unsignedBigInteger('equipo_id')->nullable();
            $table->foreign('equipo_id')->references('id')->on('equipo');
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->foreign('vehiculo_id')->references('id')->on('vehiculo');
            $table->decimal('k_inicial', 11, 2);
            $table->decimal('k_final', 11, 2);
            $table->string('lider_area');
            $table->unsignedBigInteger('conductor_id');
            $table->foreign('conductor_id')->references('id')->on('conductor');
            $table->unsignedBigInteger('concesionaria_id');
            $table->foreign('concesionaria_id')->references('id')->on('concesionaria');
            
            $table->longText('sistema_electrico')->nullable();
            $table->longText('sistema_mecanico')->nullable();
            $table->longText('accesorios')->nullable();
            $table->longText('documentos')->nullable();

            $table->boolean('especial')->default(0);
            $table->string('observaciones')->nullable();

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
        Schema::dropIfExists('checklistvehicular');
    }
}
