@extends('layouts.app')

@section('content')
<div style="max-width:900px;margin:0 auto;padding:32px 16px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:32px;
                padding-bottom:24px;border-bottom:1px solid var(--border);">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#405de6,#5851db,#833ab4,#c13584,#e1306c,#fd1d1d);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="color:#fff;font-size:28px;font-weight:700;">#</span>
        </div>
        <div>
            <h1 style="font-size:22px;font-weight:700;margin:0 0 4px;">#{{ $tag }}</h1>
            <p style="font-size:14px;color:var(--text-muted);margin:0;">
                {{ $posts->total() }} {{ $posts->total() === 1 ? 'publicación' : 'publicaciones' }}
            </p>
        </div>
    </div>

    {{-- Posts grid --}}
    @if($posts->isEmpty())
        <div style="text-align:center;padding:60px 0;color:var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <p style="font-size:14px;margin:0;">Aún no hay posts con #{{ $tag }}</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:3px;">
            @foreach($posts as $post)
                <a href="{{ route('posts.show', $post) }}"
                   style="display:block;aspect-ratio:1;overflow:hidden;position:relative;background:#efefef;">
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
        <div style="margin-top:24px;">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
