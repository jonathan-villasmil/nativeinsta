<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    protected $fillable = ['user_id', 'image_path', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Only return non-expired stories
    protected static function booted(): void
    {
        static::addGlobalScope('active', fn (Builder $q) =>
            $q->where('expires_at', '>', now())
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class);
    }

    public function isViewedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->views()->where('user_id', $user->id)->exists();
    }

    public function getImageUrlAttribute(): string
    {
        return route('img', ['path' => $this->image_path]);
    }
}
