<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $followingIds = $user->following()->pluck('users.id');
        $followingIds->push($user->id);

        $posts = Post::with(['user', 'likes', 'comments.user'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }
}
