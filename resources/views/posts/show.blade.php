@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 32px 16px;">
    <div style="display:flex;gap:0;background:#fff;border:1px solid var(--border);border-radius:4px;overflow:hidden;min-height:500px;">
        {{-- Image --}}
        <div style="flex:1;background:#000;display:flex;align-items:center;justify-content:center;max-width:60%;">
            <img src="{{ $post->image_url }}" alt="post"
                 style="width:100%;max-height:600px;object-fit:contain;display:block;">
        </div>

        {{-- Sidebar --}}
        <div style="width:340px;flex-shrink:0;display:flex;flex-direction:column;border-left:1px solid var(--border);">
            {{-- User header --}}
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border);">
                <a href="{{ route('profile.show', $post->user) }}">
                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                </a>
                <a href="{{ route('profile.show', $post->user) }}"
                   style="font-weight:600;font-size:14px;color:var(--text);text-decoration:none;flex:1;">
                    {{ $post->user->username ?? $post->user->name }}
                </a>
                @if(auth()->id() === $post->user_id)
                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                          onsubmit="return confirm('¿Eliminar este post?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M9 6V4h6v2"/>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>

            {{-- Caption + comments --}}
            <div style="flex:1;overflow-y:auto;padding:16px;">
                @if($post->caption)
                    <div style="display:flex;gap:12px;margin-bottom:16px;">
                        <img src="{{ $post->user->avatar_url }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <div style="font-size:14px;">
                            <span style="font-weight:600;">{{ $post->user->username ?? $post->user->name }}</span>
                            @rendertext($post->caption)
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endif

                @foreach($post->comments as $comment)
                    @php $liked = $comment->isLikedBy(auth()->user()); @endphp
                    <div style="display:flex;gap:10px;margin-bottom:12px;align-items:flex-start;">
                        <img src="{{ $comment->user->avatar_url }}"
                             style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">

                        <div style="flex:1;font-size:14px;">
                            {{-- View mode --}}
                            <div id="comment-view-{{ $comment->id }}">
                                <span style="font-weight:600;">{{ $comment->user->username ?? $comment->user->name }}</span>
                                @rendertext($comment->body)
                                <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                                    <span style="font-size:11px;color:var(--text-muted);">
                                        {{ $comment->created_at->diffForHumans() }}
                                        @if($comment->updated_at->gt($comment->created_at->addSecond()))
                                            · <em>editado</em>
                                        @endif
                                    </span>
                                    @if($comment->likes()->count() > 0)
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:600;">
                                            {{ $comment->likes()->count() }} {{ $comment->likes()->count() === 1 ? 'me gusta' : 'me gustas' }}
                                        </span>
                                    @endif
                                    @if(auth()->id() === $comment->user_id)
                                        <button type="button"
                                                onclick="startEdit({{ $comment->id }}, {{ json_encode($comment->body) }})"
                                                style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;font-size:11px;">
                                            Editar
                                        </button>
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;font-size:11px;">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Edit mode (hidden by default) --}}
                            @if(auth()->id() === $comment->user_id)
                            <div id="comment-edit-{{ $comment->id }}" style="display:none;margin-top:4px;">
                                <form method="POST" action="{{ route('comments.update', $comment) }}">
                                    @csrf @method('PUT')
                                    <textarea id="comment-input-{{ $comment->id }}"
                                              name="body"
                                              maxlength="500"
                                              style="width:100%;border:1px solid var(--border);border-radius:8px;padding:8px 10px;
                                                     font-size:14px;font-family:inherit;resize:none;outline:none;
                                                     background:var(--bg);color:var(--text);line-height:1.4;"
                                              rows="2"></textarea>
                                    <div style="display:flex;gap:8px;margin-top:6px;">
                                        <button type="submit"
                                                style="background:var(--primary);color:#fff;border:none;border-radius:6px;
                                                       padding:5px 14px;font-size:13px;font-weight:600;cursor:pointer;">
                                            Guardar
                                        </button>
                                        <button type="button"
                                                onclick="cancelEdit({{ $comment->id }})"
                                                style="background:none;border:1px solid var(--border);border-radius:6px;
                                                       padding:5px 14px;font-size:13px;cursor:pointer;color:var(--text-muted);">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>

                        {{-- Comment like button --}}
                        <form method="POST" action="{{ route('comment-likes.toggle', $comment) }}" style="margin:0;flex-shrink:0;">
                            @csrf
                            <button type="submit"
                                    style="background:none;border:none;cursor:pointer;padding:2px;display:flex;align-items:center;gap:3px;color:{{ $liked ? '#ed4956' : 'var(--text-muted)' }};">
                                <svg viewBox="0 0 24 24"
                                     fill="{{ $liked ? '#ed4956' : 'none' }}"
                                     stroke="{{ $liked ? '#ed4956' : 'currentColor' }}"
                                     stroke-width="2"
                                     style="width:14px;height:14px;transition:transform 0.15s;"
                                     onmouseover="this.style.transform='scale(1.2)'"
                                     onmouseout="this.style.transform='scale(1)'">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div style="border-top:1px solid var(--border);padding:12px 16px;">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
                    <form method="POST" action="{{ route('likes.toggle', $post) }}">
                        @csrf
                        <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;">
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
                </div>
                @if($post->likes->count() > 0)
                    <div style="font-size:14px;font-weight:600;margin-bottom:8px;">
                        {{ $post->likes->count() }} {{ $post->likes->count() === 1 ? 'me gusta' : 'me gustas' }}
                    </div>
                @endif
                <form method="POST" action="{{ route('comments.store', $post) }}"
                      style="display:flex;gap:8px;border-top:1px solid var(--border);padding-top:10px;">
                    @csrf
                    <input type="text" name="body" placeholder="Añade un comentario..."
                           style="flex:1;border:none;outline:none;font-size:14px;background:transparent;font-family:inherit;">
                    <button type="submit" style="background:none;border:none;color:var(--primary);font-weight:600;font-size:14px;cursor:pointer;">Publicar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function startEdit(id, body) {
    document.getElementById('comment-view-' + id).style.display = 'none';
    const editDiv = document.getElementById('comment-edit-' + id);
    const input   = document.getElementById('comment-input-' + id);
    input.value   = body;
    editDiv.style.display = 'block';
    input.focus();
    // Auto-resize
    input.style.height = 'auto';
    input.style.height = input.scrollHeight + 'px';
}

function cancelEdit(id) {
    document.getElementById('comment-edit-' + id).style.display = 'none';
    document.getElementById('comment-view-' + id).style.display = 'block';
}
</script>
@endpush
