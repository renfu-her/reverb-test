<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            // Create a default profile for new users
            UserProfile::create([
                'user_id' => $user->id,
                'display_name' => $user->name,
                'status' => 'online',
            ]);
        });
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function ownedRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function participatingRooms()
    {
        return $this->belongsToMany(Room::class, 'room_participants')
                    ->withPivot('joined_at', 'left_at')
                    ->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->profile?->display_name ?? $this->name ?? 'Unknown User';
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->profile?->avatar;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return '/storage/' . $this->avatar;
        }
        return ''; // Return empty string for FontAwesome icon
    }

    public function getAvatarHtmlAttribute(): string
    {
        if ($this->avatar) {
            return '<img src="' . $this->getAvatarUrlAttribute() . '" class="rounded-circle" alt="' . $this->display_name . '">';
        }
        return '<i class="fas fa-user-circle"></i>';
    }

    public function isInRoom(Room $room): bool
    {
        return $this->participatingRooms()
            ->where('room_id', $room->id)
            ->whereNull('left_at')
            ->exists();
    }
}
