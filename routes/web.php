<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\BatallaNaval\MovimientosController;
use App\Http\Controllers\BatallaNaval\PartidaController;;
use App\Http\Controllers\BatallaNaval\BarcoController;
use App\Http\Controllers\BatallaNaval\JugadorPartidaController;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/batallanaval/juego', [JuegoController::class, 'index'])->name('batallanaval.juego.index');
Route::get('/batallanaval/crear', [JuegoController::class, 'crearPartidaForm'])->name('batallanaval.juego.crear');
Route::post('/batallanaval/crear', [JuegoController::class, 'crearPartida'])->name('batallanaval.juego.crear');
Route::get('batallanaval/partidas', [JuegoController::class, 'listarPartidas']);
Route::post('/batallanaval/unirse/{id}', [JuegoController::class, 'unirse'])->name('batallanaval.juego.unirse');
Route::post('/batallanaval/salir/{id}', [JuegoController::class, 'salirPartida'])->name('batallanaval.juego.salir');
Route::post('/batallanaval/eliminar/{id}', [JuegoController::class, 'eliminarJuego'])->name('batallanaval.juego.eliminar');
Route::post('/batallanaval/finalizar/{id}', [JuegoController::class, 'finalizarPartida'])->name('batallanaval.juego.finalizar');   

require __DIR__.'/auth.php';
