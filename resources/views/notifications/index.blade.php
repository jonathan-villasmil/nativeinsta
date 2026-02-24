@extends('layouts.app')

@section('content')
<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 24px;">Notificaciones</h2>

    @if($notifications->isEmpty())
        <div style="text-align:center;padding:60px 0;color:#8e8e8e;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <p style="font-size:14px;margin:0;">No tienes notificaciones aún</p>
        </div>
    @else
        @foreach($notifications as $notification)
            @php
                $isRead = $notification->read_at !== null;
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:14px 12px;
                        border-radius:8px;margin-bottom:4px;
                        background:{{ $isRead ? 'transparent' : '#eff6ff' }};
                        transition:background 0.2s;">

                {{-- Actor avatar --}}
                <a href="{{ route('profile.show', $notification->actor) }}" style="flex-shrink:0;">
                    <img src="{{ $notification->actor->avatar_url }}" alt="{{ $notification->actor->name }}"
                         style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:1px solid #dbdbdb;">
                </a>

                {{-- Text --}}
                <div style="flex:1;font-size:14px;line-height:1.4;">
                    <a href="{{ route('profile.show', $notification->actor) }}"
                       style="font-weight:600;color:#262626;text-decoration:none;">
                        {{ $notification->actor->username ?? $notification->actor->name }}
                    </a>

                    @if($notification->type === 'like')
                        le dio me gusta a tu foto.
                    @elseif($notification->type === 'comment')
                        comentó tu foto.
                    @elseif($notification->type === 'follow')
                        empezó a seguirte.
                    @endif

                    <div style="font-size:12px;color:#8e8e8e;margin-top:2px;">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>

                {{-- Thumbnail for like/comment --}}
                @if(in_array($notification->type, ['like', 'comment']) && $notification->notifiable)
                    <a href="{{ route('posts.show', $notification->notifiable) }}" style="flex-shrink:0;">
                        <img src="{{ $notification->notifiable->image_url }}" alt="post"
                             style="width:44px;height:44px;object-fit:cover;border-radius:4px;border:1px solid #dbdbdb;">
                    </a>
                @endif
            </div>
        @endforeach

        <div style="margin-top:16px;">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
