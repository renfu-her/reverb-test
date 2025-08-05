<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'max_participants',
        'status',
        'room_code',
        'is_private'
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'max_participants' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($room) {
            if (empty($room->room_code)) {
                $room->room_code = self::generateRoomCode();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function participants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_participants')
                    ->withPivot('joined_at', 'left_at')
                    ->withTimestamps();
    }

    public function isUnlimited(): bool
    {
        return $this->max_participants === 0;
    }

    public function canJoin(User $user): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->isUnlimited()) {
            return true;
        }

        $currentParticipants = $this->participants()->whereNull('left_at')->count();
        return $currentParticipants < $this->max_participants;
    }

    private static function generateRoomCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (static::where('room_code', $code)->exists());

        return $code;
    }
}
