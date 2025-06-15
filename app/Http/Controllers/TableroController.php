<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\JugadorPartida;
use App\Models\Barco;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TableroController extends Controller
{
    public function tablero($id)
    {
        $partida = Partida::findOrFail($id);

        $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        if (!$jugadorActual) {
            return redirect()->route('partidas.index')
                ->withErrors(['error' => 'No tienes acceso a esta partida.']);
        }

       
        if ($partida->estado !== 'en_curso') {
            return redirect()->route('partidas.espera', $id)
                ->with('info', 'La partida aún no ha comenzado.');
        }

        $jugadores = JugadorPartida::where('id_partida', $partida->id)
            ->with('usuario:id,name,email')
            ->get();

        if ($jugadores->count() < 2) {
            return redirect()->route('partidas.espera', $id)
                ->with('info', 'Esperando que se una otro jugador.');
        }

        $oponente = $jugadores->where('id_usuario', '!=', Auth::id())->first();

        return Inertia::render('Juegos/Tablero', [
            'partida' => $partida,
            'jugadorActual' => $jugadorActual,
            'oponente' => $oponente,
            'jugadores' => $jugadores,
            'barcos' => $partida->barcos,
            'movimientos' => Movimiento::where('id_partida', $partida->id)->get(),
        ]);
    }
}