@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 20px;">Buscar personas</h2>

    {{-- Search form --}}
    <form method="GET" action="{{ route('search') }}" id="search-form">
        <div style="position:relative;margin-bottom:24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#8e8e8e" stroke-width="2"
                 style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:18px;height:18px;pointer-events:none;">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" id="search-input"
                   value="{{ $query }}"
                   placeholder="Buscar por nombre o usuario..."
                   autocomplete="off"
                   style="width:100%;background:#efefef;border:none;border-radius:10px;
                          padding:10px 12px 10px 40px;font-size:14px;outline:none;
                          box-sizing:border-box;font-family:inherit;color:#262626;"
                   oninput="this.form.submit()">
        </div>
    </form>

    {{-- Results --}}
    @if(strlen($query) >= 1)
        @if($users->isEmpty())
            <div style="text-align:center;padding:48px 0;color:#8e8e8e;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                     style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <p style="font-size:14px;margin:0;">No se encontraron resultados para <strong>{{ $query }}</strong></p>
            </div>
        @else
            <div style="font-size:13px;color:#8e8e8e;margin-bottom:12px;">
                {{ $users->count() }} {{ $users->count() === 1 ? 'resultado' : 'resultados' }}
            </div>

            @foreach($users as $user)
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;
                            border-bottom:1px solid #efefef;">
                    {{-- Avatar --}}
                    <a href="{{ route('profile.show', $user) }}" style="flex-shrink:0;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:1px solid #dbdbdb;">
                    </a>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('profile.show', $user) }}"
                           style="font-weight:600;font-size:14px;color:#262626;text-decoration:none;display:block;">
                            {{ $user->username ?? $user->name }}
                        </a>
                        <div style="font-size:13px;color:#8e8e8e;">{{ $user->name }}</div>
                        <div style="font-size:12px;color:#8e8e8e;margin-top:2px;">
                            {{ $user->followers()->count() }} seguidores
                        </div>
                    </div>

                    {{-- Follow button --}}
                    @if(auth()->user()->isFollowing($user))
                        <form method="POST" action="{{ route('follow.destroy', $user) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:transparent;border:1px solid #dbdbdb;border-radius:8px;
                                           padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;
                                           color:#262626;font-family:inherit;white-space:nowrap;">
                                Siguiendo
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('follow.store', $user) }}">
                            @csrf
                            <button type="submit"
                                    style="background:#0095f6;border:none;border-radius:8px;
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
            $suggested = App\Models\User::where('id', '!=', auth()->id())
                ->whereNotIn('id', auth()->user()->following()->pluck('users.id'))
                ->inRandomOrder()
                ->limit(5)
                ->get();
        @endphp

        @if($suggested->isNotEmpty())
            <div style="font-size:13px;color:#8e8e8e;margin-bottom:12px;font-weight:600;">
                Sugerencias para ti
            </div>
            @foreach($suggested as $user)
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;
                            border-bottom:1px solid #efefef;">
                    <a href="{{ route('profile.show', $user) }}" style="flex-shrink:0;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:1px solid #dbdbdb;">
                    </a>
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('profile.show', $user) }}"
                           style="font-weight:600;font-size:14px;color:#262626;text-decoration:none;display:block;">
                            {{ $user->username ?? $user->name }}
                        </a>
                        <div style="font-size:13px;color:#8e8e8e;">{{ $user->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('follow.store', $user) }}">
                        @csrf
                        <button type="submit"
                                style="background:#0095f6;border:none;border-radius:8px;
                                       padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;
                                       color:#fff;font-family:inherit;white-space:nowrap;">
                            Seguir
                        </button>
                    </form>
                </div>
            @endforeach
        @endif
    @endif
</div>
@endsection
