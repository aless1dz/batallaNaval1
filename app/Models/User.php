<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación: Partidas en las que participa el usuario.
     */
    public function partidas()
    {
        return $this->belongsToMany(
            Partida::class,
            'jugadores_partida',
            'id_usuario', // CORREGIDO: era 'id_usuario' no 'id_user'
            'id_partida'
        );
    }

    public function jugadoresPartida()
    {
        return $this->hasMany(JugadorPartida::class, 'id_usuario'); // CORREGIDO: era 'id_usuario' no 'id_user'
    }
}