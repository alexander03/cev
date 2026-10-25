<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbastecimientoCombustiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abastecimiento_combustible', function (Blueprint $table) {
            $table -> bigIncrements('id');
            $table -> date('fecha_abastecimiento');
            $table -> unsignedBigInteger('grifo_id');
            // $table -> unsignedBigInteger('conductor_id') -> nullable();
            $table -> unsignedBigInteger('user_id') -> nullable();
            $table -> string('conductor_fake') -> nullable();
            $table -> unsignedBigInteger('ua_id');
            $table -> unsignedBigInteger('equipo_id') -> nullable();
            $table -> unsignedBigInteger('vehiculo_id') -> nullable();
            $table -> float('qtdgl');
            $table -> float('qtdl');
            $table -> float('km');
            $table -> float('abastecimiento_dia');
            $table -> text('motivo');
            $table -> string('comprobante');
            $table -> string('numero_comprobante');
            $table -> time('hora_inicio');
            $table -> time('hora_fin');
            $table->boolean('especial')->default(0);
            $table -> unsignedBigInteger('abastecimiento_id');
            $table -> unsignedBigInteger('tipocombustible_id');
            $table -> timestamps();
            $table -> softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        Schema::dropIfExists('abastecimiento_combustible');
    }
}
