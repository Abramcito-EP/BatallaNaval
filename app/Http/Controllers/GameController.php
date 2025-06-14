<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Ship;
use App\Models\Shot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with(['host', 'guest'])
            ->where('status', 'waiting')
            ->where('host_id', '!=', Auth::id())
            ->get();
            
        $myGames = Game::with(['host', 'guest', 'winner'])
            ->where(function ($query) {
                $query->where('host_id', Auth::id())
                    ->orWhere('guest_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Games/Index', [
            'availableGames' => $games,
            'myGames' => $myGames,
            'hasActiveHostedGame' => Game::userHasActiveHostedGames(Auth::id())
        ]);
    }

    public function create()
    {
        // Verificar si el usuario ya es anfitrión de una partida activa
        if (Game::userHasActiveHostedGames(Auth::id())) {
            return redirect()->route('games.index')
                ->with('error', 'Ya eres anfitrión de una partida activa. Debes terminarla antes de crear una nueva.');
        }

        return Inertia::render('Games/Create', [
            'showRules' => true
        ]);
    }

    public function store(Request $request)
    {
        // Verificar nuevamente si el usuario ya es anfitrión de una partida activa
        if (Game::userHasActiveHostedGames(Auth::id())) {
            return redirect()->route('games.index')
                ->with('error', 'Ya eres anfitrión de una partida activa. Debes terminarla antes de crear una nueva.');
        }

        $validated = $request->validate([
            'scenario' => 'required|in:classic,foggy,stormy',
            'max_response_time' => 'required|integer|min:5|max:60',
        ]);

        $game = Game::create([
            'host_id' => Auth::id(),
            'scenario' => $validated['scenario'],
            'max_response_time' => $validated['max_response_time'],
            'status' => 'waiting',
            'last_activity' => now()
        ]);

        // Generar posiciones aleatorias para los barcos del anfitrión
        $this->generateRandomShips($game->id, Auth::id());

        return redirect()->route('games.show', $game);
    }

    public function show(Game $game)
    {
        // Comprobar si el usuario puede ver este juego
        if ($game->host_id !== Auth::id() && $game->guest_id !== Auth::id()) {
            if ($game->status === 'waiting') {
                // Si el juego está esperando un jugador, permitir unirse
                return Inertia::render('Games/Join', [
                    'game' => $game->load('host'),
                    'showRules' => true
                ]);
            }
            
            abort(403, 'No tienes acceso a este juego');
        }

        // Cargar datos según el estado del juego
        $myShips = Ship::where('game_id', $game->id)
            ->where('user_id', Auth::id())
            ->get();
            
        $myShots = Shot::where('game_id', $game->id)
            ->where('user_id', Auth::id())
            ->get();
            
        $opponentId = $game->host_id === Auth::id() ? $game->guest_id : $game->host_id;
        
        return Inertia::render('Games/Show', [
            'game' => $game->load(['host', 'guest', 'winner']),
            'myShips' => $myShips,
            'myShots' => $myShots,
            'isMyTurn' => $this->isPlayerTurn($game),
            'opponentId' => $opponentId
        ]);
    }

    public function join(Game $game)
    {
        // Verificar que el juego esté disponible
        if ($game->status !== 'waiting' || $game->guest_id !== null) {
            return redirect()->route('games.index')
                ->with('error', 'Este juego ya no está disponible');
        }

        // Verificar que no sea el anfitrión
        if ($game->host_id === Auth::id()) {
            return redirect()->route('games.index')
                ->with('error', 'No puedes unirte a tu propio juego');
        }

        // Actualizar el juego
        $game->update([
            'guest_id' => Auth::id(),
            'status' => 'in_progress',
            'last_activity' => now()
        ]);

        // Generar posiciones aleatorias para los barcos del invitado
        $this->generateRandomShips($game->id, Auth::id());

        return redirect()->route('games.show', $game);
    }

    public function checkOpponentJoined()
    {
        // Buscar juegos donde el usuario actual es anfitrión y acaba de unirse un oponente
        $game = Game::where('host_id', Auth::id())
            ->where('status', 'in_progress')
            ->whereNotNull('guest_id')
            ->latest()
            ->first();
    
        if ($game) {
            return response()->json([
                'opponentJoined' => true,
                'gameId' => $game->id,
                'opponentName' => $game->guest->name,
                'joinedAt' => $game->updated_at->diffForHumans()
            ]);
        }
    
        return response()->json(['opponentJoined' => false]);
    }

    private function generateRandomShips($gameId, $userId)
    {
        $positions = [];
        $boardSize = 8;
        $shipCount = 15;

        // Generar posiciones aleatorias únicas
        while (count($positions) < $shipCount) {
            $x = rand(0, $boardSize - 1);
            $y = rand(0, $boardSize - 1);
            $key = "$x-$y";

            if (!isset($positions[$key])) {
                $positions[$key] = true;
                
                Ship::create([
                    'game_id' => $gameId,
                    'user_id' => $userId,
                    'x_position' => $x,
                    'y_position' => $y,
                    'is_sunk' => false
                ]);
            }
        }
    }

    private function isPlayerTurn(Game $game)
    {
        if ($game->status !== 'in_progress') {
            return false;
        }

        $lastShot = Shot::where('game_id', $game->id)
            ->latest()
            ->first();

        // Si no hay disparos, empieza el anfitrión
        if (!$lastShot) {
            return $game->host_id === Auth::id();
        }

        // Si el último disparo fue del usuario actual, no es su turno
        return $lastShot->user_id !== Auth::id();
    }
}