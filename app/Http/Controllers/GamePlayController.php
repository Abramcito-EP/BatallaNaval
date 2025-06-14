<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Ship;
use App\Models\Shot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamePlayController extends Controller
{
    public function fireShot(Request $request, Game $game)
    {
        // Verificar que el juego esté en progreso
        if ($game->status !== 'in_progress') {
            return response()->json(['error' => 'El juego no está activo'], 400);
        }

        // Verificar que sea el turno del jugador
        if (!$this->isPlayerTurn($game)) {
            return response()->json(['error' => 'No es tu turno'], 403);
        }

        // Resetear contador de turnos inactivos
        $game->update([
            'last_activity' => now(),
            'inactive_turns_count' => 0
        ]);

        // Validar las coordenadas
        $validated = $request->validate([
            'x' => 'required|integer|min:0|max:7',
            'y' => 'required|integer|min:0|max:7',
        ]);

        // Verificar que no haya disparado en esa posición antes
        $existingShot = Shot::where('game_id', $game->id)
            ->where('user_id', Auth::id())
            ->where('x_position', $validated['x'])
            ->where('y_position', $validated['y'])
            ->exists();

        if ($existingShot) {
            return response()->json(['error' => 'Ya has disparado en esa posición'], 400);
        }

        // Determinar el objetivo
        $targetId = $game->host_id === Auth::id() ? $game->guest_id : $game->host_id;

        // Comprobar si hay un barco en esa posición
        $ship = Ship::where('game_id', $game->id)
            ->where('user_id', $targetId)
            ->where('x_position', $validated['x'])
            ->where('y_position', $validated['y'])
            ->first();

        $hit = $ship !== null;

        // Registrar el disparo
        $shot = Shot::create([
            'game_id' => $game->id,
            'user_id' => Auth::id(),
            'x_position' => $validated['x'],
            'y_position' => $validated['y'],
            'hit' => $hit
        ]);

        // Si hay un impacto, marcar el barco como hundido
        if ($hit) {
            $ship->update(['is_sunk' => true]);
        }

        // Verificar si el juego ha terminado
        $remainingShips = Ship::where('game_id', $game->id)
            ->where('user_id', $targetId)
            ->where('is_sunk', false)
            ->count();

        if ($remainingShips === 0) {
            $game->update([
                'status' => 'finished',
                'winner_id' => Auth::id(),
                'end_reason' => 'victory'
            ]);
        }

        return response()->json([
            'hit' => $hit,
            'gameOver' => $remainingShips === 0,
            'shot' => $shot
        ]);
    }

    public function fireRandomShot(Game $game)
    {
        // Verificar que el juego esté en progreso
        if ($game->status !== 'in_progress') {
            return response()->json(['error' => 'El juego no está activo'], 400);
        }

        // Verificar que sea el turno del jugador
        if (!$this->isPlayerTurn($game)) {
            return response()->json(['error' => 'No es tu turno'], 403);
        }

        $userId = Auth::id();
        $targetId = $game->host_id === $userId ? $game->guest_id : $game->host_id;
        
        // Obtener todas las posiciones ya disparadas
        $existingShots = Shot::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->select('x_position', 'y_position')
            ->get()
            ->map(function ($shot) {
                return $shot->x_position . '-' . $shot->y_position;
            })
            ->toArray();
    
        // Generar una posición aleatoria que no haya sido disparada
        $validPosition = false;
        $x = 0;
        $y = 0;
    
        $maxAttempts = 100; // Prevenir bucle infinito
        $attempts = 0;
    
        while (!$validPosition && $attempts < $maxAttempts) {
            $x = rand(0, 7);
            $y = rand(0, 7);
        
            if (!in_array("$x-$y", $existingShots)) {
                $validPosition = true;
            }
        
            $attempts++;
        }
    
        // Si no se encontró una posición válida (muy poco probable)
        if (!$validPosition) {
            return response()->json(['error' => 'No se pudo encontrar una posición válida'], 500);
        }

        // Comprobar si hay un barco en esa posición
        $ship = Ship::where('game_id', $game->id)
            ->where('user_id', $targetId)
            ->where('x_position', $x)
            ->where('y_position', $y)
            ->first();

        $hit = $ship !== null;

        // Registrar el disparo
        $shot = Shot::create([
            'game_id' => $game->id,
            'user_id' => $userId,
            'x_position' => $x,
            'y_position' => $y,
            'hit' => $hit
        ]);

        // Resetear contador de turnos inactivos
        $game->update([
            'last_activity' => now(),
            'inactive_turns_count' => 0
        ]);

        // Si hay un impacto, marcar el barco como hundido
        if ($hit) {
            $ship->update(['is_sunk' => true]);
        }

        // Verificar si el juego ha terminado
        $remainingShips = Ship::where('game_id', $game->id)
            ->where('user_id', $targetId)
            ->where('is_sunk', false)
            ->count();

        if ($remainingShips === 0) {
            $game->update([
                'status' => 'finished',
                'winner_id' => $userId,
                'end_reason' => 'victory'
            ]);
        }

        return response()->json([
            'hit' => $hit,
            'gameOver' => $remainingShips === 0,
            'shot' => $shot,
            'automatic' => true
        ]);
    }

    public function abandonGame(Request $request, Game $game)
    {
        // Verificar que el usuario sea parte del juego
        if ($game->host_id !== Auth::id() && $game->guest_id !== Auth::id()) {
            return response()->json(['error' => 'No eres parte de este juego'], 403);
        }
        
        // Si el juego ya está terminado, no hacer nada
        if ($game->status === 'finished') {
            return response()->json(['error' => 'El juego ya ha terminado'], 400);
        }
        
        // Determinar quién abandona y quién gana
        $isHost = $game->host_id === Auth::id();
        $winnerId = $isHost ? $game->guest_id : $game->host_id;
        $reason = $request->input('reason', 'abandoned');
        
        // Actualizar el juego
        $game->update([
            'status' => 'finished',
            'winner_id' => $winnerId,
            'end_reason' => $reason
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Has abandonado el juego'
        ]);
    }

    public function pollGameStatus(Game $game)
    {
        // Verificar que el usuario sea parte del juego
        if ($game->host_id !== Auth::id() && $game->guest_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes acceso a este juego'], 403);
        }

        // Verificar inactividad
        $gameEndedByInactivity = $this->checkInactivity($game);
        
        // Si el juego terminó por inactividad, recargar los datos
        if ($gameEndedByInactivity) {
            $game = Game::find($game->id); // Recargar el juego con los datos actualizados
        }

        $opponentId = $game->host_id === Auth::id() ? $game->guest_id : $game->host_id;
        
        // Si está esperando a un jugador
        if ($game->status === 'waiting') {
            return response()->json([
                'status' => $game->status,
                'opponent' => null,
                'yourTurn' => false,
                'lastShot' => null
            ]);
        }
        
        // Determinar de quién es el turno
        $lastShot = Shot::where('game_id', $game->id)
            ->latest()
            ->first();
        
        $isYourTurn = false;
        
        if (!$lastShot) {
            // Si no hay disparos, el anfitrión comienza
            $isYourTurn = $game->host_id === Auth::id();
        } else {
            // Si el último disparo fue del oponente, es tu turno
            $isYourTurn = $lastShot->user_id !== Auth::id();
        }
        
        // Obtener el último disparo del oponente contra ti
        $lastOpponentShot = Shot::where('game_id', $game->id)
            ->where('user_id', $opponentId)
            ->latest()
            ->first();

        return response()->json([
            'status' => $game->status,
            'yourTurn' => $isYourTurn,
            'lastOpponentShot' => $lastOpponentShot,
            'gameOver' => $game->status === 'finished',
            'winner' => $game->winner_id,
            'endReason' => $game->end_reason
        ]);
    }

    public function getStatistics()
    {
        $userId = Auth::id();
        
        // Obtener todas las partidas jugadas
        $games = Game::where(function ($query) use ($userId) {
            $query->where('host_id', $userId)
                ->orWhere('guest_id', $userId);
        })
        ->where('status', 'finished')
        ->get();
        
        $totalGames = $games->count();
        $wins = $games->where('winner_id', $userId)->count();
        $losses = $totalGames - $wins;
        
        // Estadísticas por motivo de finalización
        $victoriesByReason = [
            'victory' => $games->where('winner_id', $userId)->where('end_reason', 'victory')->count(),
            'abandon' => $games->where('winner_id', $userId)->where('end_reason', 'abandon')->count(),
            'afk' => $games->where('winner_id', $userId)->where('end_reason', 'afk')->count()
        ];
        
        $lossesByReason = [
            'victory' => $games->where('winner_id', '!=', $userId)->where('end_reason', 'victory')->count(),
            'abandon' => $games->where('winner_id', '!=', $userId)->where('end_reason', 'abandon')->count(),
            'afk' => $games->where('winner_id', '!=', $userId)->where('end_reason', 'afk')->count()
        ];
        
        return response()->json([
            'totalGames' => $totalGames,
            'wins' => $wins,
            'losses' => $losses,
            'winRate' => $totalGames > 0 ? round(($wins / $totalGames) * 100, 1) : 0,
            'victoriesByReason' => $victoriesByReason,
            'lossesByReason' => $lossesByReason
        ]);
    }

    public function getGameReplay(Game $game)
    {
        // Verificar que el usuario haya participado en el juego
        if ($game->host_id !== Auth::id() && $game->guest_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes acceso a este juego'], 403);
        }
        
        // Verificar que el juego haya terminado
        if ($game->status !== 'finished') {
            return response()->json(['error' => 'El juego aún no ha terminado'], 400);
        }
        
        // Obtener todos los barcos y disparos
        $hostShips = Ship::where('game_id', $game->id)
            ->where('user_id', $game->host_id)
            ->get();
            
        $guestShips = Ship::where('game_id', $game->id)
            ->where('user_id', $game->guest_id)
            ->get();
            
        $shots = Shot::where('game_id', $game->id)
            ->orderBy('created_at')
            ->get();
            
        return response()->json([
            'game' => $game->load(['host', 'guest', 'winner']),
            'hostShips' => $hostShips,
            'guestShips' => $guestShips,
            'shots' => $shots
        ]);
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

    // Añadir este método en el GamePlayController
    private function checkInactivity(Game $game)
    {
        // Si el juego ya ha terminado, no hacer nada
        if ($game->status !== 'in_progress') {
            return;
        }
        
        // Verificar si ha pasado más tiempo que el permitido desde la última actividad
        $maxTime = $game->max_response_time;
        $lastActivity = $game->last_activity;
        
        if ($lastActivity && $lastActivity->addSeconds($maxTime)->isPast()) {
            // Incrementar el contador de turnos inactivos
            $game->increment('inactive_turns_count');
            
            // Actualizar la hora de última actividad
            $game->update(['last_activity' => now()]);
            
            // Si se han acumulado 3 turnos inactivos, finalizar el juego
            if ($game->inactive_turns_count >= 3) {
                // Determinar quién está inactivo basado en el turno actual
                $lastShot = Shot::where('game_id', $game->id)->latest()->first();
                $currentTurnUserId = $lastShot ? 
                    ($lastShot->user_id === $game->host_id ? $game->guest_id : $game->host_id) : 
                    $game->host_id;
                
                // El ganador es el oponente del usuario inactivo
                $winnerId = $currentTurnUserId === $game->host_id ? $game->guest_id : $game->host_id;
                
                // Actualizar el juego
                $game->update([
                    'status' => 'finished',
                    'winner_id' => $winnerId,
                    'end_reason' => 'afk'
                ]);
                
                return true; // El juego ha terminado por inactividad
            }
        }
        
        return false;
    }
}