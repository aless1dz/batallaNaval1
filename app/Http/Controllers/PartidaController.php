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

        return Inertia::render('Partidas/Index', [
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
        ]);

        $partida = Partida::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'estado' => 'esperando'
        ]);

        $jugadorPartida = JugadorPartida::create([
            'id_usuario' => Auth::id(),
            'id_partida' => $partida->id,
            'es_turno' => false
        ]);

        return redirect()->route('partidas.show', $partida->id);
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

    public function unirse($id)
    {
        $partida = Partida::findOrFail($id);

        if (!$partida->puedeUnirse()) {
            return back()->withErrors(['error' => 'No se puede unir a esta partida']);
        }

        $jugadorPartida = JugadorPartida::create([
            'id_usuario' => Auth::id(),
            'id_partida' => $partida->id,
            'es_turno' => false
        ]);

        $jugadorPartida->generarTablero();

        $partida->iniciar();

        return redirect()->route('partidas.show', $partida->id);
    }

    public function atacar(Request $request, $id)
    {
        $request->validate([
            'coordenada' => 'required|string'
        ]);

        $partida = Partida::findOrFail($id);
        $jugadorActual = $partida->obtenerJugador(Auth::id());
        $rival = $partida->obtenerRival(Auth::id());

        if (!$jugadorActual?->es_turno || $partida->estado !== 'en_progreso') {
            return response()->json(['error' => 'No es tu turno'], 403);
        }

        $yaAtacado = Movimiento::where('id_partida', $partida->id)
            ->where('id_atacante', $jugadorActual->id)
            ->where('coordenada', $request->coordenada)
            ->exists();

        if ($yaAtacado) {
            return response()->json(['error' => 'Ya atacaste esta coordenada'], 400);
        }

        $barco = Barco::where('id_jugador_partida', $rival->id)
            ->where('coordenada', $request->coordenada)
            ->first();

        $acierto = $barco !== null;

        Movimiento::create([
            'id_partida' => $partida->id,
            'id_atacante' => $jugadorActual->id,
            'id_defensor' => $rival->id,
            'coordenada' => $request->coordenada,
            'acierto' => $acierto
        ]);

        if ($acierto) {
            $barco->hundir();
            
            if ($rival->todosLosBarcosHundidos()) {
                $partida->finalizarPartida(Auth::id());
                return response()->json([
                    'acierto' => true,
                    'ganaste' => true,
                    'coordenada' => $request->coordenada
                ]);
            }
        }

        $jugadorActual->cambiarTurno();

        return response()->json([
            'acierto' => $acierto,
            'coordenada' => $request->coordenada,
            'ganaste' => false
        ]);
    }

    public function partidasDisponibles()
    {
        $partidas = Partida::where('estado', 'esperando')
            ->whereDoesntHave('jugadores', function ($query) {
                $query->where('id_usuario', Auth::id());
            })
            ->with(['jugadores.usuario'])
            ->get();

        return Inertia::render('Partidas/Disponibles', [
            'partidas' => $partidas
        ]);
    }

    public function estadoPartida($id)
    {
        $partida = Partida::with([
            'jugadores.usuario',
            'movimientos' => function ($query) {
                $query->latest()->limit(1);
            }
        ])->findOrFail($id);

        $jugadorActual = $partida->obtenerJugador(Auth::id());

        return response()->json([
            'estado' => $partida->estado,
            'es_mi_turno' => $jugadorActual?->es_turno ?? false,
            'ultimo_movimiento' => $partida->movimientos->first(),
            'ganador_id' => $partida->ganador_id
        ]);
    }
}