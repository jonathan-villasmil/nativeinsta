<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** Full-page search results */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $users = collect();

        if (strlen($query) >= 1) {
            $users = User::withCount('followers')
                ->where(function ($q) use ($query) {
                    $q->where('username', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%");
                })
                ->where('id', '!=', auth()->id())
                ->orderByDesc('followers_count')
                ->limit(20)
                ->get();
        }

        // AJAX partial — return only the results HTML (no layout)
        if ($request->header('X-Search-Partial')) {
            return view('search._results', compact('users', 'query'));
        }

        return view('search.index', compact('users', 'query'));
    }

    /** JSON autocomplete — used by the sidebar live-search dropdown */
    public function autocomplete(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $users = User::withCount('followers')
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->where('id', '!=', auth()->id())
            ->orderByDesc('followers_count')
            ->limit(6)
            ->get()
            ->map(fn ($u) => [
                'username'    => $u->username ?? $u->name,
                'name'        => $u->name,
                'avatar_url'  => $u->avatar_url,
                'profile_url' => route('profile.show', $u),
                'followers'   => $u->followers_count,
            ]);

        return response()->json($users);
    }
}
