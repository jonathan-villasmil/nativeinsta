<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentLikeController extends Controller
{
    public function toggle(Comment $comment)
    {
        $user = auth()->user();
        $existing = $comment->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
        }

        return back();
    }
}
