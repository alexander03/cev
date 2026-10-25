<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpcionmenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opcionmenu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion',20);
            $table->string('link',20);
            $table->string('icono',20);
            $table->integer('orden');
            $table->bigInteger('grupomenu_id')->unsigned()->nullable();
            $table->foreign('grupomenu_id')->references('id')->on('grupomenu')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opcionmenu');
    }
}
