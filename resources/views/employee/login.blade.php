<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login — Sartel-E</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <style>
        :root {
            --navy:   #0a0f1e;
            --card:   #111827;
            --border: rgba(255,255,255,.08);
            --sky:    #38bdf8;
            --violet: #818cf8;
            --red:    #f87171;
            --text:   #e2e8f0;
            --muted:  #64748b;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--navy);
            min-height: 100vh;
            display: grid;
            place-items: center;
            position: relative;
        }

        .bg-orbs {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .25;
            animation: drift 12s ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 500px; height: 500px;
            background: var(--sky);
            top: -150px; left: -150px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px; height: 400px;
            background: var(--violet);
            bottom: -100px; right: -100px;
            animation-delay: -6s;
        }

        .orb-3 {
            width: 250px; height: 250px;
            background: #34d399;
            top: 50%; left: 60%;
            animation-delay: -3s;
            opacity: .12;
        }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(40px, 30px) scale(1.08); }
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        .login-wrap {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: slideUp .5s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes slideUp {
            from { opacity:0; transform: translateY(28px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .login-card {
            background: rgba(17, 24, 39, .85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px 36px 36px;
            box-shadow:
                0 0 0 1px rgba(56,189,248,.06),
                0 32px 64px rgba(0,0,0,.5);
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.4px;
            margin-top: 12px;
        }

        .brand p {
            font-size: .82rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .alert-error {
            background: rgba(248,113,113,.1);
            border: 1px solid rgba(248,113,113,.25);
            color: var(--red);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .83rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            opacity: .5;
        }

        .field input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            background: rgba(255,255,255,.04);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: .92rem;
            font-family: 'Sora', sans-serif;
            outline: none;
            transition: border .2s, background .2s, box-shadow .2s;
            /* Prevent zoom on focus in iOS */
            font-size: 16px;
        }

        .field input::placeholder { color: #374151; }

        .field input:focus {
            border-color: var(--sky);
            background: rgba(56,189,248,.04);
            box-shadow: 0 0 0 3px rgba(56,189,248,.1);
        }

        .field input.error {
            border-color: var(--red);
            background: rgba(248,113,113,.04);
            box-shadow: 0 0 0 3px rgba(248,113,113,.08);
        }

        label.error {
            display: block;
            color: var(--red);
            font-size: .75rem;
            font-weight: 500;
            margin-top: 6px;
            padding-left: 4px;
            text-transform: none;
            letter-spacing: 0;
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: .9rem;
            padding: 0;
            min-width: 36px;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            transition: color .2s;
        }

        .toggle-pw:hover { color: var(--sky); }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, var(--sky), var(--violet));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 4px 20px rgba(56,189,248,.25);
            letter-spacing: .02em;
            /* Prevent double-tap zoom on iOS */
            touch-action: manipulation;
        }

        .btn-login:hover {
            opacity: .92;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(56,189,248,.35);
        }

        .btn-login:active { transform: translateY(0); opacity: .85; }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: .75rem;
            color: var(--muted);
        }

        .login-footer span {
            font-family: 'JetBrains Mono', monospace;
            font-size: .7rem;
            opacity: .5;
        }

        @media (max-width: 600px) {
            .login-wrap {
                padding: 16px;
                align-self: flex-start;
                margin: auto;
                padding-top: 32px;
                padding-bottom: 32px;
            }

            .login-card {
                padding: 28px 20px 24px;
                border-radius: 18px;
            }

            .brand h1 {
                font-size: 1.2rem;
            }

            .brand p {
                font-size: .78rem;
            }

            .orb-1 { width: 280px; height: 280px; }
            .orb-2 { width: 220px; height: 220px; }
            .orb-3 { width: 140px; height: 140px; }
        }

        @media (max-width: 380px) {
            .login-card {
                padding: 22px 16px 20px;
                border-radius: 14px;
            }

            .brand h1 { font-size: 1.1rem; }

            .field input {
                padding: 12px 12px 12px 38px;
            }

            .btn-login {
                padding: 13px;
                font-size: .92rem;
            }
        }
    </style>
</head>
<body>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>
<div class="grid-overlay"></div>

<div class="login-wrap">
    <div class="login-card">

        <div class="brand">
            <img src="{{ asset('uploads/intas-logo.png') }}" style="height:80px; width:auto;">
            <h1>Sartel-E</h1>
        </div>

        @if(session('error'))
            <div class="alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <form method="POST" id="loginForm" action="{{ route('employee.login') }}">
            @csrf

            <div class="field">
                <label>Employee Code</label>
                <div class="input-wrap">
                    <span class="input-icon">🪪</span>
                    <input type="text"
                           name="employee_code"
                           placeholder="Enter your code"
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="none"
                           spellcheck="false">
                </div>
            </div>

            <div class="field">
                <label>Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔑</span>
                    <input type="password"
                           name="password"
                           id="passwordField"
                           placeholder="Enter your password"
                           autocomplete="current-password">
                    <button type="button" class="toggle-pw" id="togglePw" title="Show/Hide password">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In →</button>
        </form>
    </div>
</div>

<script>
    $('#togglePw').on('click', function () {
        var field = $('#passwordField');
        var isText = field.attr('type') === 'text';
        field.attr('type', isText ? 'password' : 'text');
        $(this).text(isText ? '👁' : '🙈');
    });

    $("#loginForm").validate({
        errorClass: 'error',
        rules: {
            employee_code: { required: true },
            password:      { required: true, minlength: 4 }
        },
        messages: {
            employee_code: { required: "Please enter your employee code" },
            password: {
                required: "Please enter your password",
                minlength: "Minimum 4 characters required"
            }
        },
        highlight:   function(el) { $(el).addClass('error'); },
        unhighlight: function(el) { $(el).removeClass('error'); }
    });
</script>

</body>
</html>
