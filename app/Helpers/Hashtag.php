<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;

class Hashtag
{
    /**
     * Parse #hashtag references in text and turn them into clickable links.
     * Returns an HtmlString safe for {!! !!} / @hashtag() rendering.
     */
    public static function parse(string $text): HtmlString
    {
        // Escape first, then replace #tag with <a>
        $escaped = e($text);

        $html = preg_replace_callback('/#([\w\x{00C0}-\x{024F}]+)/u', function ($m) {
            $tag = $m[1];
            $url = route('hashtags.show', $tag);
            return '<a href="' . e($url) . '" '
                . 'style="color:#00376b;font-weight:600;text-decoration:none;" '
                . 'onmouseover="this.style.textDecoration=\'underline\'" '
                . 'onmouseout="this.style.textDecoration=\'none\'">'
                . '#' . e($tag)
                . '</a>';
        }, $escaped);

        return new HtmlString($html);
    }
}
