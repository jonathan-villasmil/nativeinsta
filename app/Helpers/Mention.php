<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\HtmlString;

class Mention
{
    /**
     * Parse @username mentions in text, linking found users to their profiles.
     * Returns an HtmlString safe for {!! ... !!} rendering.
     */
    public static function parse(string $text): HtmlString
    {
        // Extract all @handles from the text first (batch DB lookup)
        preg_match_all('/@([\w.]+)/', $text, $matches);
        $handles = array_unique($matches[1] ?? []);

        // Build a lookup map: username (lowercase) => User
        $users = [];
        if (!empty($handles)) {
            $users = User::whereIn('username', $handles)
                ->orWhereIn('name', $handles)
                ->get()
                ->keyBy(fn ($u) => strtolower($u->username ?? $u->name))
                ->all();
        }

        // Escape the raw text first, then replace @handle with <a> tags
        $escaped = e($text);

        $html = preg_replace_callback('/@([\w.]+)/', function ($m) use ($users) {
            $key = strtolower($m[1]);
            if (isset($users[$key])) {
                $user = $users[$key];
                $url  = route('profile.show', $user);
                return '<a href="' . e($url) . '" '
                    . 'style="font-weight:600;color:var(--text);text-decoration:none;" '
                    . 'onmouseover="this.style.textDecoration=\'underline\'" '
                    . 'onmouseout="this.style.textDecoration=\'none\'">'
                    . '@' . e($m[1])
                    . '</a>';
            }
            // Unknown handle — just show as plain text (already escaped)
            return '@' . e($m[1]);
        }, $escaped);

        return new HtmlString($html);
    }

    /**
     * Parse @mentions and send a 'mention' notification to each mentioned user.
     * Call this after saving a post or comment.
     *
     * @param  string  $text        The caption or comment body
     * @param  int     $actorId     The user who wrote the text
     * @param  \Illuminate\Database\Eloquent\Model  $notifiable  The model to attach the notification to
     */
    public static function notifyMentions(string $text, int $actorId, \Illuminate\Database\Eloquent\Model $notifiable): void
    {
        preg_match_all('/@([\w.]+)/', $text, $matches);
        $handles = array_unique($matches[1] ?? []);
        if (empty($handles)) return;

        $mentioned = User::where(fn ($q) =>
            $q->whereIn('username', $handles)->orWhereIn('name', $handles)
        )->get();

        foreach ($mentioned as $user) {
            $user->notify('mention', $actorId, $notifiable);
        }
    }
}
