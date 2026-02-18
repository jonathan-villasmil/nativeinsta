<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — NativeInsta</title>
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
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }
        .logo span { color: #e1306c; }
        .tagline {
            font-size: 17px;
            font-weight: 600;
            color: #8e8e8e;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.4;
        }
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
        .btn-submit:disabled { opacity: 0.5; cursor: default; }
        .error-msg { color: #ed4956; font-size: 12px; margin: 3px 0 4px; }
        .divider {
            display: flex; align-items: center; gap: 16px;
            margin: 18px 0; color: #8e8e8e; font-size: 13px; font-weight: 600;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #dbdbdb;
        }
        .login-link {
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            padding: 12px 40px 24px;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
            background: #fff;
            max-width: 350px;
            width: 100%;
        }
        .login-link a { color: #0095f6; font-weight: 600; text-decoration: none; }
        .hint { font-size: 11px; color: #8e8e8e; text-align: center; margin: 16px 0 8px; line-height: 1.5; }
    </style>
</head>
<body>
    <div style="display:flex;flex-direction:column;align-items:center;gap:0;width:100%;max-width:350px;">
        <div class="card">
            <div class="logo">Native<span>Insta</span></div>
            <p class="tagline">Regístrate para ver fotos de tus amigos.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <input class="form-input" type="text" name="name"
                           value="{{ old('name') }}" placeholder="Nombre completo" required autofocus>
                    @error('name')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input class="form-input" type="text" name="username"
                           value="{{ old('username') }}" placeholder="Nombre de usuario" required>
                    @error('username')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input class="form-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="Correo electrónico" required>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input class="form-input" type="password" name="password"
                           placeholder="Contraseña" required autocomplete="new-password">
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input class="form-input" type="password" name="password_confirmation"
                           placeholder="Confirmar contraseña" required autocomplete="new-password">
                </div>

                <p class="hint">
                    Al registrarte, aceptas nuestros Términos y Política de privacidad.
                </p>

                <button type="submit" class="btn-submit">Registrarse</button>
            </form>
        </div>

        <div class="login-link">
            ¿Tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </div>
    </div>
</body>
</html>
