<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipohoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipohora', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
            $table->bigIncrements('id');
//            $table->integer('codigo');
            $table->string('codigo',2);
            $table->boolean('prioridad')->default(false);
            $table->string('descripcion',25);
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
        Schema::dropIfExists('tipohora');
    }
}
