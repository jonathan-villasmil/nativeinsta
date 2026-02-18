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
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:3px;">
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
        <div style="margin-top:24px;">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
