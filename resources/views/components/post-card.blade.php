<div style="background:#fff;border:1px solid var(--border);border-radius:12px;margin-bottom:24px;overflow:hidden;">
    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;">
        <a href="{{ route('profile.show', $post->user) }}">
            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}"
                 style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
        </a>
        <div style="flex:1;">
            <a href="{{ route('profile.show', $post->user) }}"
               style="font-weight:600;font-size:14px;color:var(--text);text-decoration:none;">
                {{ $post->user->username ?? $post->user->name }}
            </a>
            <div style="font-size:12px;color:var(--text-muted);">{{ $post->created_at->diffForHumans() }}</div>
        </div>
        @if(auth()->id() === $post->user_id)
            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                  onsubmit="return confirm('¿Eliminar este post?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:4px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                </button>
            </form>
        @endif
    </div>

    {{-- Image --}}
    <a href="{{ route('posts.show', $post) }}">
        <img src="{{ $post->image_url }}" alt="post"
             style="width:100%;display:block;max-height:600px;object-fit:cover;">
    </a>

    {{-- Actions --}}
    <div style="padding:12px 16px 4px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
            {{-- Like --}}
            <form method="POST" action="{{ route('likes.toggle', $post) }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;gap:6px;font-size:14px;color:var(--text);">
                    @if($post->isLikedBy(auth()->user()))
                        <svg viewBox="0 0 24 24" fill="#ed4956" stroke="#ed4956" stroke-width="2" style="width:24px;height:24px">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    @endif
                </button>
            </form>
            {{-- Comment icon --}}
            <a href="{{ route('posts.show', $post) }}" style="color:var(--text);display:flex;align-items:center;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </a>
        </div>

        {{-- Like count --}}
        @if($post->likes->count() > 0)
            <div style="font-size:14px;font-weight:600;margin-bottom:6px;">
                {{ $post->likes->count() }} {{ $post->likes->count() === 1 ? 'me gusta' : 'me gustas' }}
            </div>
        @endif

        {{-- Caption --}}
        @if($post->caption)
            <div style="font-size:14px;margin-bottom:8px;">
                <span style="font-weight:600;">{{ $post->user->username ?? $post->user->name }}</span>
                @rendertext($post->caption)
            </div>
        @endif

        {{-- Comments preview --}}
        @if($post->comments->count() > 0)
            <a href="{{ route('posts.show', $post) }}"
               style="font-size:14px;color:var(--text-muted);text-decoration:none;display:block;margin-bottom:6px;">
                Ver los {{ $post->comments->count() }} comentarios
            </a>
        @endif

        {{-- Add comment --}}
        <form method="POST" action="{{ route('comments.store', $post) }}"
              style="display:flex;gap:8px;border-top:1px solid var(--border);padding-top:10px;margin-top:6px;">
            @csrf
            <input type="text" name="body" placeholder="Añade un comentario..."
                   style="flex:1;border:none;outline:none;font-size:14px;background:transparent;font-family:inherit;">
            <button type="submit" class="btn-primary" style="padding:6px 12px;font-size:13px;">Publicar</button>
        </form>
    </div>
</div>
