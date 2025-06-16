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
            Log::info('Partida encontrada:', ['id' => $id, 'estado' => $partida->estado]);
        
            $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
                ->where('id_usuario', Auth::id())
                ->first();

            Log::info('Jugador actual:', ['jugador' => $jugadorActual ? $jugadorActual->toArray() : null]);

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

            // DATOS PARA MI TABLERO (completos)
            $misBarcos = Barco::where('id_jugador_partida', $jugadorActual->id)->get();
            Log::info('Mis barcos:', ['count' => $misBarcos->count(), 'barcos' => $misBarcos->toArray()]);
            
            // Convertir a array de coordenadas
            $misBarcosCoords = $misBarcos->pluck('coordenada')->toArray();

            // DATOS PARA TABLERO DEL OPONENTE (solo impactos confirmados)
            $movimientosContraOponente = Movimiento::where('id_partida', $partida->id)
                ->where('id_atacante', $jugadorActual->id)
                ->where('id_defensor', $oponente->id)
                ->get();

            // Solo incluir coordenadas donde hubo acierto para el tablero enemigo
            $barcosOponenteVisibles = $movimientosContraOponente
                ->where('acierto', true)
                ->pluck('coordenada')
                ->toArray();

            // TODOS los movimientos para mostrar disparos
            $todosMovimientos = Movimiento::where('id_partida', $partida->id)->get();
            
            // Separar movimientos por jugador
            $misMovimientos = $todosMovimientos->where('id_atacante', $jugadorActual->id);
            $movimientosOponente = $todosMovimientos->where('id_defensor', $jugadorActual->id);

            // Calcular estadísticas
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

            return Inertia::render('Juegos/Tablero', [
                'partida' => $partida,
                'jugadorActual' => $jugadorActual,
                'oponente' => $oponente,
                
                // DATOS PARA MI TABLERO
                'miTablero' => [
                    'barcos' => $misBarcosCoords, // Mis coordenadas de barcos
                    'disparos' => $this->formatearDisparos($movimientosOponente), // Disparos que me hicieron
                    'estadisticas' => $estadisticasOponente,
                    'esPropio' => true,
                    'mostrarBarcos' => true, // Siempre mostrar mis barcos
                    'puedeDisparar' => false // No puedo disparar a mi tablero
                ],
                
                // DATOS PARA TABLERO DEL OPONENTE
                'tableroOponente' => [
                    'barcos' => $barcosOponenteVisibles, // Solo barcos donde he hecho acierto
                    'disparos' => $this->formatearDisparos($misMovimientos), // Mis disparos
                    'estadisticas' => $estadisticasMias,
                    'esPropio' => false,
                    'mostrarBarcos' => false, // Solo mostrar barcos donde hay acierto
                    'puedeDisparar' => $jugadorActual->es_turno // Solo si es mi turno
                ],
                
                // ESTADO DEL JUEGO
                'esMiTurno' => $jugadorActual->es_turno,
                'juegoTerminado' => $partida->estado === 'terminada',
                'ganador' => $partida->ganador_id == $jugadorActual->id ? 'jugador' : ($partida->estado === 'terminada' ? 'oponente' : null)
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

    /**
     * Formatear movimientos para el componente Tablero
     */
    private function formatearDisparos($movimientos)
    {
        return $movimientos->map(function ($movimiento) {
            // Verificar si el barco está hundido
            $hundido = false;
            if ($movimiento->acierto) {
                // Buscar el barco impactado
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
        })->toArray();
    }

    /**
     * Realizar disparo
     */
    public function disparar(Request $request, $id)
{
    try {
        // CAMBIO 1: Corregir la validación de posición
        $request->validate([
    'posicion' => ['required', 'string', 'min:2', 'max:3', 'regex:/^[A-J](10|[1-9])$/']
        ]    , [
            'posicion.required' => 'La posición es requerida',
            'posicion.min' => 'La posición debe tener al menos 2 caracteres',
            'posicion.max' => 'La posición debe tener máximo 3 caracteres', 
            'posicion.regex' => 'Formato de posición inválido (debe ser A1-J10)'
        ]);

        $partida = Partida::findOrFail($id);
        
        if ($partida->estado !== 'en_curso') {
            return response()->json(['error' => 'La partida no está en curso'], 400);
        }
        
        $jugadorActual = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', Auth::id())
            ->first();

        if (!$jugadorActual) {
            return response()->json(['error' => 'No tienes acceso a esta partida'], 403);
        }

        \Log::info('Validando turno', [
        'usuario_actual' => Auth::id(),
        'jugador_actual' => $jugadorActual ? $jugadorActual->id : null,
        'es_turno' => $jugadorActual ? $jugadorActual->es_turno : null,
        'partida' => $partida->id,
        ]);
        // Verificar que es el turno del jugador
        if (!$jugadorActual->es_turno) {
            return response()->json(['error' => 'No es tu turno'], 400);
        }

        // Obtener el oponente
        $oponente = JugadorPartida::where('id_partida', $partida->id)
            ->where('id_usuario', '!=', Auth::id())
            ->first();

        if (!$oponente) {
            return response()->json(['error' => 'No se encontró el oponente'], 400);
        }

        // Verificar que no se haya disparado ya a esa posición
        $disparoExistente = Movimiento::where('id_partida', $partida->id)
            ->where('id_atacante', $jugadorActual->id)
            ->where('id_defensor', $oponente->id)
            ->where('coordenada', $request->posicion)
            ->exists();

        if ($disparoExistente) {
            return response()->json(['error' => 'Ya disparaste a esa posición'], 400);
        }

        // Buscar si hay un barco en esa posición
        $barcoImpactado = Barco::where('id_jugador_partida', $oponente->id)
            ->where('coordenada', $request->posicion)
            ->where('hundido', false)
            ->first();

        $acierto = !!$barcoImpactado;
        $resultado = $acierto ? 'impacto' : 'agua';

        // CAMBIO 2: Mejorar la lógica de hundido
        if ($barcoImpactado) {
            $barcoImpactado->update(['hundido' => true]);
            $resultado = 'hundido'; // En tu estructura cada barco es una coordenada
            
            Log::info('Barco hundido:', [
                'posicion' => $request->posicion,
                'barco_id' => $barcoImpactado->id
            ]);
        }

        // Crear el movimiento
        Movimiento::create([
            'id_partida' => $partida->id,
            'id_atacante' => $jugadorActual->id,
            'id_defensor' => $oponente->id,
            'coordenada' => $request->posicion,
            'acierto' => $acierto
        ]);

        Log::info('Disparo realizado:', [
            'atacante' => $jugadorActual->id,
            'defensor' => $oponente->id,
            'posicion' => $request->posicion,
            'resultado' => $resultado
        ]);

        // CAMBIO 3: Cambiar turno solo si fue agua
        if (!$acierto) {
            $jugadorActual->update(['es_turno' => false]);
            $oponente->update(['es_turno' => true]);
            
            Log::info('Turno cambiado:', [
                'de' => $jugadorActual->id,
                'a' => $oponente->id
            ]);
        }

        // Verificar si el juego terminó (todos los barcos hundidos)
        $barcosRestantes = Barco::where('id_jugador_partida', $oponente->id)
            ->where('hundido', false)
            ->count();
            
        $todosBarcosHundidos = $barcosRestantes === 0;

       if ($todosBarcosHundidos) {
    $partida->update([
    'estado' => 'finalizada',
    'ganador_id' => $jugadorActual->id_usuario // <-- este es el correcto
    ]);
    
    Log::info('Juego terminado:', [
        'ganador' => $jugadorActual->id,
        'partida' => $partida->id
    ]);
    }

        // CAMBIO 4: Mejorar la respuesta JSON
        return response()->json([
            'success' => true,
            'resultado' => $resultado,
            'posicion' => $request->posicion,
            'juegoTerminado' => $todosBarcosHundidos,
            'esMiTurno' => $acierto, // Sigue siendo mi turno si hubo acierto
            'mensaje' => $this->obtenerMensajeResultado($resultado, $request->posicion),
            // CAMBIO 5: Agregar información adicional útil
            'barcosRestantes' => $barcosRestantes,
            'turnoActual' => $acierto ? $jugadorActual->id : $oponente->id
        ], 200); // CAMBIO 6: Especificar código de estado explícitamente
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validación fallida en disparo:', [
            'errors' => $e->errors(),
            'input' => $request->all()
        ]);
        
        return response()->json([
            'error' => 'Datos de entrada inválidos',
            'message' => 'Formato de posición incorrecto', // CAMBIO 7: Mensaje más claro
            'errores' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Error en disparo:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'partida_id' => $id,
            'posicion' => $request->posicion ?? 'N/A',
            'user_id' => Auth::id()
        ]);
        
        return response()->json([
            'error' => 'Error interno del servidor',
            'message' => 'Ha ocurrido un error inesperado' // CAMBIO 8: Mensaje más amigable
        ], 500);
    }
}
    /**
     * Obtener mensaje descriptivo del resultado
     */
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