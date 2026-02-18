<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $posts = $user->posts()->with(['likes', 'comments'])->paginate(12);
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
