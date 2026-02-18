<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:500',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return back();
    }

    public function destroy(Comment $comment)
    {
        abort_if(auth()->id() !== $comment->user_id, 403);
        $comment->delete();

        return back();
    }
}
