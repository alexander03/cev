<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculo', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->string('ua',10);
            $table->string('placa',15);
//            $table->string('unidad',25);
            $table->string('motor',20);
            $table->string('modelo',20);
            $table->integer('asientos');
            $table->year('anio');
            $table->unsignedBigInteger('marca_id');
            $table->foreign('marca_id')->references('id')->on('marca');
            $table->unsignedBigInteger('ua_id');
            $table->foreign('ua_id')->references('id')->on('ua');
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('area');
            $table->unsignedBigInteger('contratista_id');
            $table->foreign('contratista_id')->references('id')->on('contratista');
            $table->string('chasis',20);
            $table->float('kilometraje_ini',6,2);
            $table->float('kilometraje_act',6,2);
            $table->float('kilometraje_rec',6,2)->nullable();
            $table->unsignedBigInteger('carroceria_id');
//            $table->boolean('carroceria');
            $table->string('color',20);
            $table->unsignedBigInteger('concesionaria_id');
/*
            $table->date('fechavencimientosoat')->nullable();
            $table->date('fechavencimientogps')->nullable();
            $table->date('fechavencimientortv')->nullable();
*/
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
        Schema::dropIfExists('vehiculo');
    }
}
