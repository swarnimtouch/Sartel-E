<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sartel-E</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg:      #0a0f1e;
            --surface: #111827;
            --border:  #1e2d47;
            --accent:  #3b82f6;
            --accent2: #06b6d4;
            --text:    #e2e8f0;
            --muted:   #64748b;
            --danger:  #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* ── Background ── */
        .bg-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .bg-glow {
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,.08) 0%, transparent 70%);
            top: -200px; right: -200px;
            pointer-events: none;
        }

        .bg-glow2 {
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(6,182,212,.06) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }

        /* ── Wrap ── */
        .login-wrap {
            position: relative; z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 24px;
            /* allow scroll when keyboard opens on mobile */
            margin: auto;
        }

        /* ── Brand ── */
        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand h1 {
            font-size: 24px;
            font-weight: 700;
            margin-top: 12px;
        }

        .brand p {
            font-size: 14px;
            color: var(--muted);
            margin-top: 6px;
        }

        /* ── Card ── */
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text);
        }

        .input-wrap { position: relative; }

        .input-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); font-size: 14px;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: #0d1424;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Sora', sans-serif;
            /* 16px prevents iOS auto-zoom */
            font-size: 16px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }

        input.error { border-color: var(--danger); }

        .error-msg {
            font-size: 12px;
            color: var(--danger);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Password toggle ── */
        .toggle-pw {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: var(--muted); cursor: pointer;
            font-size: 14px;
            /* bigger tap target */
            min-width: 36px; min-height: 36px;
            display: flex; align-items: center; justify-content: flex-end;
            transition: color .2s;
            touch-action: manipulation;
        }

        .toggle-pw:hover { color: var(--accent); }

        /* ── Remember ── */
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }

        .remember input[type="checkbox"] { accent-color: var(--accent); }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 24px;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 4px 20px rgba(59,130,246,.25);
            touch-action: manipulation;
        }

        .btn-login:hover  { opacity: .9; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); opacity: .85; }

        /* ── MOBILE (≤480px) ── */
        @media (max-width: 480px) {
            body {
                align-items: flex-start;
            }

            .login-wrap {
                padding: 20px 16px;
                padding-top: 40px;
                padding-bottom: 40px;
            }

            .login-card {
                padding: 24px 18px;
                border-radius: 16px;
            }

            .brand { margin-bottom: 24px; }
            .brand h1 { font-size: 20px; }

            .btn-login { font-size: 14px; padding: 13px; }

            /* Reduce glow on small screens */
            .bg-glow  { width: 300px; height: 300px; }
            .bg-glow2 { width: 200px; height: 200px; }
        }
    </style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-glow"></div>
<div class="bg-glow2"></div>

<div class="login-wrap">

    <div class="brand">
        <img src="{{ asset('uploads/intas-logo.png') }}" style="height:40px; width:auto;" alt="Logo">
        <h1>Sartel-E</h1>
    </div>

    <div class="login-card">
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="{{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="admin@example.com"
                           autocomplete="email"
                           required
                           autofocus>
                </div>
                @error('email')
                <div class="error-msg">
                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="toggle-pw" id="togglePw" title="Show/Hide password">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                <div class="error-msg">
                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <label class="remember">
                <input type="checkbox" name="remember"> Remember me
            </label>

            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right-to-bracket"></i> Login
            </button>

        </form>
    </div>

</div>

<script>
    var pwField  = document.getElementById('password');
    var toggleBtn = document.getElementById('togglePw');
    var toggleIcon = document.getElementById('toggleIcon');

    toggleBtn.addEventListener('click', function () {
        var isPassword = pwField.type === 'password';
        pwField.type = isPassword ? 'text' : 'password';
        toggleIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
</script>

</body>
</html>
