<?php

namespace App\Http\Controllers;

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
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:2200',
        ]);

        $path = $request->file('image')->store('posts', 'public');

        auth()->user()->posts()->create([
            'image_path' => $path,
            'caption' => $request->caption,
        ]);

        return redirect()->route('feed')->with('success', '¡Post publicado!');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'likes', 'comments.user']);
        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        abort_if(auth()->id() !== $post->user_id, 403);

        Storage::disk('public')->delete($post->image_path);
        $post->delete();

        return redirect()->route('feed')->with('success', 'Post eliminado.');
    }
}
