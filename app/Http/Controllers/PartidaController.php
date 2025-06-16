<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\JugadorPartida;
use App\Models\Barco;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class PartidaController extends Controller
{
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

        
        $this->generarBarcosParaJugador($jugadorPartida->id);

        return redirect()->route('partidas.espera', $partida->id)->with('success', 'Partida creada exitosamente.');
    }



    public function SalaEspera($id) 
    {
        $partida = Partida::findOrFail($id);

        $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        if (!$jugadorActual) {
            return redirect()->route('partidas.index')
                ->withErrors(['error' => 'No tienes acceso a esta partida.']);
        }

        $totalJugadores = JugadorPartida::where('id_partida', $partida->id)->count();
        
       
        if ($totalJugadores >= 2 && $partida->estado === 'en_curso') {
            return redirect()->route('juego.tablero', $id)
                ->with('success', '¡La partida ha comenzado!');
        }

        $jugadores = JugadorPartida::where('id_partida', $partida->id)
            ->with('usuario:id,name,email')
            ->get();

        return Inertia::render('Partidas/SalaEspera', [
            'partida' => $partida,
            'jugadorActual' => $jugadorActual,
            'totalJugadores' => $totalJugadores,
            'jugadores' => $jugadores
        ]);
    }



    public function verificarEstado($id) {
        $partida = Partida::findOrFail($id);
        $totalJugadores = JugadorPartida::where('id_partida', $partida->id)->count();

        
        if ($totalJugadores >= 2 && $partida->estado === 'esperando') {
            $partida->update(['estado' => 'en_curso']);

            $primerJugador = JugadorPartida::where('id_partida', $partida->id)
                ->orderBy('created_at', 'asc')
                ->first();
            if ($primerJugador) {
                $primerJugador->update(['es_turno' => true]);
            }
        }

        return response()->json([
            'estado' => $partida->fresh()->estado, 
            'totalJugadores' => $totalJugadores,
            'puedeIniciar' => $totalJugadores >= 2,
            'debeRedirigir' => $totalJugadores >= 2 && $partida->fresh()->estado === 'en_curso', 
            'urlRedireccion' => route('juego.tablero', $id) 
        ]);
    }

    public function index()
    { 
        $partidas = Partida::where('estado', 'esperando')
            ->with([
                'usuarios' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email');
                }
            ])
            ->withCount('usuarios')
            ->having('usuarios_count', '<', 2)
            ->get();
    
        return Inertia::render('Partidas/Index', [
            'partidas' => $partidas
        ]);
    }

    public function unirse($id) {
        $partida = Partida::findOrFail($id);

        if ($partida->estado !== 'esperando') {
            return redirect()->route('partidas.index')->with('error', 'Esta partida ya ha comenzado.');
        }

        $totalJugadores = JugadorPartida::where('id_partida', $partida->id)->count();
        if ($totalJugadores >= 2) {
            return redirect()->route('partidas.index')->with('error', 'No se puede unirse a la partida, ya está llena.');
        }

        $jugadorExistente = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        
        if ($jugadorExistente) {
            $tieneBarcosAsignados = Barco::where('id_jugador_partida', $jugadorExistente->id)->exists();
            
            if (!$tieneBarcosAsignados) {
                $this->generarBarcosParaJugador($jugadorExistente->id);
            }

            return redirect()->route('juego.tablero', $partida->id)
                ->with('info', 'Ya estás en esta partida.');
        }

        
        $jugadorPartida = JugadorPartida::create([
            'id_usuario' => Auth::id(),
            'id_partida' => $partida->id,
            'es_turno' => false
        ]);

        
        $this->generarBarcosParaJugador($jugadorPartida->id);

        $jugadoresActuales = JugadorPartida::where('id_partida', $partida->id)->count();
        
        
        if ($jugadoresActuales >= 2) {
            $partida->update(['estado' => 'en_curso']);

            $primerJugador = JugadorPartida::where('id_partida', $partida->id)
                ->orderBy('created_at', 'asc')
                ->first();
            if ($primerJugador) {
                $primerJugador->update(['es_turno' => true]);
            }
        }

        return redirect()->route('juego.tablero', $partida->id)->with('success', 'Te has unido a la partida. ¡Empieza la partida!');
    }

    
    private function generarBarcosParaJugador($jugadorPartidaId) {
        $coordenadas = [];
        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']; 
        $numeros = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; 

        foreach ($letras as $letra) {
            foreach ($numeros as $numero) {
                $coordenadas[] = $letra . $numero;
            }
        }

        shuffle($coordenadas); 
        $barcosCoords = array_slice($coordenadas, 0, 15); 

        foreach ($barcosCoords as $coord) {
            Barco::create([
                'id_jugador_partida' => $jugadorPartidaId,
                'coordenada' => $coord,
                'hundido' => false
            ]);
        }
    }

    
    public function cancelar($id)
    {
        $partida = Partida::findOrFail($id);
        
        $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        if ($jugadorActual) {
            $jugadorActual->delete();
            
            
            $jugadoresRestantes = JugadorPartida::where('id_partida', $partida->id)->count();
            if ($jugadoresRestantes === 0) {
                $partida->delete();
            }
        }

        return redirect()->route('dashboard')->with('success', 'Has salido de la partida.');
    }
}