<?php
// app/Models/Movimiento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_partida',
        'id_atacante',
        'id_defensor',
        'coordenada',
        'acierto',
    ];

    protected $casts = [
        'acierto' => 'boolean',
        'creado_en' => 'datetime',
    ];

    // Relaciones
    public function partida()
    {
        return $this->belongsTo(Partida::class, 'id_partida');
    }

    public function atacante()
    {
        return $this->belongsTo(JugadorPartida::class, 'id_atacante');
    }

    public function defensor()
    {
        return $this->belongsTo(JugadorPartida::class, 'id_defensor');
    }
}