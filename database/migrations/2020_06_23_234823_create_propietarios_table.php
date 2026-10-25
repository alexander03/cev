<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePropietariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propietarios', function (Blueprint $table) {
            //$table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->text('descripcion');
            $table->date('fecha_llegada');
            $table->date('fecha_salida');
            $table->date('fecha_contrato');
            $table->string('status');
            $table->string('hra');
            $table->string('hrb');
            $table->string('hrc');
            $table->float('km');
            $table->text('observacion');
            $table->string('ubicacion');
            $table->unsignedBigInteger('ua_id');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('propietarios');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
