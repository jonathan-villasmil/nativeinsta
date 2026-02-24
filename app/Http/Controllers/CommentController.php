<?php

namespace App\Http\Controllers;

use App\Helpers\Mention;
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

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        // Notify post owner (comment)
        $post->user->notify('comment', auth()->id(), $post);

        // Notify any @mentioned users
        Mention::notifyMentions($request->body, auth()->id(), $comment);

        return back();
    }

    public function update(\Illuminate\Http\Request $request, Comment $comment)
    {
        abort_if(auth()->id() !== $comment->user_id, 403);

        $request->validate(['body' => 'required|string|max:500']);

        $comment->update(['body' => $request->body]);

        return back();
    }

    public function destroy(Comment $comment)
    {
        abort_if(auth()->id() !== $comment->user_id, 403);
        $comment->delete();

        return back();
    }
}
