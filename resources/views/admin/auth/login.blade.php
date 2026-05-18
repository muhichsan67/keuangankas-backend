<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.login_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root, [data-theme="dark"] {
            --bg: #0b0d14; --card-bg: rgba(255,255,255,0.04); --card-border: rgba(255,255,255,0.08);
            --text: #e2e8f0; --text-sec: #94a3b8; --text-muted: #475569;
            --input-bg: rgba(255,255,255,0.06); --input-border: rgba(255,255,255,0.08);
            --select-bg: #1a1d27;
        }
        [data-theme="light"] {
            --bg: #f1f5f9; --card-bg: rgba(255,255,255,0.9); --card-border: rgba(0,0,0,0.08);
            --text: #0f172a; --text-sec: #475569; --text-muted: #94a3b8;
            --input-bg: rgba(0,0,0,0.03); --input-border: rgba(0,0,0,0.1);
            --select-bg: #fff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg); color: var(--text); min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; transition: background .3s, color .3s;
        }
        body::before {
            content: ''; position: fixed; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(124,58,237,0.12) 0%, transparent 70%);
            top: -200px; left: -200px; border-radius: 50%; pointer-events: none;
        }
        body::after {
            content: ''; position: fixed; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(79,70,229,0.1) 0%, transparent 70%);
            bottom: -150px; right: -150px; border-radius: 50%; pointer-events: none;
        }

        .login-container { width: 100%; max-width: 420px; padding: 20px; position: relative; z-index: 1; }
        .login-header { text-align: center; margin-bottom: 36px; }
        .logo-circle {
            width: 64px; height: 64px; border-radius: 18px;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin: 0 auto 16px;
            box-shadow: 0 0 40px rgba(124,58,237,0.35);
        }
        .login-title { font-size: 24px; font-weight: 800; }
        .login-subtitle { font-size: 13.5px; color: var(--text-sec); margin-top: 6px; }

        .login-card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 20px; padding: 32px;
            backdrop-filter: blur(20px); box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            transition: background .3s, border-color .3s;
        }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-sec); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--text-muted); }
        .form-control {
            width: 100%; padding: 11px 14px 11px 40px;
            border-radius: 10px; font-size: 14px; font-family: inherit;
            background: var(--input-bg); border: 1px solid var(--input-border);
            color: var(--text); outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }
        .form-control::placeholder { color: var(--text-muted); }

        .checkbox-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-sec); margin-bottom: 22px; }
        input[type="checkbox"] { accent-color: #7c3aed; width: 15px; height: 15px; }

        .btn-login {
            width: 100%; padding: 13px; border-radius: 10px; border: none; cursor: pointer;
            font-family: inherit; font-size: 15px; font-weight: 600;
            background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff;
            transition: all .2s; box-shadow: 0 4px 20px rgba(124,58,237,0.35);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(124,58,237,0.45); }

        .alert-danger {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #ef4444; padding: 11px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px;
        }

        .footer-row { display: flex; align-items: center; justify-content: space-between; margin-top: 24px; }
        .footer-note { font-size: 12px; color: var(--text-muted); }
        .toggle-row { display: flex; gap: 6px; }
        .toggle-btn {
            background: var(--card-bg); border: 1px solid var(--card-border);
            color: var(--text-sec); font-size: 12px; padding: 4px 10px; border-radius: 6px;
            cursor: pointer; transition: all .2s;
        }
        .toggle-btn:hover { color: var(--text); }
    </style>
</head>
<body>
<script>
(function(){
    const t = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();
</script>

<div class="login-container">
    <div class="login-header">
        <div class="logo-circle">💰</div>
        <div class="login-title">{{ __('admin.login_title') }}</div>
        <div class="login-subtitle">{{ __('admin.login_subtitle') }}</div>
    </div>

    <div class="login-card">
        @if($errors->any())
            <div class="alert-danger">⚠️ {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('admin.login_email_label') }}</label>
                <div class="input-wrap">
                    <span class="input-icon">📧</span>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@keluargakas.app"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.login_pass_label') }}</label>
                <div class="input-wrap">
                    <span class="input-icon">🔐</span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>
            <div class="checkbox-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">{{ __('admin.remember_me') }}</label>
            </div>
            <button type="submit" class="btn-login">{{ __('admin.login_button') }}</button>
        </form>
    </div>

    <div class="footer-row">
        <div class="footer-note">KeluargaKas © {{ date('Y') }} · {{ __('admin.login_footer') }}</div>
        <div class="toggle-row">
            <button class="toggle-btn" onclick="toggleTheme()" id="loginThemeBtn">🌙</button>
            <form method="POST" action="{{ route('locale.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" style="display:inline;">
                @csrf
                <button type="submit" class="toggle-btn">🌐 {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    const btn = document.getElementById('loginThemeBtn');
    if (btn) btn.textContent = next === 'dark' ? '🌙' : '☀️';
}
(function(){
    const btn = document.getElementById('loginThemeBtn');
    const t = document.documentElement.getAttribute('data-theme') || 'dark';
    if (btn) btn.textContent = t === 'dark' ? '🌙' : '☀️';
})();
</script>
</body>
</html>
