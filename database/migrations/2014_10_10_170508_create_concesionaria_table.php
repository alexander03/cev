<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcesionariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concesionaria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ruc', 11)->unique();
            $table->string('razonsocial');
            $table->string('abreviatura',15);
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
        Schema::dropIfExists('concesionaria');
    }
}
