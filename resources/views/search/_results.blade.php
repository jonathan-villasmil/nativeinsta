{{-- search/_results.blade.php — rendered by both full page & AJAX --}}

@if(strlen($query) >= 1)
    @if($users->isEmpty())
        <div style="text-align:center;padding:48px 0;color:var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <p style="font-size:14px;margin:0;">
                No se encontraron resultados para <strong>{{ $query }}</strong>
            </p>
        </div>
    @else
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">
            {{ $users->count() }} {{ $users->count() === 1 ? 'resultado' : 'resultados' }}
        </div>

        @foreach($users as $user)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;
                        border-bottom:1px solid var(--border);">
                <a href="{{ route('profile.show', $user) }}" style="flex-shrink:0;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:1px solid var(--border);">
                </a>
                <div style="flex:1;min-width:0;">
                    <a href="{{ route('profile.show', $user) }}"
                       style="font-weight:600;font-size:14px;color:var(--text);text-decoration:none;display:block;">
                        {{ $user->username ?? $user->name }}
                    </a>
                    <div style="font-size:13px;color:var(--text-muted);">{{ $user->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                        {{ $user->followers_count }} seguidores
                    </div>
                </div>
                @if(auth()->user()->isFollowing($user))
                    <form method="POST" action="{{ route('follow.destroy', $user) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="background:transparent;border:1px solid var(--border);border-radius:8px;
                                       padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;
                                       color:var(--text);font-family:inherit;white-space:nowrap;">
                            Siguiendo
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('follow.store', $user) }}">
                        @csrf
                        <button type="submit"
                                style="background:var(--primary);border:none;border-radius:8px;
                                       padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;
                                       color:#fff;font-family:inherit;white-space:nowrap;">
                            Seguir
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    @endif
@else
    {{-- Suggested users when no query --}}
    @php
        $suggested = App\Models\User::withCount('followers')
            ->where('id', '!=', auth()->id())
            ->whereNotIn('id', auth()->user()->following()->pluck('users.id'))
            ->orderByDesc('followers_count')
            ->limit(6)
            ->get();
    @endphp

    @if($suggested->isNotEmpty())
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;font-weight:600;">
            Sugerencias para ti
        </div>
        @foreach($suggested as $user)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;
                        border-bottom:1px solid var(--border);">
                <a href="{{ route('profile.show', $user) }}" style="flex-shrink:0;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:1px solid var(--border);">
                </a>
                <div style="flex:1;min-width:0;">
                    <a href="{{ route('profile.show', $user) }}"
                       style="font-weight:600;font-size:14px;color:var(--text);text-decoration:none;display:block;">
                        {{ $user->username ?? $user->name }}
                    </a>
                    <div style="font-size:13px;color:var(--text-muted);">{{ $user->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                        {{ $user->followers_count }} seguidores
                    </div>
                </div>
                <form method="POST" action="{{ route('follow.store', $user) }}">
                    @csrf
                    <button type="submit"
                            style="background:var(--primary);border:none;border-radius:8px;
                                   padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;
                                   color:#fff;font-family:inherit;white-space:nowrap;">
                        Seguir
                    </button>
                </form>
            </div>
        @endforeach
    @endif
@endif
