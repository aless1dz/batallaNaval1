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
        $partida = Partida::findOrFail($id);
        return Inertia::render('Estadisticas/Detalle', [
            'partida' => $partida,
        ]);
    }
}
