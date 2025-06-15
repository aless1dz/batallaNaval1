<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partida;
use Inertia\Inertia;

class EstadisticasController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $ganadas = Partida::where('ganador_id', $userId)->count();

        $perdidas = Partida::whereHas('jugadores', function($q) use ($userId) {
            $q->where('id_usuario', $userId);
        })
        ->where('ganador_id', '!=', $userId)
        ->count();

        return Inertia::render('Estadisticas/Index', [
            'ganadas' => $ganadas,
            'perdidas' => $perdidas,
        ]);
    }

    public function partidas($tipo)
    {
        $userId = Auth::id();
        if ($tipo === 'ganadas') {
            $partidas = Partida::where('ganador_id', $userId)->get();
        } else {
            $partidas = Partida::whereHas('jugadores', function($q) use ($userId) {
                $q->where('id_usuario', $userId);
            })
            ->where('ganador_id', '!=', $userId)
            ->get();
        }
        return Inertia::render('Estadisticas/Partidas', [
            'partidas' => $partidas,
            'tipo' => $tipo,
        ]);
    }

    public function detalle($id)
{
    $partida = Partida::with([
        'jugadores.usuario',
        'jugadores.barcos',
        // Todos los movimientos donde el jugador fue atacante o defensor en esta partida
        'jugadores.movimientosAtacante' => function($q) use ($id) {
            $q->where('id_partida', $id);
        },
        'jugadores.movimientosDefensor' => function($q) use ($id) {
            $q->where('id_partida', $id);
        },
    ])->findOrFail($id);

    // También puedes traer todos los movimientos de la partida si quieres mostrarlos juntos
    $movimientos = Movimiento::where('id_partida', $id)
        ->with(['atacante.usuario', 'defensor.usuario'])
        ->get();

    return inertia('Estadisticas/Detalle', [
        'partida' => $partida,
        'movimientos' => $movimientos,
    ]);
}
}
