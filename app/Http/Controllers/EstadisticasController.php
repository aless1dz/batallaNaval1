<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partida;
use Inertia\Inertia;
use App\Models\Movimiento;
use App\Models\JugadorPartida;
use App\Models\Barco;

class EstadisticasController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();

        
        $ganadas = Partida::where('estado', 'finalizada')
            ->where('ganador_id', $usuarioId)
            ->whereHas('jugadores', function($q) use ($usuarioId) {
                $q->where('id_usuario', $usuarioId);
            })
            ->get();

        
        $perdidas = Partida::where('estado', 'finalizada')
            ->whereHas('jugadores', function($q) use ($usuarioId) {
                $q->where('id_usuario', $usuarioId);
            })
            ->where('ganador_id', '!=', $usuarioId)
            ->get();

        return Inertia::render('Estadisticas/Index', [
            'ganadas' => $ganadas->count(),
            'perdidas' => $perdidas->count(),
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

    public function detalle($id, Request $request)
    {
        $from = $request->input('from', 'mis-partidas'); 

        $partida = Partida::with([
            'jugadores.usuario',
            'jugadores.barcos',
            'jugadores.movimientosAtacante' => function($q) use ($id) {
                $q->where('id_partida', $id);
            },
            'jugadores.movimientosDefensor' => function($q) use ($id) {
                $q->where('id_partida', $id);
            },
        ])->findOrFail($id);

        $movimientos = Movimiento::where('id_partida', $id)
            ->with(['atacante.usuario', 'defensor.usuario'])
            ->get();

        return Inertia::render('Estadisticas/DetallePartidas', [
            'partida' => $partida,
            'movimientos' => $movimientos,
            'from' => $from, 
        ]);
    }
}
