<?php
// app/Models/JugadorPartida.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JugadorPartida extends Model
{
    use HasFactory;

    protected $table = 'jugadores_partida';
    
    protected $fillable = [
        'id_user',
        'id_partida',
        'es_turno',
    ];

    protected $casts = [
        'es_turno' => 'boolean',
        'creado_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user');
    }

    public function partida()
    {
        return $this->belongsTo(Partida::class, 'id_partida');
    }

    public function barcos()
    {
        return $this->hasMany(Barco::class, 'id_jugador_partida');
    }

    public function movimientosAtacante()
    {
        return $this->hasMany(Movimiento::class, 'id_atacante');
    }

    public function movimientosDefensor()
    {
        return $this->hasMany(Movimiento::class, 'id_defensor');
    }

    public function generarTablero()
    {
        $coordenadas = [];
        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $numeros = [1, 2, 3, 4, 5, 6, 7, 8];

       
        foreach ($letras as $letra) {
            foreach ($numeros as $numero) {
                $coordenadas[] = $letra . $numero;
            }
        }

        
        $barcosCoords = array_slice(array_shuffle($coordenadas), 0, 15);
        
        foreach ($barcosCoords as $coord) {
            Barco::create([
                'id_jugador_partida' => $this->id,
                'coordenada' => $coord,
                'hundido' => false
            ]);
        }
    }

    public function cambiarTurno()
    {
        $this->es_turno = false;
        $this->save();

       
        $rival = $this->partida->jugadores()
            ->where('id', '!=', $this->id)
            ->first();
        
        if ($rival) {
            $rival->es_turno = true;
            $rival->save();
        }
    }

    public function todosLosBarcosHundidos()
    {
        return $this->barcos()->where('hundido', false)->count() === 0;
    }
}