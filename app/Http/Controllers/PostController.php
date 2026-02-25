<?php

namespace App\Http\Controllers;

use App\Helpers\Mention;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function explore()
    {
        $posts = Post::with(['user', 'likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('explore.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'   => 'required|image|max:5120',
            'caption' => 'nullable|string|max:2200',
        ]);

        $path = $request->file('image')->store('posts', 'public');

        $post = auth()->user()->posts()->create([
            'image_path' => $path,
            'caption'    => $request->caption,
        ]);

        // Notify any @mentioned users in the caption
        if ($request->caption) {
            Mention::notifyMentions($request->caption, auth()->id(), $post);
        }

        return redirect()->route('feed')->with('success', '¡Post publicado!');
    }

    public function show(Post $post)
    {
        $authId = auth()->id();

        $post->load([
            'user',
            'likes',
            'comments.user',
            // Eager load likes on each comment to avoid N+1
            'comments.likes',
        ]);

        // Pre-compute which comments the current user has liked
        // so the view never calls isLikedBy() (which hits the DB)
        $likedCommentIds = $post->comments
            ->filter(fn ($c) => $c->likes->contains('user_id', $authId))
            ->pluck('id')
            ->flip();   // flip so we can do isset($likedCommentIds[$id])

        return view('posts.show', compact('post', 'likedCommentIds'));
    }

    public function destroy(Post $post)
    {
        abort_if(auth()->id() !== $post->user_id, 403);

        Storage::disk('public')->delete($post->image_path);
        $post->delete();

        return redirect()->route('feed')->with('success', 'Post eliminado.');
    }
}
