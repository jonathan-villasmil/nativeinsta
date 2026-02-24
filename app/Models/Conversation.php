<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->oldest();
    }

    public function lastMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    /** Returns the other participant for a given user */
    public function otherUser(User $user): User
    {
        return $this->user_one_id === $user->id ? $this->userTwo : $this->userOne;
    }

    /** Get or create a conversation between two users (smaller id always user_one) */
    public static function between(int $a, int $b): self
    {
        [$one, $two] = $a < $b ? [$a, $b] : [$b, $a];
        return self::firstOrCreate(['user_one_id' => $one, 'user_two_id' => $two]);
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
