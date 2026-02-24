<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $followingIds = $user->following()->pluck('users.id');
        $followingIds->push($user->id);

        $posts = Post::with(['user', 'likes', 'comments.user'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(5);

        // AJAX (infinite scroll) → return only post HTML + pagination meta
        if ($request->ajax()) {
            $html = '';
            foreach ($posts as $post) {
                $html .= view('components.post-card', compact('post'))->render();
            }
            return response()->json([
                'html'     => $html,
                'nextPage' => $posts->hasMorePages() ? $posts->currentPage() + 1 : null,
            ]);
        }

        // Stories: current user first, then followed users with active stories
        $storyUsers = User::whereIn('id', $followingIds)
            ->whereHas('stories')
            ->with(['stories' => fn ($q) => $q->latest()])
            ->get()
            ->sortByDesc(fn ($u) => $u->id === $user->id ? PHP_INT_MAX : 0)
            ->values();

        return view('feed.index', compact('posts', 'storyUsers'));
    }
}
