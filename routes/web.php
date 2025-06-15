<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\GamePlayController;
use App\Http\Controllers\ProfileController;
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
    Route::post('/games/{game}/join', [GameController::class, 'join'])->name('games.join');
    Route::get('/check-opponent-joined', [GameController::class, 'checkOpponentJoined'])->name('games.check-opponent');
    
    // Gameplay
    Route::post('/games/{game}/fire', [GamePlayController::class, 'fireShot'])->name('games.fire');
    Route::post('/games/{game}/fire-random', [GamePlayController::class, 'fireRandomShot'])->name('games.fireRandom');
    Route::get('/games/{game}/poll', [GamePlayController::class, 'pollGameStatus'])->name('games.poll');
    Route::post('/games/{game}/abandon', [GamePlayController::class, 'abandonGame'])->name('games.abandon');
    
    // Estadísticas
    Route::get('/statistics', [GamePlayController::class, 'getStatistics'])->name('games.statistics');
    Route::get('/games/{game}/replay', [GamePlayController::class, 'getGameReplay'])->name('games.replay');
});

// Ruta para cerrar sesión
Route::post('logout', function () {
    Auth::logout();
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
