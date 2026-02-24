<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NativeInsta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        :root {
            --bg: #fafafa;
            --surface: #ffffff;
            --border: #dbdbdb;
            --text: #262626;
            --text-muted: #8e8e8e;
            --primary: #0095f6;
            --primary-hover: #1877f2;
            --danger: #ed4956;
            --sidebar-w: 244px;
        }
        body { background: var(--bg); color: var(--text); margin: 0; }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-w); background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            padding: 12px 0; z-index: 100;
        }
        .sidebar-logo {
            padding: 16px 24px 24px;
            font-size: 22px; font-weight: 700; letter-spacing: -0.5px;
            color: var(--text); text-decoration: none; display: block;
        }
        .sidebar-logo span { color: #e1306c; }
        .nav-item {
            display: flex; align-items: center; gap: 16px;
            padding: 12px 24px; border-radius: 8px; margin: 2px 8px;
            color: var(--text); text-decoration: none; font-size: 15px;
            font-weight: 400; transition: background 0.15s;
        }
        .nav-item:hover { background: #f0f0f0; }
        .nav-item.active { font-weight: 600; }
        .nav-item svg { width: 24px; height: 24px; flex-shrink: 0; }
        .nav-spacer { flex: 1; }
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; }
        .btn-primary {
            background: var(--primary); color: #fff; border: none;
            padding: 8px 16px; border-radius: 8px; font-size: 14px;
            font-weight: 600; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-outline {
            background: transparent; color: var(--text);
            border: 1px solid var(--border); padding: 7px 16px;
            border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px; transition: background 0.15s;
        }
        .btn-outline:hover { background: #f0f0f0; }
        .btn-danger {
            background: var(--danger); color: #fff; border: none;
            padding: 7px 16px; border-radius: 8px; font-size: 14px;
            font-weight: 600; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .alert-success {
            background: #d4edda; color: #155724; border: 1px solid #c3e6cb;
            padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('feed') }}" class="sidebar-logo">
            Native<span>Insta</span>
        </a>
        <nav>
            <a href="{{ route('feed') }}" class="nav-item {{ request()->routeIs('feed') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('feed') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Inicio
            </a>
            <a href="{{ route('search') }}" class="nav-item {{ request()->routeIs('search') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('search') ? '2.5' : '2' }}">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Buscar
            </a>
            <a href="{{ route('explore') }}" class="nav-item {{ request()->routeIs('explore') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('explore') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Explorar
            </a>
            @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
            <a href="{{ route('notifications') }}" class="nav-item {{ request()->routeIs('notifications') ? 'active' : '' }}">
                <div style="position:relative;width:24px;height:24px;flex-shrink:0;">
                    <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('notifications') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if($unread > 0)
                        <span style="position:absolute;top:-4px;right:-4px;background:#ed4956;color:#fff;
                                     font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;
                                     display:flex;align-items:center;justify-content:center;padding:0 3px;
                                     border:2px solid #fff;">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </div>
                Notificaciones
            </a>
            <a href="{{ route('posts.create') }}" class="nav-item {{ request()->routeIs('posts.create') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Crear
            </a>
            <a href="{{ route('profile.show', auth()->user()) }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <img src="{{ auth()->user()->avatar_url }}" alt="avatar"
                     style="width:24px;height:24px;border-radius:50%;object-fit:cover;border:2px solid {{ request()->routeIs('profile.*') ? '#262626' : 'transparent' }}">
                Perfil
            </a>
        </nav>
        <div class="nav-spacer"></div>
        <form method="POST" action="{{ route('logout') }}" style="margin: 8px;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Salir
            </button>
        </form>
    </aside>
    <main class="main-content">
        @if(session('success'))
            <div style="padding: 16px 32px 0;">
                <div class="alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
