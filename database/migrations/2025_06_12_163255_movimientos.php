<?php
// database/migrations/2024_01_01_000005_create_movimientos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_partida');
            $table->unsignedBigInteger('id_atacante');
            $table->unsignedBigInteger('id_defensor');
            $table->string('coordenada'); 
            $table->boolean('acierto')->default(false);
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamps();
            
            $table->foreign('id_partida')->references('id')->on('partidas')->onDelete('cascade');
            $table->foreign('id_atacante')->references('id')->on('jugadores_partida')->onDelete('cascade');
            $table->foreign('id_defensor')->references('id')->on('jugadores_partida')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos');
    }
};