<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKilometrajeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kilometraje', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion')->unique();
            $table->decimal('limite_inf', 10,2);
            $table->decimal('limite_sup', 10,2);
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
        Schema::dropIfExists('kilometraje');
    }
}
