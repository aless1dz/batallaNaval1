<?php

namespace App\Http\Controllers\BatallaNaval;

use App\Http\Controllers\Controller;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JuegoController extends Controller
{
    public function index()
    {
        $partidas = Partida::with('jugadores')->get();

        return Inertia::render('BatallaNaval/Index', [
            'partidas' => $partidas,
        ]);
    }

    public function crearPartidaForm()
    {
        return Inertia::render('BatallaNaval/CrearPartida');
    }

    public function crearPartida(Reques $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
        ]);
        $partida = Partida::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'ganador_id' => null,
        ]);

        $partida->jugadores()->attach(auth()->id());

        return redirect()->route('batallanaval.juego.index')->with('success', 'Partida creada exitosamente.');
    }


    public function unirse()
    {
        $partida = Partida::with('jugadores')
            ->where('estado', 'esperando')
            ->first();

        if(!$partida->jugadores->count()<2)
        {
            return redirect()->back()->with('error', 'No se puede unirse a la partida, ya hay suficientes jugadores.');
        }

        if(!$partida->jugadores->contains(auth()->id())) {
            $partida->jugadores()->attach(auth()->id());
        }

        if(!$partida->jugadores->count() == 2)
        {
            $partida->estado = 'en_progreso';
            $partida->save();
        }
        return redirect()->route('batallanaval.juego.index')->with('success', 'Te has unido a la partida exitosamente.');
        
    }

    public function salirPartida(Partida $partida)
    {
        if ($partida->jugadores->contains(auth()->id())) {
            $partida->jugadores()->detach(auth()->id());
        }

        return redirect()->route('batallanaval.juego.index')->with('success', 'Has salido de la partida exitosamente.');
    }

    public function eliminarJuego($id)
    {
        $partida = Partida::findOrFail($id);

        if ($partida->jugadores->contains(auth()->id())) {
            $partida->jugadores()->detach(auth()->id());
        }

        $partida->delete();

        return redirect()->route('batallanaval.juego.index')->with('success', 'Juego eliminado exitosamente.');
    }

    public function finalizarPartida(Partida $partida, User $ganador)
    {
        if ($partida->estado !== 'en_progreso') {
            return redirect()->back()->with('error', 'La partida no está en progreso.');
        }

        $partida->estado = 'finalizada';
        $partida->ganador_id = $ganador->id;
        $partida->save();

        return redirect()->route('batallanaval.juego.index')->with('success', 'Partida finalizada exitosamente.');
    }

    
}