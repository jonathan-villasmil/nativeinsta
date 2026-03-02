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
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex-shrink:0;">
                <div style="position:relative;width:60px;height:60px;">
                    {{-- Avatar — click to VIEW own stories if any exist --}}
                    <img src="{{ auth()->user()->avatar_url }}" alt="tú"
                         onclick="{{ $myStories->isNotEmpty() ? 'loadStory('.auth()->id().')' : 'openStoryUpload()' }}"
                         style="width:60px;height:60px;border-radius:50%;object-fit:cover;cursor:pointer;
                                border:2px solid {{ $myStories->isNotEmpty() ? '#e1306c' : '#dbdbdb' }};">
                    {{-- "+" badge — always opens upload modal --}}
                    <div onclick="openStoryUpload()"
                         style="position:absolute;bottom:0;right:0;width:20px;height:20px;
                                background:#0095f6;border-radius:50%;border:2px solid #fff;
                                display:flex;align-items:center;justify-content:center;cursor:pointer;">
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
    <div id="story-viewer"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.92);
                z-index:2000;align-items:center;justify-content:center;"
         onclick="_svBackdropClick(event)">
        {{-- Inner card — click here does NOT close --}}
        <div id="story-card"
             style="position:relative;width:100%;max-width:420px;height:100vh;
                    max-height:100vh;overflow:hidden;">
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

        {{-- Bottom action bar --}}
        <div id="sv-action-bar"
             style="position:absolute;bottom:0;left:0;right:0;z-index:10;
                    padding:16px 20px;display:flex;align-items:center;justify-content:space-between;
                    background:linear-gradient(transparent,rgba(0,0,0,0.55));">

            {{-- Views button (owner only) --}}
            <button id="sv-views-btn" onclick="openViewers()"
                    style="display:none;align-items:center;gap:8px;background:none;border:none;
                           color:#fff;cursor:pointer;padding:0;font-family:inherit;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                     style="width:20px;height:20px;flex-shrink:0;">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span id="sv-view-count" style="font-size:14px;font-weight:600;">0</span>
                <span id="sv-like-count-owner"
                      style="font-size:13px;color:rgba(255,255,255,0.7);margin-left:8px;"></span>
            </button>

            <div></div>{{-- spacer when like btn is shown --}}

            {{-- Like button (non-owners only) --}}
            <button id="sv-like-btn" onclick="toggleStoryLike()"
                    style="display:none;align-items:center;gap:6px;background:none;border:none;
                           color:#fff;cursor:pointer;padding:0;font-family:inherit;">
                <svg id="sv-like-icon" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                     style="width:28px;height:28px;transition:transform 0.15s;">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span id="sv-like-count" style="font-size:14px;font-weight:600;">0</span>
            </button>
        </div>

        {{-- Viewer + Likers panel (slides up from bottom, owner only) --}}
        <div id="sv-viewer-panel"
             style="display:none;position:absolute;bottom:0;left:0;right:0;z-index:20;
                    background:rgba(24,24,24,0.97);border-radius:16px 16px 0 0;
                    max-height:65vh;overflow:hidden;flex-direction:column;">
            {{-- Tabs --}}
            <div style="display:flex;border-bottom:1px solid rgba(255,255,255,0.1);">
                <button onclick="showTab('views')" id="tab-views"
                        style="flex:1;background:none;border:none;color:#fff;padding:14px 0;
                               font-size:14px;font-weight:600;cursor:pointer;
                               border-bottom:2px solid #fff;font-family:inherit;">
                    👁 Vistos
                </button>
                <button onclick="showTab('likes')" id="tab-likes"
                        style="flex:1;background:none;border:none;color:rgba(255,255,255,0.5);
                               padding:14px 0;font-size:14px;font-weight:600;cursor:pointer;
                               border-bottom:2px solid transparent;font-family:inherit;">
                    ❤️ Likes
                </button>
                <button onclick="closeViewers()"
                        style="background:none;border:none;color:rgba(255,255,255,0.5);
                               font-size:22px;cursor:pointer;padding:0 16px;line-height:1;">×</button>
            </div>
            <div id="sv-viewer-list" style="overflow-y:auto;flex:1;padding:8px 0;">
                <p style="text-align:center;color:rgba(255,255,255,0.4);font-size:13px;padding:24px 0;">
                    Cargando...
                </p>
            </div>
        </div>

        {{-- Prev / Next tap zones --}}
        <div onclick="prevStory()"
             style="position:absolute;left:0;top:0;bottom:60px;width:35%;cursor:pointer;z-index:5;"></div>
        <div onclick="nextStory()"
             style="position:absolute;right:0;top:0;bottom:60px;width:35%;cursor:pointer;z-index:5;"></div>
        </div>{{-- /story-card --}}
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
@keyframes heartFadeOut {
    0%   { opacity: 1; }
    70%  { opacity: 1; }
    100% { opacity: 0; }
}
.dbl-heart { animation: none; }
.dbl-heart.show { display: flex !important; animation: heartFadeOut 0.8s ease forwards; }

/* Story modal animations */
@keyframes svFadeIn  { from { opacity:0; } to { opacity:1; } }
@keyframes svFadeOut { from { opacity:1; } to { opacity:0; } }
@keyframes svCardIn  { from { transform:scale(0.92);opacity:0; } to { transform:scale(1);opacity:1; } }
#story-viewer.sv-opening { animation: svFadeIn  0.25s ease forwards; }
#story-viewer.sv-closing { animation: svFadeOut 0.25s ease forwards; }
#story-card.sv-card-in   { animation: svCardIn  0.25s cubic-bezier(.34,1.56,.64,1) forwards; }
</style>
<script>
    // ─── Double-click / double-tap to like ──────────────────────
    var _tapTimers = {};
    var _tapCounts = {};

    function imgTap(e, wrap) {
        e.preventDefault();
        var id = wrap.dataset.postId;
        _tapCounts[id] = (_tapCounts[id] || 0) + 1;

        if (_tapCounts[id] >= 2) {
            // Double tap → like
            clearTimeout(_tapTimers[id]);
            _tapCounts[id] = 0;
            playHeart(wrap);
            sendLike(wrap);
            return;
        }

        // First tap — wait to see if a second comes
        _tapTimers[id] = setTimeout(function() {
            _tapCounts[id] = 0;
            window.location.href = wrap.dataset.postUrl;
        }, 280);
    }

    function playHeart(wrap) {
        var overlay = wrap.querySelector('.dbl-heart');
        var icon    = wrap.querySelector('.dbl-heart-icon');
        icon.style.transform = 'scale(0)';
        overlay.classList.remove('show');
        overlay.style.display = 'flex';
        void icon.offsetWidth; // force reflow
        icon.style.transform = 'scale(1)';
        overlay.classList.add('show');
        overlay.addEventListener('animationend', function() {
            overlay.style.display = 'none';
            overlay.classList.remove('show');
        }, { once: true });
    }

    function sendLike(wrap) {
        var url   = wrap.dataset.likeUrl;
        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(data) {
            if (!data) return;
            wrap.dataset.liked = data.liked ? '1' : '0';
            // Update heart icon in action bar
            var card    = wrap.closest('div[style*="border-radius:12px"]') || wrap.parentElement.parentElement;
            var likeBtn = card.querySelector('form[action*="/likes/"] svg');
            if (likeBtn) {
                likeBtn.setAttribute('fill',   data.liked ? '#ed4956' : 'none');
                likeBtn.setAttribute('stroke', data.liked ? '#ed4956' : 'currentColor');
            }
            // Update like count
            var countEl = card.querySelector('[data-like-count]');
            if (countEl && data.count !== undefined) {
                countEl.textContent = data.count + (data.count === 1 ? ' me gusta' : ' me gustas');
                if (countEl.parentElement) countEl.parentElement.style.display = data.count > 0 ? 'block' : 'none';
            }
        })
        .catch(function(){});
    }

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
        const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        const res = await fetch(`/stories/${userId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }
        });
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

        // Open modal (once per loadStory call)
        const viewer = document.getElementById('story-viewer');
        const card   = document.getElementById('story-card');
        const wasHidden = viewer.style.display === 'none';
        viewer.style.display = 'flex';
        if (wasHidden) {
            viewer.classList.remove('sv-closing');
            viewer.classList.add('sv-opening');
            card.classList.remove('sv-card-in');
            void card.offsetWidth; // reflow
            card.classList.add('sv-card-in');
            viewer.addEventListener('animationend', () => viewer.classList.remove('sv-opening'), { once: true });
        }

        // Bottom action bar: owner sees eye button, others see heart
        const viewsBtn = document.getElementById('sv-views-btn');
        const likeBtn  = document.getElementById('sv-like-btn');
        if (s.is_owner) {
            const vc = s.view_count;
            const lc = s.like_count;
            document.getElementById('sv-view-count').textContent =
                vc + (vc === 1 ? ' persona' : ' personas');
            document.getElementById('sv-like-count-owner').textContent =
                lc > 0 ? `· ❤️ ${lc}` : '';
            viewsBtn.style.display = 'flex';
            likeBtn.style.display  = 'none';
        } else {
            // Like button state
            const icon = document.getElementById('sv-like-icon');
            icon.setAttribute('fill',   s.is_liked ? '#ed4956' : 'none');
            icon.setAttribute('stroke', s.is_liked ? '#ed4956' : 'white');
            document.getElementById('sv-like-count').textContent = s.like_count || '';
            viewsBtn.style.display = 'none';
            likeBtn.style.display  = 'flex';
        }
        closeViewers(); // close panel when switching stories

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
    function _doClose() {
        document.getElementById('story-viewer').style.display = 'none';
        closeViewers();
        _stories = []; _idx = 0;
    }
    function closeViewer() {
        clearTimeout(_timer);
        const viewer = document.getElementById('story-viewer');
        viewer.classList.remove('sv-opening');
        viewer.classList.add('sv-closing');
        viewer.addEventListener('animationend', _doClose, { once: true });
    }
    function _svBackdropClick(e) {
        // Close only if the click landed directly on the backdrop (not inside the card)
        if (e.target === document.getElementById('story-viewer')) closeViewer();
    }

    // ─── Like button ───────────────────────────────────────────
    function toggleStoryLike() {
        const s = _stories[_idx];
        if (!s || s.is_owner) return;
        const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        fetch(`/stories/${s.id}/like`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            s.is_liked   = data.liked;
            s.like_count = data.count;
            const icon = document.getElementById('sv-like-icon');
            icon.setAttribute('fill',   data.liked ? '#ed4956' : 'none');
            icon.setAttribute('stroke', data.liked ? '#ed4956' : 'white');
            icon.style.transform = 'scale(1.3)';
            setTimeout(() => icon.style.transform = 'scale(1)', 150);
            document.getElementById('sv-like-count').textContent = data.count || '';
        })
        .catch(() => {});
    }

    // ─── Viewer / Likes panel ──────────────────────────────────
    let _activeTab = 'views';

    function showTab(tab) {
        _activeTab = tab;
        const tViews = document.getElementById('tab-views');
        const tLikes = document.getElementById('tab-likes');
        tViews.style.color        = tab === 'views' ? '#fff' : 'rgba(255,255,255,0.5)';
        tViews.style.borderBottom = tab === 'views' ? '2px solid #fff' : '2px solid transparent';
        tLikes.style.color        = tab === 'likes' ? '#fff' : 'rgba(255,255,255,0.5)';
        tLikes.style.borderBottom = tab === 'likes' ? '2px solid #fff' : '2px solid transparent';
        loadTabContent(tab);
    }

    function loadTabContent(tab) {
        const s = _stories[_idx];
        const list = document.getElementById('sv-viewer-list');
        list.innerHTML = '<p style="text-align:center;color:rgba(255,255,255,0.4);font-size:13px;padding:24px 0;">Cargando...</p>';
        const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        const url = tab === 'views'
            ? `/stories/${s.id}/views`
            : `/stories/${s.id}/likes`;
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }
        })
        .then(r => r.json())
        .then(data => {
            const items = data.viewers || data.likers || [];
            const empty = tab === 'views' ? 'Nadie lo ha visto todavía' : 'Nadie le ha dado like todavía';
            list.innerHTML = !items.length
                ? `<p style="text-align:center;color:rgba(255,255,255,0.4);font-size:13px;padding:24px 0;">${empty}</p>`
                : items.map(v => `
                    <a href="${v.profile_url}"
                       style="display:flex;align-items:center;gap:12px;padding:10px 16px;
                              text-decoration:none;color:#fff;">
                        <img src="${v.avatar_url}"
                             style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;
                                    border:1px solid rgba(255,255,255,0.2);">
                        <div>
                            <div style="font-weight:600;font-size:14px;">${v.username}</div>
                            <div style="font-size:12px;color:rgba(255,255,255,0.5);">${v.name}</div>
                        </div>
                    </a>`).join('');
        })
        .catch(() => {
            list.innerHTML = '<p style="text-align:center;color:rgba(255,255,255,0.4);font-size:13px;padding:24px 0;">Error al cargar</p>';
        });
    }

    function openViewers() {
        const s = _stories[_idx];
        if (!s || !s.is_owner) return;
        clearTimeout(_timer);
        document.getElementById('sv-viewer-panel').style.display = 'flex';
        showTab('views');
    }

    function closeViewers() {
        const panel = document.getElementById('sv-viewer-panel');
        if (panel) panel.style.display = 'none';
        if (_stories.length && document.getElementById('story-viewer').style.display !== 'none') {
            clearTimeout(_timer);
            _timer = setTimeout(nextStory, DURATION);
        }
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
