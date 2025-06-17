<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'guest_id',
        'status',
        'scenario',
        'max_response_time',
        'winner_id',
        'end_reason',
        'inactive_turns_count',
        'last_activity'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function ships()
    {
        return $this->hasMany(Ship::class);
    }

    public function shots()
    {
        return $this->hasMany(Shot::class);
    }

    // Verificar si un usuario tiene partidas activas como anfitrión
    public static function userHasActiveHostedGames($userId)
    {
        return self::where('host_id', $userId)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->exists();
    }

    // Verificar si un usuario tiene partidas activas (como anfitrión o invitado)
    public static function userHasActiveGames($userId)
    {
        return self::where(function ($query) use ($userId) {
            $query->where('host_id', $userId)
                ->orWhere('guest_id', $userId);
        })
        ->whereIn('status', ['waiting', 'in_progress'])
        ->exists();
    }

    public static function userIsInActiveGame($userId)
    {
        return self::where(function ($query) use ($userId) {
                $query->where('host_id', $userId)
                    ->orWhere('guest_id', $userId);
            })
            ->whereIn('status', ['waiting', 'in_progress'])
            ->exists();
    }
}