<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneExpiredStories extends Command
{
    protected $signature   = 'stories:prune';
    protected $description = 'Delete stories that passed their 24-hour expiration time';

    public function handle(): int
    {
        // withoutGlobalScopes() lets us see already-expired rows
        $expired = Story::withoutGlobalScopes()
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expired as $story) {
            Storage::disk('public')->delete($story->image_path);
            $story->delete();
            $count++;
        }

        $this->info("Pruned {$count} expired story/stories.");
        return self::SUCCESS;
    }
}
