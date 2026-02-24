@extends('layouts.app')

@section('content')
<div style="max-width:680px;margin:0 auto;padding:32px 16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h2 style="font-size:20px;font-weight:700;margin:0;">Mensajes</h2>
        <a href="{{ route('search') }}" class="btn-primary" style="font-size:13px;padding:7px 14px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nuevo mensaje
        </a>
    </div>

    @if($conversations->isEmpty())
        <div style="text-align:center;padding:64px 0;color:#8e8e8e;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 style="width:56px;height:56px;margin:0 auto 12px;display:block;opacity:0.4">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <p style="font-size:14px;margin:0 0 16px;">Aún no tienes mensajes</p>
            <a href="{{ route('search') }}" class="btn-primary">Buscar usuarios</a>
        </div>
    @else
        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;">
            @foreach($conversations as $convo)
                @php
                    $other = $convo->otherUser(auth()->user());
                    $last  = $convo->messages()->latest()->first();
                    $unread = $convo->unreadCountFor(auth()->user());
                @endphp
                <a href="{{ route('messages.show', $other) }}"
                   style="display:flex;align-items:center;gap:14px;padding:14px 16px;
                          text-decoration:none;color:var(--text);transition:background 0.15s;
                          border-bottom:1px solid var(--border);"
                   onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='transparent'">

                    {{-- Avatar --}}
                    <div style="position:relative;flex-shrink:0;">
                        <img src="{{ $other->avatar_url }}" alt="{{ $other->name }}"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;">
                        @if($unread > 0)
                            <span style="position:absolute;top:0;right:0;background:#ed4956;color:#fff;
                                         font-size:10px;font-weight:700;min-width:16px;height:16px;
                                         border-radius:8px;display:flex;align-items:center;
                                         justify-content:center;padding:0 3px;border:2px solid #fff;">
                                {{ $unread }}
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:{{ $unread > 0 ? '700' : '500' }};font-size:14px;">
                            {{ $other->username ?? $other->name }}
                        </div>
                        @if($last)
                            <div style="font-size:13px;color:#8e8e8e;white-space:nowrap;overflow:hidden;
                                        text-overflow:ellipsis;font-weight:{{ $unread > 0 ? '600' : '400' }};">
                                {{ $last->sender_id === auth()->id() ? 'Tú: ' : '' }}{{ $last->body }}
                            </div>
                        @endif
                    </div>

                    {{-- Time --}}
                    @if($last)
                        <div style="font-size:11px;color:#8e8e8e;flex-shrink:0;">
                            {{ $last->created_at->diffForHumans(null, true) }}
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
