<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $users = collect();

        if (strlen($query) >= 1) {
            $users = User::where('username', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->where('id', '!=', auth()->id())
                ->limit(20)
                ->get();
        }

        return view('search.index', compact('users', 'query'));
    }
}
