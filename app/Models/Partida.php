<?php
// app/Models/Partida.php
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
        'creada_en',
    ];

    protected $casts = [
        'creada_en' => 'datetime',
    ];

    
    public function ganador()
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }

   
    public function jugadores() 
    {
        return $this->hasMany(JugadorPartida::class, 'id_partida');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'jugadores_partida',
            'id_partida',
            'id_usuario' 
        );
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_partida');
    }

   public function barcos()
{
    return $this->hasManyThrough(
        Barco::class, 
        JugadorPartida::class, 
        'id_partida', 
        'id_jugador_partida', 
        'id', 
        'id' 
    );
}

    

    
}