<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    public function show(string $tag)
    {
        // Case-insensitive search in caption using LIKE
        $posts = Post::with(['user', 'likes', 'comments'])
            ->where('caption', 'like', '%#' . $tag . '%')
            ->latest()
            ->paginate(12);

        return view('hashtags.show', compact('posts', 'tag'));
    }
}
