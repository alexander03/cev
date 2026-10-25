<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControldiarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controldiario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('equipo_id')->nullable();
            $table->foreign('equipo_id')->references('id')->on('equipo');
            $table->unsignedBigInteger('ua_id')->nullable();
            $table->foreign('ua_id')->references('id')->on('ua');
            $table->boolean('turno');
            $table->float('horometro_inicial',6,2);
            $table->float('horometro_final',6,2);        
//            $table->time('hora_inicio');
//            $table->time('hora_fin');
            $table->float('hora_total',4,2);
            $table->unsignedBigInteger('tipohora_id')->nullable();
            $table->foreign('tipohora_id')->references('id')->on('tipohora');
            $table->float('hora_parada',4,2)->nullable();
            $table->integer('viajes')->nullable();
            $table->integer('km_inicio')->nullable();
            $table->integer('acceso_origen')->nullable();
//            $table->unsignedBigInteger('uaorigen_id')->nullable();
//            $table->foreign('uaorigen_id')->references('id')->on('ua');
            $table->integer('acceso_destino')->nullable();
//            $table->unsignedBigInteger('uadestino_id')->nullable();
//            $table->foreign('uadestino_id')->references('id')->on('ua');
            // $table->float('horometro_inicial')->nullable();
            // $table->float('horometro_final')->nullable();
            $table->string('tipo_material')->nullable();
            $table->date('fecha');
            $table->longText('observaciones')->nullable();
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
        Schema::dropIfExists('controldiario');
    }
}
