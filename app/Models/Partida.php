<?php
// app/Models/Partida.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'ganador_id',
    ];

    protected $casts = [
        'creada_en' => 'datetime',
    ];

    // Relaciones
    public function ganador()
    {
        return $this->belongsTo(Usuario::class, 'ganador_id');
    }

    public function jugadores()
    {
        return $this->hasMany(JugadorPartida::class, 'id_partida');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'jugadores_partida', 'id_partida', 'id_usuario');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_partida');
    }

    // Métodos personalizados
    public function puedeUnirse()
    {
        return $this->jugadores()->count() < 2 && $this->estado === 'esperando';
    }

    public function iniciar()
    {
        if ($this->jugadores()->count() === 2) {
            $this->estado = 'en_progreso';
            
            // Asignar turno al primer jugador
            $primerJugador = $this->jugadores()->first();
            $primerJugador->es_turno = true;
            $primerJugador->save();
            
            $this->save();
            return true;
        }
        return false;
    }

    public function finalizarPartida($ganadorId)
    {
        $this->estado = 'finalizada';
        $this->ganador_id = $ganadorId;
        $this->save();
    }

    public function obtenerRival($usuarioId)
    {
        return $this->jugadores()
            ->where('id_usuario', '!=', $usuarioId)
            ->with('usuario')
            ->first();
    }

    public function obtenerJugador($usuarioId)
    {
        return $this->jugadores()
            ->where('id_usuario', $usuarioId)
            ->first();
    }
}