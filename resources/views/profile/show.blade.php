@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 32px 16px;">
    {{-- Profile header --}}
    <div style="display:flex;align-items:flex-start;gap:40px;margin-bottom:40px;padding-bottom:32px;border-bottom:1px solid var(--border);">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
             style="width:120px;height:120px;border-radius:50%;object-fit:cover;flex-shrink:0;border:3px solid var(--border);">

        <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:16px;flex-wrap:wrap;">
                <h1 style="font-size:24px;font-weight:300;margin:0;">{{ $user->username ?? $user->name }}</h1>

                @if(auth()->id() === $user->id)
                    <a href="{{ route('profile.edit') }}" class="btn-outline">Editar perfil</a>
                @elseif(auth()->user()->isFollowing($user))
                    <form method="POST" action="{{ route('follow.destroy', $user) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-outline">Siguiendo</button>
                    </form>
                    <a href="{{ route('messages.show', $user) }}" class="btn-outline">Mensaje</a>
                @else
                    <form method="POST" action="{{ route('follow.store', $user) }}">
                        @csrf
                        <button type="submit" class="btn-primary">Seguir</button>
                    </form>
                    <a href="{{ route('messages.show', $user) }}" class="btn-outline">Mensaje</a>
                @endif
            </div>

            <div style="display:flex;gap:32px;margin-bottom:16px;">
                <div style="font-size:15px;">
                    <strong>{{ $posts->total() }}</strong> posts
                </div>
                <div style="font-size:15px;">
                    <strong>{{ $user->followers()->count() }}</strong> seguidores
                </div>
                <div style="font-size:15px;">
                    <strong>{{ $user->following()->count() }}</strong> siguiendo
                </div>
            </div>

            <div>
                <div style="font-weight:600;font-size:14px;">{{ $user->name }}</div>
                @if($user->bio)
                    <div style="font-size:14px;margin-top:4px;white-space:pre-line;">{{ $user->bio }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Posts grid --}}
    @if($posts->isEmpty())
        <div style="text-align:center;padding:60px 0;color:var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <p style="font-size:14px;margin:0;">Aún no hay posts</p>
        </div>
    @else
        <div id="profile-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:3px;">
            @foreach($posts as $post)
                <a href="{{ route('posts.show', $post) }}" style="display:block;aspect-ratio:1;overflow:hidden;position:relative;background:#efefef;">
                    <img src="{{ $post->image_url }}" alt="post"
                         style="width:100%;height:100%;object-fit:cover;transition:opacity 0.2s;"
                         onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:16px;
                                background:rgba(0,0,0,0);color:#fff;font-weight:600;font-size:14px;
                                opacity:0;transition:all 0.2s;"
                         onmouseover="this.style.opacity='1';this.style.background='rgba(0,0,0,0.35)'"
                         onmouseout="this.style.opacity='0';this.style.background='rgba(0,0,0,0)'">
                        <span>❤️ {{ $post->likes->count() }}</span>
                        <span>💬 {{ $post->comments->count() }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Infinite scroll sentinel --}}
        <div id="profile-sentinel" style="height:40px;display:flex;align-items:center;justify-content:center;margin-top:8px;">
            <div id="profile-spinner" style="display:none;">
                <svg width="28" height="28" viewBox="0 0 50 50" style="animation:spin 0.8s linear infinite;">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="var(--primary)" stroke-width="4"
                            stroke-dasharray="80" stroke-dashoffset="60"/>
                </svg>
            </div>
            <p id="profile-end" style="display:none;color:var(--text-muted);font-size:13px;margin:0;">Ya has visto todo 👋</p>
        </div>

        @if($posts->hasMorePages())
            <div id="profile-next"
                 data-page="{{ $posts->currentPage() + 1 }}"
                 data-url="{{ route('profile.show', $user) }}"></div>
        @else
            <p style="text-align:center;color:var(--text-muted);font-size:13px;margin:8px 0 32px;">Ya has visto todo 👋</p>
        @endif

        <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
        <script>
        (function () {
            const nextEl   = document.getElementById('profile-next');
            if (!nextEl) return;

            const grid     = document.getElementById('profile-grid');
            const sentinel = document.getElementById('profile-sentinel');
            const spinner  = document.getElementById('profile-spinner');
            const endMsg   = document.getElementById('profile-end');
            const baseUrl  = nextEl.dataset.url;
            let nextPage   = parseInt(nextEl.dataset.page, 10);
            let loading    = false;

            const observer = new IntersectionObserver(async (entries) => {
                if (!entries[0].isIntersecting || loading) return;
                loading = true;
                spinner.style.display = 'block';

                try {
                    const res  = await apiFetch(`${baseUrl}?page=${nextPage}`);
                    const data = await res.json();
                    grid.insertAdjacentHTML('beforeend', data.html);

                    if (data.nextPage) {
                        nextPage = data.nextPage;
                        loading  = false;
                        spinner.style.display = 'none';
                    } else {
                        observer.disconnect();
                        spinner.style.display = 'none';
                        endMsg.style.display  = 'block';
                    }
                } catch (e) {
                    spinner.style.display = 'none';
                    loading = false;
                }
            }, { threshold: 0.1 });

            observer.observe(sentinel);
        })();
        </script>
    @endif
</div>
@endsection
