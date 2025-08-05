<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'content',
        'type',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSystemMessage(): bool
    {
        return in_array($this->type, ['join', 'leave', 'system']);
    }

    public function isJoinMessage(): bool
    {
        return $this->type === 'join';
    }

    public function isLeaveMessage(): bool
    {
        return $this->type === 'leave';
    }
}
