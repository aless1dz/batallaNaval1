<?php
// database/migrations/2024_01_01_000004_create_barcos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('barcos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jugador_partida');
            $table->string('coordenada'); // Ejemplo: "A3"
            $table->boolean('hundido')->default(false);
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamps();
            
            $table->foreign('id_jugador_partida')->references('id')->on('jugadores_partida')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barcos');
    }
};