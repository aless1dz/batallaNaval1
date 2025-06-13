<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\JugadorPartida;
use App\Models\Barco;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PartidaController extends Controller
{
    public function index()
    {
        $partidas = Partida::with(['usuarios', 'ganador'])
            ->whereHas('jugadores', function ($query) {
                $query->where('id_usuario', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('BatallaNaval/Index', [
            'partidas' => $partidas
        ]);
    }

    public function create()
    {
        return Inertia::render('Partidas/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string|max:1000',
        ]);

            $partida = Partida::create([
                'nombre' => $request->input('nombre', 'Partida de ' . Auth::user()->name),
                'descripcion' => $request->input('descripcion', ''),
                'estado' => 'esperando'
            ]);

            $jugadorPartida = JugadorPartida::create([
                'id_usuario' => Auth::id(),
                'id_partida' => $partida->id,
                'es_turno' => false
            ]);

        return redirect()->route('partidas.espera', $partida->id)->with('success', 'Partida creada exitosamente.');
    }

    public function SalaEspera($id) {
        $partida = Partida::findOrFail($id);

        $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        $totalJugadores = JugadorPartida::where('id_partida', $partida->id)->count();
        if ($totalJugadores >= 2) {
            return redirect()->route('dashboard', $id);
        }

        return Inertia::render('Partidas/SalaEspera', [
            'partida' => $partida,
            'jugadorActual' => $jugadorActual,
            'totalJugadores' => $totalJugadores
        ]);
    }

    public function verificarEstado($id) {

        $partida = Partida::findOrFail($id);
        $totalJugadores = JugadorPartida::where('id_partida', $partida->id)->count();

        return response()->json([
            'estado' => $partida->estado,
            'totalJugadores' => $totalJugadores,
            'puedeIniciar' => $totalJugadores >= 2
        ]);
    }

    public function show($id)
    {
        $partida = Partida::with([
            'jugadores.usuario',
            'jugadores.barcos',
            'movimientos.atacante.usuario',
            'movimientos.defensor.usuario'
        ])->findOrFail($id);

        $jugadorActual = $partida->obtenerJugador(Auth::id());
        $rival = $partida->obtenerRival(Auth::id());

        $misBarcos = $jugadorActual ? $jugadorActual->barcos->pluck('coordenada')->toArray() : [];
        
        $misMovimientos = $partida->movimientos()
            ->where('id_atacante', $jugadorActual?->id)
            ->get()
            ->map(function ($mov) {
                return [
                    'coordenada' => $mov->coordenada,
                    'acierto' => $mov->acierto
                ];
            })
            ->toArray();

        return Inertia::render('Partidas/Show', [
            'partida' => $partida,
            'jugadorActual' => $jugadorActual,
            'rival' => $rival,
            'misBarcos' => $misBarcos,
            'misMovimientos' => $misMovimientos,
            'esmiTurno' => $jugadorActual?->es_turno ?? false
        ]);
    }
}