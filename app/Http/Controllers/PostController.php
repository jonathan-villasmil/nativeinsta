<?php

namespace App\Http\Controllers;

use App\Helpers\Mention;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function explore(Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments'])
            ->latest()
            ->paginate(12);

        if ($request->ajax()) {
            $html = '';
            foreach ($posts as $post) {
                $likes    = $post->likes->count();
                $comments = $post->comments->count();
                $url      = route('posts.show', $post);
                $img      = $post->image_url;
                $html .= <<<HTML
<a href="{$url}" style="display:block;aspect-ratio:1;overflow:hidden;position:relative;background:#efefef;">
    <img src="{$img}" alt="post" style="width:100%;height:100%;object-fit:cover;transition:opacity 0.2s;"
         onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:16px;
                background:rgba(0,0,0,0);color:#fff;font-weight:600;font-size:15px;
                opacity:0;transition:all 0.2s;"
         onmouseover="this.style.opacity='1';this.style.background='rgba(0,0,0,0.35)'"
         onmouseout="this.style.opacity='0';this.style.background='rgba(0,0,0,0)'">
        <span>❤️ {$likes}</span>
        <span>💬 {$comments}</span>
    </div>
</a>
HTML;
            }
            return response()->json([
                'html'     => $html,
                'nextPage' => $posts->hasMorePages() ? $posts->currentPage() + 1 : null,
            ]);
        }

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
