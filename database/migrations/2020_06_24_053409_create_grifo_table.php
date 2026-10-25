<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrifoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grifo', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('descripcion',25);
            $table->string('ubicacion',25);
            $table->string('contacto', 30);
            $table->string('telefono', 9);
            $table->string('correo', 25);
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
        Schema::dropIfExists('grifo');
    }
}
