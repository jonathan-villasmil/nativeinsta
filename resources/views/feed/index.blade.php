@extends('layouts.app')

@section('content')
<div style="max-width: 630px; margin: 0 auto; padding: 32px 16px;">

    @if($posts->isEmpty())
        <div style="text-align:center; padding: 80px 0; color: var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:64px;height:64px;margin:0 auto 16px;display:block;opacity:0.4">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <h3 style="font-size:18px;font-weight:600;margin:0 0 8px;">Aún no hay posts</h3>
            <p style="font-size:14px;margin:0 0 20px;">Sigue a otros usuarios o sube tu primer post.</p>
            <a href="{{ route('explore') }}" class="btn-primary">Explorar</a>
        </div>
    @else
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post])
        @endforeach

        <div style="margin-top: 24px;">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
