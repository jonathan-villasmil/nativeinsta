<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(User $user)
    {
        abort_if(auth()->id() === $user->id, 403, 'No puedes seguirte a ti mismo.');

        auth()->user()->following()->syncWithoutDetaching([$user->id]);

        return back();
    }

    public function destroy(User $user)
    {
        auth()->user()->following()->detach($user->id);

        return back();
    }
}
