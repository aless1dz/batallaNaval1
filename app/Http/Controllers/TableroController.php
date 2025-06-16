<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\JugadorPartida;
use App\Models\Barco;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TableroController extends Controller
{
    public function tablero($id)
    {
        try {
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

            $misBarcos = Barco::where('id_jugador_partida', $jugadorActual->id)->get();
            
            $misBarcosCoords = $misBarcos->pluck('coordenada')->toArray();

            $movimientosContraOponente = Movimiento::where('id_partida', $partida->id)
                ->where('id_atacante', $jugadorActual->id)
                ->where('id_defensor', $oponente->id)
                ->get();

            $barcosOponenteVisibles = $movimientosContraOponente
                ->where('acierto', true)
                ->pluck('coordenada')
                ->toArray();

            $todosMovimientos = Movimiento::where('id_partida', $partida->id)->get();
            
            $misMovimientos = $todosMovimientos->where('id_atacante', $jugadorActual->id);
            $movimientosOponente = $todosMovimientos->where('id_defensor', $jugadorActual->id);

            
            $estadisticasMias = [
                'total_disparos' => $misMovimientos->count(),
                'impactos' => $misMovimientos->where('acierto', true)->count(),
                'precision' => $misMovimientos->count() > 0 
                    ? round(($misMovimientos->where('acierto', true)->count() / $misMovimientos->count()) * 100, 1)
                    : 0
            ];

            $estadisticasOponente = [
                'total_disparos' => $movimientosOponente->count(),
                'impactos' => $movimientosOponente->where('acierto', true)->count(),
                'precision' => $movimientosOponente->count() > 0 
                    ? round(($movimientosOponente->where('acierto', true)->count() / $movimientosOponente->count()) * 100, 1)
                    : 0
            ];

            return Inertia::render('Juegos/Partida', [
                'partida' => $partida,
                'jugadorActual' => $jugadorActual,
                'oponente' => $oponente,
                
                'miTablero' => [
                    'barcos' => $misBarcosCoords, 
                    'disparos' => $this->formatearDisparos($movimientosOponente), 
                    'estadisticas' => $estadisticasOponente,
                    'esPropio' => true,
                    'mostrarBarcos' => true, 
                    'puedeDisparar' => false 
                ],
                
                'tableroOponente' => [
                    'barcos' => $barcosOponenteVisibles, 
                    'disparos' => $this->formatearDisparos($misMovimientos), 
                    'estadisticas' => $estadisticasMias,
                    'esPropio' => false,
                    'mostrarBarcos' => false, 
                    'puedeDisparar' => $jugadorActual->es_turno 
                ],
                
                'esMiTurno' => $jugadorActual->es_turno,
                'juegoTerminado' => $partida->estado === 'finalizada',
                'ganador' => $partida->ganador_id == $jugadorActual->id ? 'jugador' : ($partida->estado === 'finalizada' ? 'oponente' : null)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en tablero:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('partidas.index')
                ->withErrors(['error' => 'Error al cargar el tablero: ' . $e->getMessage()]);
        }
    }

    private function formatearDisparos($movimientos)
{
    // Si es null, vacío o no es una colección, retornar array vacío
    if (empty($movimientos) || !method_exists($movimientos, 'map')) {
        return [];
    }

    return $movimientos->map(function ($movimiento) {
        $hundido = false;

        if ($movimiento->acierto) {
            $barcoImpactado = Barco::where('id_jugador_partida', $movimiento->id_defensor)
                ->where('coordenada', $movimiento->coordenada)
                ->first();

            if ($barcoImpactado) {
                $hundido = $barcoImpactado->hundido;
            }
        }

        return [
            'posicion' => $movimiento->coordenada,
            'impacto' => $movimiento->acierto,
            'hundido' => $hundido,
            'resultado' => $hundido ? 'hundido' : ($movimiento->acierto ? 'impacto' : 'agua')
        ];
    })->values()->toArray(); // ← AQUÍ está el cambio importante: ->values()
}





    public function disparar(Request $request, $id) {

    $request->validate([
        'posicion' => ['required', 'string', 'min:2', 'max:3', 'regex:/^[A-J](10|[1-9])$/']
    ], [
        'posicion.required' => 'La posición es requerida',
        'posicion.regex' => 'Formato de posición inválido (debe ser A1-J10)'
    ]);

    try {
        $partida = Partida::findOrFail($id);

        if ($partida->estado !== 'en_curso') {
            return response()->json(['error' => 'La partida no está en curso'], 400);
        }

        $jugadorActual = JugadorPartida::where('id_partida', $id)
            ->where('id_usuario', Auth::id())
            ->first();

        if (!$jugadorActual) {
            return response()->json(['error' => 'No tienes acceso a esta partida'], 403);
        }

        if (!$jugadorActual->es_turno) {
            return response()->json(['error' => 'No es tu turno'], 400);
        }

        $oponente = JugadorPartida::where('id_partida', $id)
            ->where('id_usuario', '!=', Auth::id())
            ->first();

        if (!$oponente) {
            return response()->json(['error' => 'No se encontró el oponente'], 400);
        }

        $yaDisparo = Movimiento::where([
            ['id_partida', $id],
            ['id_atacante', $jugadorActual->id],
            ['id_defensor', $oponente->id],
            ['coordenada', $request->posicion]
        ])->exists();

        if ($yaDisparo) {
            return response()->json(['error' => 'Ya disparaste a esa posición'], 400);
        }

        $barcoImpactado = Barco::where('id_jugador_partida', $oponente->id)
            ->where('coordenada', $request->posicion)
            ->where('hundido', false)
            ->first();

        $acierto = (bool) $barcoImpactado;
        $resultado = $acierto ? 'impacto' : 'agua';

        if ($barcoImpactado) {
            $barcoImpactado->update(['hundido' => true]);
            $resultado = 'hundido';
        }

        Movimiento::create([
            'id_partida' => $id,
            'id_atacante' => $jugadorActual->id,
            'id_defensor' => $oponente->id,
            'coordenada' => $request->posicion,
            'acierto' => $acierto
        ]);

        
        $jugadorActual->update(['es_turno' => false]);
        $oponente->update(['es_turno' => true]);

        $barcosRestantes = Barco::where('id_jugador_partida', $oponente->id)
            ->where('hundido', false)
            ->count();

        $juegoTerminado = $barcosRestantes === 0;

        if ($juegoTerminado) {
            $partida->update([
                'estado' => 'finalizada',
                'ganador_id' => $jugadorActual->id_usuario
            ]);
        }

        return response()->json([
            'success' => true,
            'resultado' => $resultado,
            'posicion' => $request->posicion,
            'juegoTerminado' => $juegoTerminado,
            'esMiTurno' => false, 
            'mensaje' => $this->obtenerMensajeResultado($resultado, $request->posicion),
            'barcosRestantes' => $barcosRestantes,
            'turnoActual' => $oponente->id 
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Ocurrió un error inesperado',
            'message' => $e->getMessage()
        ], 500);
    }
}

    private function obtenerMensajeResultado($resultado, $posicion)
    {
        switch ($resultado) {
            case 'agua':
                return "Agua en {$posicion}";
            case 'impacto':
                return "¡Impacto en {$posicion}!";
            case 'hundido':
                return "¡Barco hundido en {$posicion}!";
            default:
                return "Disparo en {$posicion}";
        }
    
    }
}