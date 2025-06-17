<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\GamePlayController;
use App\Http\Controllers\ProfileController;
use App\Models\Game; // Añadir esta importación
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestión de juegos
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games', [GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    Route::get('/check-opponent-joined', [GameController::class, 'checkOpponentJoined'])->name('games.check-opponent');
    Route::match(['get', 'post'], '/games/{game}/join', [GameController::class, 'join'])->name('games.join')->middleware(['signed']);
    
    // Gameplay
    Route::post('/games/{game}/fire', [GamePlayController::class, 'fireShot'])->name('games.fire');
    Route::post('/games/{game}/fire-random', [GamePlayController::class, 'fireRandomShot'])->name('games.fireRandom');
    Route::get('/games/{game}/poll', [GamePlayController::class, 'pollGameStatus'])->name('games.poll');
    Route::post('/games/{game}/abandon', [GamePlayController::class, 'abandonGame'])->name('games.abandon');
    
    // Estadísticas
    Route::get('/statistics', [GamePlayController::class, 'getStatistics'])->name('games.statistics');
    Route::get('/games/{game}/replay', [GamePlayController::class, 'getGameReplay'])->name('games.replay');
});

// Eliminar o comentar esta sección:
/*
Route::post('/logout', function () {
    // Verificar si el usuario tiene partidas activas como anfitrión
    if (Game::where('host_id', Auth::id())
        ->whereIn('status', ['waiting', 'in_progress'])
        ->exists()) {
        return back()->with('error', 'No puedes cerrar sesión mientras seas anfitrión de una partida activa. Por favor, termina o abandona tus partidas primero.');
    }
    
    // Verificar si el usuario está en cualquier partida activa (como invitado)
    if (Game::where(function ($query) {
            $query->where('host_id', Auth::id())
                ->orWhere('guest_id', Auth::id());
        })
        ->whereIn('status', ['waiting', 'in_progress'])
        ->exists()) {
        return back()->with('error', 'No puedes cerrar sesión mientras estés en una partida activa. Por favor, termina o abandona tu partida actual primero.');
    }
    
    Auth::logout();
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/');
})->middleware('auth')->name('logout');
*/

require __DIR__.'/auth.php';
