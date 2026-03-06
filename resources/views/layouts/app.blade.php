<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediAdmin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
        }

        /* ─── OVERLAY (mobile) ─── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity .25s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position: fixed;
            left: 0; top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 24px rgba(0,0,0,.18);
            transition: transform .25s ease;
        }

        .sidebar-brand {
            padding: 28px 24px 24px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand h2 {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -.3px;
        }

        .sidebar-brand span {
            display: inline-block;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Close button inside sidebar (mobile only) */
        .sidebar-close {
            display: none;
            background: rgba(255,255,255,.08);
            border: none;
            color: #94a3b8;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .2s, color .2s;
        }

        .sidebar-close:hover {
            background: rgba(255,255,255,.15);
            color: #fff;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .1em;
            color: #475569;
            text-transform: uppercase;
            padding: 8px 12px 6px;
            margin-top: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            transition: all .2s ease;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(56,189,248,.18), rgba(129,140,248,.12));
            color: #fff;
            border: 1px solid rgba(56,189,248,.2);
        }

        .nav-item .icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: grid; place-items: center;
            background: rgba(255,255,255,.05);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .nav-item.active .icon {
            background: linear-gradient(135deg, #38bdf8, #818cf8);
        }

        .sidebar-footer {
            padding: 16px 12px 24px;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #f87171;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            transition: all .2s ease;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .logout-btn:hover { background: rgba(248,113,113,.1); }

        .logout-btn .icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: grid; place-items: center;
            background: rgba(248,113,113,.12);
            font-size: 1rem;
        }

        /* ─── MAIN ─── */
        .main {
            margin-left: 260px;
            min-height: 100vh;
            padding: 32px 36px;
            transition: margin-left .25s ease;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 12px;
        }

        /* Hamburger (mobile only) */
        .hamburger {
            display: none;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: border-color .2s;
        }

        .hamburger:hover { border-color: #38bdf8; }

        .hamburger svg {
            width: 18px;
            height: 18px;
            stroke: #475569;
            stroke-width: 2;
            stroke-linecap: round;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .page-title {
            min-width: 0;
        }

        .page-title h1 {
            font-size: 1.7rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .page-title p {
            color: #64748b;
            font-size: .88rem;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ─── USER DROPDOWN ─── */
        .user-menu {
            position: relative;
            flex-shrink: 0;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 14px 7px 7px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .user-trigger:hover {
            border-color: #38bdf8;
            box-shadow: 0 2px 12px rgba(56,189,248,.15);
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            border-radius: 50%;
            display: grid; place-items: center;
            color: white;
            font-weight: 700;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .user-info {
            text-align: left;
        }

        .user-info .user-name {
            font-size: .85rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.2;
            max-width: 130px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info .user-role {
            font-size: .72rem;
            color: #94a3b8;
        }

        .chevron {
            color: #94a3b8;
            font-size: .75rem;
            transition: transform .2s;
        }

        .user-menu.open .chevron { transform: rotate(180deg); }

        .user-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 200px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
            overflow: hidden;
            z-index: 200;
            animation: dropIn .15s ease;
        }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .user-menu.open .user-dropdown { display: block; }

        .dropdown-header {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .dropdown-header p {
            font-size: .82rem;
            font-weight: 700;
            color: #0f172a;
        }

        .dropdown-header span {
            font-size: .75rem;
            color: #94a3b8;
        }

        .dropdown-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: #f43f5e;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            font-family: inherit;
            transition: background .15s;
        }

        .dropdown-logout:hover { background: #fff1f2; }

        /* ─── CARD ─── */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        }

        /* ─── TABLET (≤1024px) ─── */
        @media (max-width: 1024px) {
            .sidebar {
                width: 230px;
            }
            .main {
                margin-left: 230px;
                padding: 24px 24px;
            }
            .page-title h1 {
                font-size: 1.4rem;
            }
            .user-info {
                display: none;
            }
            .chevron {
                display: none;
            }
            .user-trigger {
                padding: 7px;
            }
        }

        /* ─── MOBILE (≤768px) ─── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 270px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-close {
                display: flex;
            }

            .main {
                margin-left: 0;
                padding: 20px 16px;
            }

            .hamburger {
                display: flex;
            }

            .page-title h1 {
                font-size: 1.2rem;
            }

            .page-title p {
                display: none;
            }

            .topbar {
                margin-bottom: 20px;
            }

            .user-info {
                display: none;
            }

            .chevron {
                display: none;
            }

            .user-trigger {
                padding: 7px;
            }

            .card {
                padding: 16px;
                border-radius: 12px;
            }
        }

        /* ─── SMALL MOBILE (≤480px) ─── */
        @media (max-width: 480px) {
            .main {
                padding: 16px 12px;
            }

            .page-title h1 {
                font-size: 1.1rem;
            }
        }

        @stack('styles')
    </style>
    @stack('styles')
</head>
<body>

{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <img src="{{ asset('uploads/intas-logo.png') }}" style="height:48px; width:auto;">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close menu">✕</button>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-label">Main Menu</span>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           onclick="closeSidebarOnMobile()">
            <div class="icon">📊</div>
            Dashboard
        </a>

        <a href="{{ route('doctors.index') }}"
           class="nav-item {{ request()->routeIs('doctors.*') ? 'active' : '' }}"
           onclick="closeSidebarOnMobile()">
            <div class="icon">👨‍⚕️</div>
            Manage Doctors
        </a>

    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="icon">🚪</div>
                Logout
            </button>
        </form>
    </div>

</aside>

<main class="main">

    <div class="topbar">
        <div class="topbar-left">
            {{-- Hamburger --}}
            <button class="hamburger" onclick="openSidebar()" aria-label="Open menu">
                <svg viewBox="0 0 24 24" fill="none">
                    <line x1="3" y1="6"  x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <div class="page-title">
                <h1>@yield('page_title', 'Dashboard')</h1>
                <p>@yield('page_subtitle', 'Welcome back!')</p>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="user-menu" id="userMenu">
            <div class="user-trigger" onclick="toggleMenu()">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->designation_name }}</div>
                </div>
                <span class="chevron">▼</span>
            </div>

            <div class="user-dropdown">
                <div class="dropdown-header">
                    <p>{{ Auth::user()->name }}</p>
                    <span>{{ Auth::user()->email }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-logout">
                        🚪 Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    @yield('content')

</main>

<script>
    // ── User dropdown ──
    function toggleMenu() {
        document.getElementById('userMenu').classList.toggle('open');
    }

    document.addEventListener('click', function (e) {
        var menu = document.getElementById('userMenu');
        if (!menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });

    // ── Mobile sidebar ──
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close sidebar on nav click (mobile only)
    function closeSidebarOnMobile() {
        if (window.innerWidth <= 768) {
            closeSidebar();
        }
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });

    // Keyboard: Escape to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            document.getElementById('userMenu').classList.remove('open');
        }
    });
</script>

</body>
</html>
