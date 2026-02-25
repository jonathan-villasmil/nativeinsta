<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user, Request $request)
    {
        $posts = $user->posts()->with(['likes', 'comments'])->latest()->paginate(12);

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

        return view('profile.show', compact('user', 'posts'));
    }

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:30|unique:users,username,' . $user->id,
            'bio'      => 'nullable|string|max:150',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'username', 'bio');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show', $user)->with('success', 'Perfil actualizado.');
    }
}
