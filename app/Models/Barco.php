<?php
// app/Models/Barco.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barco extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jugador_partida',
        'coordenada',
        'hundido',
    ];

    protected $casts = [
        'hundido' => 'boolean',
        'creado_en' => 'datetime',
    ];

    // Relaciones
    public function jugadorPartida()
    {
        return $this->belongsTo(JugadorPartida::class, 'id_jugador_partida');
    }

    // Métodos personalizados
    public function hundir()
    {
        $this->hundido = true;
        $this->save();
    }
}