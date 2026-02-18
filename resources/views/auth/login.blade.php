<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — NativeInsta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            padding: 40px 40px 24px;
            width: 100%;
            max-width: 350px;
        }
        .logo {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 32px;
            letter-spacing: -0.5px;
        }
        .logo span { color: #e1306c; }
        .form-group { margin-bottom: 6px; }
        .form-input {
            width: 100%;
            background: #fafafa;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            padding: 9px 8px;
            font-size: 12px;
            color: #262626;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-input:focus { border-color: #a8a8a8; }
        .btn-submit {
            width: 100%;
            background: #0095f6;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.15s;
        }
        .btn-submit:hover { background: #1877f2; }
        .error-msg { color: #ed4956; font-size: 12px; margin: 3px 0 4px; }
        .forgot {
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
        }
        .forgot a { color: #00376b; text-decoration: none; font-weight: 600; }
        .register-link {
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            padding: 16px 40px;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
            background: #fff;
            max-width: 350px;
            width: 100%;
        }
        .register-link a { color: #0095f6; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <div style="display:flex;flex-direction:column;align-items:center;gap:0;width:100%;max-width:350px;">
        <div class="card">
            <div class="logo">Native<span>Insta</span></div>

            @if(session('status'))
                <p style="color:#155724;background:#d4edda;border:1px solid #c3e6cb;padding:10px;border-radius:4px;font-size:13px;margin-bottom:12px;">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <input class="form-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="Correo electrónico" required autofocus>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input class="form-input" type="password" name="password"
                           placeholder="Contraseña" required autocomplete="current-password">
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Iniciar sesión</button>

                @if(Route::has('password.request'))
                    <div class="forgot">
                        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                    </div>
                @endif
            </form>
        </div>

        <div class="register-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
        </div>
    </div>
</body>
</html>
