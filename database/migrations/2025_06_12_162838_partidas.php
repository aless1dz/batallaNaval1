<?php
// database/migrations/2024_01_01_000002_create_partidas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['esperando', 'en_progreso', 'finalizada'])->default('esperando');
            $table->unsignedBigInteger('ganador_id')->nullable();
            $table->timestamp('creada_en')->useCurrent();
            $table->timestamps();
            
            $table->foreign('ganador_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('partidas');
    }
};
