<?php
// database/migrations/2024_01_01_000003_create_jugadores_partida_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jugadores_partida', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_partida');
            $table->boolean('es_turno')->default(false);
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamps();
            
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_partida')->references('id')->on('partidas')->onDelete('cascade');
            $table->unique(['id_usuario', 'id_partida']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jugadores_partida');
    }
};