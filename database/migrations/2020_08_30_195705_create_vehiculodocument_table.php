<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculodocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculodocument', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->date('fecha');
            $table->string('tipo',5);
            $table->string('archivo')->nullable();
            $table->boolean('notificacion')->default(0);
//            $table->bigInteger('tipovehiculodocument_id');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculo')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('tipovehiculodocument_id')->references('id')->on('tipovehiculodocument')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('vehiculodocument');
    }
}
