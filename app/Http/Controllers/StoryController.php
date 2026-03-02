<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    /**
     * Show all stories of a specific user (viewer mode).
     * Marks each story as viewed and returns JSON for the modal viewer.
     */
    public function show(User $user)
    {
        $stories = $user->stories()->with('user')->get();

        if ($stories->isEmpty()) {
            abort(404);
        }

        // Mark all stories of this user as viewed
        foreach ($stories as $story) {
            $story->views()->firstOrCreate(['user_id' => auth()->id()]);
        }

        return response()->json([
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'avatar_url' => $user->avatar_url,
                'profile_url'=> route('profile.show', $user),
            ],
            'stories' => $stories->map(fn ($s) => [
                'id'         => $s->id,
                'image_url'  => $s->image_url,
                'created_at' => $s->created_at->diffForHumans(),
                'expires_at' => $s->expires_at->toIso8601String(),
                'view_count' => $s->views()->count(),
                'like_count' => $s->likes()->count(),
                'is_liked'   => $s->isLikedBy(auth()->user()),
                'is_owner'   => $s->user_id === auth()->id(),
            ]),
        ]);
    }

    /**
     * Toggle like on a story.
     */
    public function toggleLike(Story $story)
    {
        $user = auth()->user();
        $like = $story->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $story->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $story->likes()->count(),
        ]);
    }

    /**
     * Return the list of viewers for a story (owner only).
     */
    public function views(Story $story)
    {
        abort_if($story->user_id !== auth()->id(), 403);

        $viewers = $story->views()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn ($v) => [
                'username'    => $v->user->username ?? $v->user->name,
                'name'        => $v->user->name,
                'avatar_url'  => $v->user->avatar_url,
                'profile_url' => route('profile.show', $v->user),
            ]);

        return response()->json([
            'count'   => $viewers->count(),
            'viewers' => $viewers,
        ]);
    }

    /**
     * Return the list of likers for a story (owner only).
     */
    public function likers(Story $story)
    {
        abort_if($story->user_id !== auth()->id(), 403);

        $likers = $story->likes()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn ($l) => [
                'username'    => $l->user->username ?? $l->user->name,
                'name'        => $l->user->name,
                'avatar_url'  => $l->user->avatar_url,
                'profile_url' => route('profile.show', $l->user),
            ]);

        return response()->json([
            'count'  => $likers->count(),
            'likers' => $likers,
        ]);
    }

    /**
     * Store a new story (image upload).
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $path = $request->file('image')->store('stories', 'public');

        auth()->user()->stories()->create([
            'image_path' => $path,
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Story publicada. Desaparecerá en 24 horas.');
    }

    /**
     * Delete own story.
     */
    public function destroy(Story $story)
    {
        abort_if(auth()->id() !== $story->user_id, 403);
        $story->delete();

        return back()->with('success', 'Story eliminada.');
    }
}
