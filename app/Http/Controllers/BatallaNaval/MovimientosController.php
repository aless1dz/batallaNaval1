<?php

namespace App\Http\Controllers\BatallaNaval;

use App\Http\Controllers\Controller;
use App\Models\Partida;
use App\Models\Barco;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Movimiento;
use Inertia\Inertia;

class MovimientosController extends Controller
{
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'id_partida' => 'required|exists:partidas,id',
            'id_atacante' => 'required|exists:jugadores_partida,id',
            'id_defensor' => 'required|exists:jugadores_partida,id',
            'coordenada' => 'required|string|max:255',
        ]);
        $acierto = false;

        $barco = Barco::where('id_jugador_partida', $request->id_defensor)
            ->where('coordenada', $request->coordenada)
            ->first();

        if($barco){
            $acierto = true;
            $barco->coordenada = null; // Marcar el barco como hundido
            $barco->save();
        }

        $movimiento = Movimiento::create([
            'id_partida' => $request->id_partida,
            'id_atacante' => $request->id_atacante,
            'id_defensor' => $request->id_defensor,
            'coordenada' => $request->coordenada,
            'acierto' => $acierto,
        ]);

        return response()->json([
            'success' => true,
            'movimiento' => $movimiento,
            'acierto' => $acierto,
            'mensaje' => $acierto ? '¡Acierto!' : 'Fallaste el tiro.'
        ]);
    }

    public function obtenerMovimientos($id_partida)
    {
        $movimientos = Movimiento::where('id_partida', $id_partida)
            ->orderBy('creado_en', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'movimientos' => $movimientos,
        ]);
    }
}