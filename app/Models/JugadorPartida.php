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
        'id_usuario',
        'id_partida',
        'es_turno',
    ];

    protected $casts = [
        'es_turno' => 'boolean',
        'creado_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
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