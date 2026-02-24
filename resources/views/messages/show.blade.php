@extends('layouts.app')

@section('content')
<div style="display:flex;flex-direction:column;height:calc(100vh);max-width:720px;
            margin:0 auto;background:#fff;border-left:1px solid var(--border);border-right:1px solid var(--border);">

    {{-- ─── Header ──────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;
                border-bottom:1px solid var(--border);background:#fff;flex-shrink:0;">
        <a href="{{ route('messages.index') }}"
           style="color:var(--text);text-decoration:none;display:flex;align-items:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        <a href="{{ route('profile.show', $user) }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:var(--text);">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
            <div>
                <div style="font-weight:700;font-size:15px;">{{ $user->username ?? $user->name }}</div>
                <div style="font-size:12px;color:#8e8e8e;">{{ $user->name }}</div>
            </div>
        </a>
    </div>

    {{-- ─── Messages ─────────────────────────────────────────── --}}
    <div id="messages-box" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:8px;">

        @if($messages->isEmpty())
            <div style="text-align:center;margin:auto;color:#8e8e8e;">
                <img src="{{ $user->avatar_url }}" alt=""
                     style="width:72px;height:72px;border-radius:50%;object-fit:cover;margin-bottom:12px;">
                <div style="font-weight:600;font-size:15px;margin-bottom:4px;">{{ $user->username ?? $user->name }}</div>
                <div style="font-size:13px;">Empieza la conversación 👋</div>
            </div>
        @endif

        @php $prevDate = null; @endphp
        @foreach($messages as $msg)
            @php
                $isMine = $msg->sender_id === auth()->id();
                $date   = $msg->created_at->toDateString();
            @endphp

            {{-- Date separator --}}
            @if($date !== $prevDate)
                <div style="text-align:center;font-size:11px;color:#8e8e8e;margin:8px 0;">
                    {{ $msg->created_at->isToday() ? 'Hoy' : ($msg->created_at->isYesterday() ? 'Ayer' : $msg->created_at->format('d M Y')) }}
                </div>
                @php $prevDate = $date; @endphp
            @endif

            <div style="display:flex;justify-content:{{ $isMine ? 'flex-end' : 'flex-start' }};align-items:flex-end;gap:8px;">
                @if(!$isMine)
                    <img src="{{ $msg->sender->avatar_url }}" alt=""
                         style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;margin-bottom:2px;">
                @endif
                <div style="max-width:70%;">
                    <div style="background:{{ $isMine ? '#0095f6' : '#efefef' }};
                                color:{{ $isMine ? '#fff' : 'var(--text)' }};
                                padding:10px 14px;border-radius:{{ $isMine ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }};
                                font-size:14px;line-height:1.4;word-break:break-word;">
                        {{ $msg->body }}
                    </div>
                    <div style="font-size:10px;color:#8e8e8e;margin-top:3px;text-align:{{ $isMine ? 'right' : 'left' }};">
                        {{ $msg->created_at->format('H:i') }}
                        @if($isMine)
                            {{ $msg->read_at ? '· Visto' : '' }}
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ─── Input ────────────────────────────────────────────── --}}
    <div style="padding:12px 16px;border-top:1px solid var(--border);background:#fff;flex-shrink:0;">
        <form method="POST" action="{{ route('messages.store', $user) }}"
              style="display:flex;align-items:center;gap:10px;" id="msg-form">
            @csrf
            <input type="text" name="body" id="msg-input"
                   placeholder="Escribe un mensaje…"
                   autocomplete="off"
                   style="flex:1;background:#efefef;border:none;border-radius:22px;
                          padding:10px 16px;font-size:14px;outline:none;font-family:inherit;"
                   required>
            <button type="submit"
                    style="background:none;border:none;cursor:pointer;color:#0095f6;
                           font-size:14px;font-weight:600;padding:0 4px;font-family:inherit;">
                Enviar
            </button>
        </form>
    </div>
</div>

<script>
    // Scroll to bottom on load
    const box = document.getElementById('messages-box');
    box.scrollTop = box.scrollHeight;

    // Submit on Enter (not Shift+Enter)
    document.getElementById('msg-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('msg-form').submit();
        }
    });

    // Auto-refresh every 5s to get new messages
    setTimeout(() => location.reload(), 5000);
</script>
@endsection
