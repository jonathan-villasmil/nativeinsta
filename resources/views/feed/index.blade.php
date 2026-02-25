@extends('layouts.app')

@section('content')
<div style="max-width:630px;margin:0 auto;padding:24px 16px;">

    {{-- ═══════════════════════════════════════════════════
         STORIES BAR
    ═══════════════════════════════════════════════════ --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;
                padding:16px;margin-bottom:24px;overflow:hidden;">

        <div style="display:flex;gap:16px;overflow-x:auto;padding-bottom:4px;
                    scrollbar-width:none;" id="stories-bar">

            {{-- Your story / Add story button --}}
            @php $myStories = auth()->user()->stories; @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex-shrink:0;cursor:pointer;"
                 onclick="openStoryUpload()">
                <div style="position:relative;width:60px;height:60px;">
                    <img src="{{ auth()->user()->avatar_url }}" alt="tú"
                         style="width:60px;height:60px;border-radius:50%;object-fit:cover;
                                border:2px solid {{ $myStories->isNotEmpty() ? '#e1306c' : '#dbdbdb' }};">
                    <div style="position:absolute;bottom:0;right:0;width:20px;height:20px;
                                background:#0095f6;border-radius:50%;border:2px solid #fff;
                                display:flex;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-size:14px;line-height:1;margin-top:-1px;">+</span>
                    </div>
                </div>
                <span style="font-size:11px;color:var(--text);max-width:60px;text-align:center;
                             white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Tu historia</span>
            </div>

            {{-- Other users' stories --}}
            @foreach($storyUsers as $storyUser)
                @if($storyUser->id !== auth()->id())
                    @php
                        $allViewed = $storyUser->stories->every(fn($s) => $s->isViewedBy(auth()->user()));
                        $ring = $allViewed ? '#dbdbdb' : 'linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)';
                    @endphp
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex-shrink:0;cursor:pointer;"
                         onclick="loadStory({{ $storyUser->id }})">
                        <div style="width:64px;height:64px;border-radius:50%;padding:2px;
                                    background:{{ $ring }};flex-shrink:0;">
                            <img src="{{ $storyUser->avatar_url }}" alt="{{ $storyUser->name }}"
                                 style="width:100%;height:100%;border-radius:50%;object-fit:cover;border:2px solid #fff;">
                        </div>
                        <span style="font-size:11px;color:var(--text);max-width:60px;text-align:center;
                                     white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $storyUser->username ?? $storyUser->name }}
                        </span>
                    </div>
                @endif
            @endforeach

            @if($storyUsers->isEmpty())
                <p style="color:var(--text-muted);font-size:13px;line-height:60px;margin:0;">
                    Sigue a alguien para ver sus stories
                </p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         UPLOAD STORY MODAL
    ═══════════════════════════════════════════════════ --}}
    <div id="upload-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);
                                   z-index:1000;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;padding:32px;width:340px;position:relative;">
            <button onclick="closeStoryUpload()"
                    style="position:absolute;top:12px;right:16px;background:none;border:none;
                           font-size:22px;cursor:pointer;color:#8e8e8e;">×</button>
            <h3 style="margin:0 0 20px;font-size:16px;font-weight:700;">Nueva historia</h3>
            <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data">
                @csrf
                <div id="story-preview"
                     style="width:100%;aspect-ratio:9/16;background:#f0f0f0;border-radius:12px;
                            margin-bottom:16px;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    <span style="color:#8e8e8e;font-size:13px;">Selecciona una imagen</span>
                </div>
                <input type="file" name="image" id="story-file" accept="image/*"
                       style="display:none;" onchange="previewStory(this)">
                <button type="button" onclick="document.getElementById('story-file').click()"
                        class="btn-outline" style="width:100%;justify-content:center;margin-bottom:12px;">
                    Elegir imagen
                </button>
                <button type="submit" id="story-submit" class="btn-primary"
                        style="width:100%;justify-content:center;" disabled>
                    Publicar historia
                </button>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STORY VIEWER MODAL
    ═══════════════════════════════════════════════════ --}}
    <div id="story-viewer" style="display:none;position:fixed;inset:0;background:#000;
                                   z-index:2000;align-items:center;justify-content:center;">
        {{-- Progress bars --}}
        <div id="story-progress-bar"
             style="position:absolute;top:0;left:0;right:0;display:flex;gap:4px;padding:12px 12px 0;z-index:10;">
        </div>

        {{-- Header --}}
        <div style="position:absolute;top:20px;left:0;right:0;display:flex;align-items:center;
                    justify-content:space-between;padding:0 16px;z-index:10;">
            <div style="display:flex;align-items:center;gap:10px;">
                <img id="sv-avatar" src="" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                <div>
                    <a id="sv-username" href="#" style="font-weight:600;font-size:13px;color:#fff;text-decoration:none;"></a>
                    <div id="sv-time" style="font-size:11px;color:rgba(255,255,255,0.7);"></div>
                </div>
            </div>
            <button onclick="closeViewer()"
                    style="background:none;border:none;color:#fff;font-size:28px;cursor:pointer;line-height:1;">×</button>
        </div>

        {{-- Story image --}}
        <img id="sv-image" src="" alt="story"
             style="max-height:100vh;max-width:100%;object-fit:contain;">

        {{-- Prev / Next tap zones --}}
        <div onclick="prevStory()"
             style="position:absolute;left:0;top:0;bottom:0;width:35%;cursor:pointer;z-index:5;"></div>
        <div onclick="nextStory()"
             style="position:absolute;right:0;top:0;bottom:0;width:35%;cursor:pointer;z-index:5;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         POSTS
    ═══════════════════════════════════════════════════ --}}
    @if($posts->isEmpty())
        <div style="text-align:center;padding:80px 0;color:var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 style="width:64px;height:64px;margin:0 auto 16px;display:block;opacity:0.4">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <h3 style="font-size:18px;font-weight:600;margin:0 0 8px;">Aún no hay posts</h3>
            <p style="font-size:14px;margin:0 0 20px;">Sigue a otros usuarios o sube tu primer post.</p>
            <a href="{{ route('explore') }}" class="btn-primary">Explorar</a>
        </div>
    @else
        <div id="posts-container">
            @foreach($posts as $post)
                @include('components.post-card', ['post' => $post])
            @endforeach
        </div>

        {{-- Infinite scroll sentinel --}}
        <div id="scroll-sentinel" style="height:40px;display:flex;align-items:center;justify-content:center;">
            <div id="scroll-spinner" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"
                     style="width:28px;height:28px;animation:spin 0.8s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke-dasharray="40" stroke-dashoffset="10"/>
                </svg>
            </div>
            <p id="scroll-end" style="display:none;color:var(--text-muted);font-size:13px;margin:0;">Ya has visto todo 👋</p>
        </div>
        @if($posts->hasMorePages())
            <div id="next-page" data-page="{{ $posts->currentPage() + 1 }}"></div>
        @else
            <p style="text-align:center;color:var(--text-muted);font-size:13px;margin:8px 0 32px;">Ya has visto todo 👋</p>
        @endif
    @endif
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
    // ─── Upload modal ──────────────────────────────────────────
    function openStoryUpload() {
        document.getElementById('upload-modal').style.display = 'flex';
    }
    function closeStoryUpload() {
        document.getElementById('upload-modal').style.display = 'none';
    }
    function previewStory(input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const box = document.getElementById('story-preview');
            box.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
            document.getElementById('story-submit').disabled = false;
        };
        reader.readAsDataURL(input.files[0]);
    }

    // ─── Viewer state ──────────────────────────────────────────
    let _stories = [];
    let _idx = 0;
    let _timer = null;
    const DURATION = 5000; // 5 seconds per story

    async function loadStory(userId) {
        const res = await apiFetch(`/stories/${userId}`);
        if (!res.ok) return;
        const data = await res.json();
        _stories = data.stories.map(s => ({ ...s, user: data.user }));
        _idx = 0;
        showStory();
    }

    function showStory() {
        if (_idx < 0 || _idx >= _stories.length) { closeViewer(); return; }
        const s = _stories[_idx];
        document.getElementById('sv-image').src    = s.image_url;
        document.getElementById('sv-avatar').src   = s.user.avatar_url;
        document.getElementById('sv-username').textContent = s.user.username || s.user.name;
        document.getElementById('sv-username').href = s.user.profile_url;

        // Show "hace X · expira en Y"
        const expiresAt  = new Date(s.expires_at);
        const remaining  = expiresAt - Date.now();
        const hoursLeft  = Math.max(0, Math.floor(remaining / 36e5));
        const minsLeft   = Math.max(0, Math.floor((remaining % 36e5) / 6e4));
        const timeLeft   = hoursLeft > 0 ? `${hoursLeft}h` : `${minsLeft}m`;
        document.getElementById('sv-time').textContent =
            `${s.created_at} · expira en ${timeLeft}`;

        document.getElementById('story-viewer').style.display = 'flex';

        // Progress bars
        const bar = document.getElementById('story-progress-bar');
        bar.innerHTML = _stories.map((_, i) => `
            <div style="flex:1;height:2px;background:rgba(255,255,255,0.4);border-radius:2px;overflow:hidden;">
                <div id="pb-${i}" style="height:100%;background:#fff;width:${i < _idx ? '100' : '0'}%;
                     transition:${i === _idx ? `width ${DURATION}ms linear` : 'none'};"></div>
            </div>`).join('');

        clearTimeout(_timer);
        // Trigger animation
        setTimeout(() => {
            const pb = document.getElementById(`pb-${_idx}`);
            if (pb) pb.style.width = '100%';
        }, 50);

        _timer = setTimeout(nextStory, DURATION);
    }

    function nextStory() {
        clearTimeout(_timer);
        _idx++;
        showStory();
    }
    function prevStory() {
        clearTimeout(_timer);
        _idx = Math.max(0, _idx - 1);
        showStory();
    }
    function closeViewer() {
        clearTimeout(_timer);
        document.getElementById('story-viewer').style.display = 'none';
        _stories = []; _idx = 0;
    }

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeViewer(); closeStoryUpload(); }
        if (e.key === 'ArrowRight') nextStory();
        if (e.key === 'ArrowLeft')  prevStory();
    });

    // ─── Infinite scroll ───────────────────────────────────────
    (function () {
        const sentinel  = document.getElementById('scroll-sentinel');
        const nextMeta  = document.getElementById('next-page');
        const container = document.getElementById('posts-container');
        const spinner   = document.getElementById('scroll-spinner');
        const endMsg    = document.getElementById('scroll-end');

        if (!sentinel || !nextMeta || !container) return; // no posts / no more pages

        let loading = false;
        let nextPage = parseInt(nextMeta.dataset.page, 10);

        const observer = new IntersectionObserver(async (entries) => {
            if (!entries[0].isIntersecting || loading) return;
            loading = true;
            spinner.style.display = 'block';

            try {
                const res  = await apiFetch(`/feed?page=${nextPage}`);
                const data = await res.json();

                // Append new post cards
                container.insertAdjacentHTML('beforeend', data.html);

                if (data.nextPage) {
                    nextPage = data.nextPage;
                    loading  = false;
                    spinner.style.display = 'none';
                } else {
                    // No more pages — stop observing
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
@endsection
