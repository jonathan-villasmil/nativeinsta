@extends('layouts.app')

@section('content')
<div style="padding: 32px 32px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 24px;">Explorar</h2>

    @if($posts->isEmpty())
        <div style="text-align:center; padding: 80px 0; color: var(--text-muted);">
            <p>Aún no hay posts. ¡Sé el primero!</p>
            <a href="{{ route('posts.create') }}" class="btn-primary" style="margin-top:12px;">Crear post</a>
        </div>
    @else
        <div id="explore-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:3px;">
            @foreach($posts as $post)
                <a href="{{ route('posts.show', $post) }}" style="display:block;aspect-ratio:1;overflow:hidden;position:relative;background:#efefef;">
                    <img src="{{ $post->image_url }}" alt="post"
                         style="width:100%;height:100%;object-fit:cover;transition:opacity 0.2s;"
                         onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:16px;
                                background:rgba(0,0,0,0);color:#fff;font-weight:600;font-size:15px;
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
        <div id="explore-sentinel" style="height:40px;display:flex;align-items:center;justify-content:center;margin-top:8px;">
            <div id="explore-spinner" style="display:none;">
                <svg width="28" height="28" viewBox="0 0 50 50" style="animation:spin 0.8s linear infinite;">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="var(--primary)" stroke-width="4"
                            stroke-dasharray="80" stroke-dashoffset="60"/>
                </svg>
            </div>
            <p id="explore-end" style="display:none;color:var(--text-muted);font-size:13px;margin:0;">Ya has visto todo 👋</p>
        </div>

        @if($posts->hasMorePages())
            <div id="explore-next" data-page="{{ $posts->currentPage() + 1 }}" data-url="{{ url()->current() }}"></div>
        @else
            <p style="text-align:center;color:var(--text-muted);font-size:13px;margin:8px 0 32px;">Ya has visto todo 👋</p>
        @endif

        <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
        <script>
        (function () {
            const nextEl   = document.getElementById('explore-next');
            if (!nextEl) return; // nothing more to load

            const grid     = document.getElementById('explore-grid');
            const sentinel = document.getElementById('explore-sentinel');
            const spinner  = document.getElementById('explore-spinner');
            const endMsg   = document.getElementById('explore-end');
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
