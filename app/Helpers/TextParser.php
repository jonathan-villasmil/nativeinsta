<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\HtmlString;

class TextParser
{
    /**
     * Parse a raw text string, replacing @mentions and #hashtags with links.
     * Escapes the input ONCE, then applies both replacements on the escaped text.
     */
    public static function render(string $text): HtmlString
    {
        // ── Step 1: escape HTML entities once ──────────────────────────────
        $escaped = e($text);

        // ── Step 2: @mention replacement ───────────────────────────────────
        preg_match_all('/@([\w.]+)/', $escaped, $matches);
        $handles = array_unique($matches[1] ?? []);

        $users = [];
        if (!empty($handles)) {
            $users = User::where(function ($q) use ($handles) {
                $q->whereIn('username', $handles)->orWhereIn('name', $handles);
            })->get()->keyBy(fn ($u) => strtolower($u->username ?? $u->name))->all();
        }

        $escaped = preg_replace_callback('/@([\w.]+)/', function ($m) use ($users) {
            $key = strtolower($m[1]);
            if (isset($users[$key])) {
                $url = route('profile.show', $users[$key]);
                return '<a href="' . $url . '" '
                    . 'style="font-weight:600;color:var(--text);text-decoration:none;" '
                    . 'onmouseover="this.style.textDecoration=\'underline\'" '
                    . 'onmouseout="this.style.textDecoration=\'none\'">'
                    . '@' . $m[1]
                    . '</a>';
            }
            return '@' . $m[1];
        }, $escaped);

        // ── Step 3: #hashtag replacement ───────────────────────────────────
        $escaped = preg_replace_callback('/#([\w\x{00C0}-\x{024F}]+)/u', function ($m) {
            $tag = $m[1];
            $url = route('hashtags.show', $tag);
            return '<a href="' . $url . '" '
                . 'style="color:#00376b;font-weight:600;text-decoration:none;" '
                . 'onmouseover="this.style.textDecoration=\'underline\'" '
                . 'onmouseout="this.style.textDecoration=\'none\'">'
                . '#' . $tag
                . '</a>';
        }, $escaped);

        return new HtmlString($escaped);
    }
}
