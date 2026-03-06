<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0f1e;
            --surface: #111827;
            --surface2: #1a2236;
            --border: #1e2d47;
            --accent: #3b82f6;
            --accent2: #06b6d4;
            --text: #e2e8f0;
            --muted: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-w: 260px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ─── OVERLAY ─── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 99;
            opacity: 0;
            transition: opacity .25s ease;
        }
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .25s ease;
        }
        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
        }
        .sidebar-brand .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .sidebar-brand .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
        }
        .sidebar-brand .logo-text {
            font-size: 16px; font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .sidebar-brand .logo-sub {
            font-size: 11px; color: var(--muted);
            font-weight: 400;
        }

        /* Close btn inside sidebar (mobile) */
        .sidebar-close {
            display: none;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--muted);
            width: 32px; height: 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: color .2s, border-color .2s;
        }
        .sidebar-close:hover { color: var(--text); border-color: var(--accent); }

        .sidebar-nav { padding: 20px 12px; flex: 1; overflow-y: auto; }
        .nav-label {
            font-size: 10px; font-weight: 600;
            color: var(--muted); letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0 12px; margin-bottom: 8px; margin-top: 16px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: var(--surface2); color: var(--text); }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(6,182,212,.1));
            color: var(--accent);
            border: 1px solid rgba(59,130,246,.2);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--surface2);
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .user-info .name { font-size: 13px; font-weight: 600; }
        .user-info .role { font-size: 11px; color: var(--muted); }
        .logout-btn {
            margin-left: auto;
            background: none; border: none;
            color: var(--muted); cursor: pointer;
            font-size: 14px;
            transition: color .2s;
        }
        .logout-btn:hover { color: var(--danger); }

        /* ─── MAIN ─── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: margin-left .25s ease;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            gap: 12px;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .topbar-title {
            font-size: 18px; font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-right { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
        .topbar-badge {
            padding: 4px 12px; border-radius: 20px;
            background: rgba(16,185,129,.1);
            color: var(--success);
            font-size: 12px; font-weight: 500;
            border: 1px solid rgba(16,185,129,.2);
            white-space: nowrap;
        }

        /* Hamburger */
        .hamburger {
            display: none;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--muted);
            width: 38px; height: 38px;
            border-radius: 8px;
            align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 15px;
            flex-shrink: 0;
            transition: color .2s, border-color .2s;
        }
        .hamburger:hover { color: var(--text); border-color: var(--accent); }

        .page-content { padding: 32px; flex: 1; }

        /* ─── STATS ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin-bottom: 32px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            position: relative; overflow: hidden;
            transition: transform .2s, border-color .2s;
        }
        .stat-card:hover { transform: translateY(-3px); border-color: var(--accent); }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
        }
        .stat-card.blue::before   { background: linear-gradient(90deg, var(--accent), var(--accent2)); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--success), #34d399); }
        .stat-card.yellow::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card.red::before    { background: linear-gradient(90deg, var(--danger), #f87171); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin-bottom: 16px;
        }
        .stat-icon.blue   { background: rgba(59,130,246,.15); color: var(--accent); }
        .stat-icon.green  { background: rgba(16,185,129,.15); color: var(--success); }
        .stat-icon.yellow { background: rgba(245,158,11,.15); color: var(--warning); }
        .stat-icon.red    { background: rgba(239,68,68,.15); color: var(--danger); }
        .stat-value { font-size: 28px; font-weight: 700; font-family: 'JetBrains Mono', monospace; }
        .stat-label { font-size: 13px; color: var(--muted); margin-top: 4px; }

        /* ─── CARD / TABLE ─── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
        }
        .card-title { font-size: 16px; font-weight: 600; }
        .card-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

        .filters-bar {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
        }
        .search-box {
            position: relative; flex: 1; min-width: 180px;
        }
        .search-box i {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: var(--muted); font-size: 14px;
        }
        .search-box input {
            width: 100%; padding: 9px 12px 9px 36px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-family: 'Sora', sans-serif;
            font-size: 13px;
            outline: none; transition: border-color .2s;
        }
        .search-box input:focus { border-color: var(--accent); }
        .filter-select {
            padding: 9px 12px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-family: 'Sora', sans-serif;
            font-size: 13px; outline: none; cursor: pointer;
            transition: border-color .2s;
        }
        .filter-select:focus { border-color: var(--accent); }
        .btn {
            padding: 9px 18px; border-radius: 8px;
            border: none; cursor: pointer;
            font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 500;
            transition: all .2s; display: inline-flex; align-items: center; gap: 6px;
            white-space: nowrap;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #2563eb; }
        .btn-ghost {
            background: var(--surface2); color: var(--muted);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { color: var(--text); border-color: var(--accent); }

        /* ─── TABLE ─── */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 520px; }
        thead th {
            padding: 12px 24px;
            text-align: left; font-size: 11px; font-weight: 600;
            color: var(--muted); letter-spacing: 1px; text-transform: uppercase;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        td { padding: 14px 24px; font-size: 14px; }

        .doctor-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border);
        }
        .avatar-placeholder {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
        }
        .doctor-info { display: flex; align-items: center; gap: 12px; }
        .doctor-name { font-weight: 600; font-size: 14px; }
        .doctor-id { font-size: 11px; color: var(--muted); font-family: 'JetBrains Mono', monospace; }

        .badge {
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 500;
            display: inline-block; white-space: nowrap;
        }
        .badge-blue   { background: rgba(59,130,246,.15); color: var(--accent); }
        .badge-green  { background: rgba(16,185,129,.15); color: var(--success); }
        .badge-yellow { background: rgba(245,158,11,.15); color: var(--warning); }

        .empty-state {
            text-align: center; padding: 64px 24px;
            color: var(--muted);
        }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: .3; display: block; }
        .empty-state p { font-size: 15px; }

        /* ─── PAGINATION ─── */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        .pagination-info { font-size: 13px; color: var(--muted); }
        .pagination { display: flex; gap: 4px; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 6px;
            font-size: 13px; text-decoration: none;
            border: 1px solid var(--border);
            color: var(--muted); background: var(--surface2);
            transition: all .2s;
        }
        .pagination a:hover { border-color: var(--accent); color: var(--accent); }
        .pagination .active span {
            background: var(--accent); color: #fff; border-color: var(--accent);
        }

        .recent-table td { padding: 12px 20px; }

        /* ─── TABLET (≤1024px) ─── */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 230px; }
            .page-content { padding: 24px; }
            .topbar { padding: 0 24px; }
            .stat-value { font-size: 24px; }
        }

        /* ─── MOBILE (≤768px) ─── */
        @media (max-width: 768px) {
            body { display: block; }

            .sidebar {
                transform: translateX(-100%);
                width: 270px;
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: flex; }

            .main { margin-left: 0; }

            .hamburger { display: flex; }

            .topbar {
                padding: 0 16px;
                height: 58px;
            }
            .topbar-title { font-size: 16px; }
            .topbar-badge { display: none; }

            .page-content { padding: 16px; }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 20px;
            }
            .stat-card { padding: 16px; }
            .stat-value { font-size: 22px; }
            .stat-icon { width: 36px; height: 36px; font-size: 15px; margin-bottom: 10px; }

            .card-header { padding: 14px 16px; }
            .filters-bar { padding: 12px 16px; gap: 8px; }
            thead th { padding: 10px 16px; }
            td { padding: 12px 16px; }

            .pagination-wrap { padding: 12px 16px; }
        }

        /* ─── SMALL MOBILE (≤480px) ─── */
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .page-content { padding: 12px; }
            .topbar-title { font-size: 14px; }
            .btn span { display: none; } /* hide btn text, keep icon */
        }

        @stack('styles')
    </style>
    @stack('styles')
</head>
<body>

{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <img src="{{ asset('uploads/intas-logo.png') }}" style="height:50px; width:auto;">
        </a>
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           onclick="closeSidebarOnMobile()">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="nav-label">Directory</div>
        <a href="{{ route('admin.doctors.index') }}"
           class="nav-item {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}"
           onclick="closeSidebarOnMobile()">
            <i class="fas fa-user-md"></i> Doctors
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">Administrator</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <div class="topbar">
        <div class="topbar-left">
            <button class="hamburger" onclick="openSidebar()" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        </div>
        <div class="topbar-right">
            @yield('topbar-right')
        </div>
    </div>

    <div class="page-content">
        @yield('content')
    </div>
</main>

<script>
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

    function closeSidebarOnMobile() {
        if (window.innerWidth <= 768) closeSidebar();
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) closeSidebar();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });
</script>

</body>
</html>
