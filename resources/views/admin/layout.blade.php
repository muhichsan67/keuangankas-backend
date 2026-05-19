<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard')) — {{ __('admin.app_name') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Dark Theme (default) ────────────────── */
        :root, [data-theme="dark"] {
            --bg-base:        #0b0d14;
            --bg-sidebar:     #111420;
            --bg-card:        rgba(255,255,255,0.04);
            --bg-card-hover:  rgba(255,255,255,0.07);
            --border:         rgba(255,255,255,0.07);
            --accent-1:       #7c3aed;
            --accent-2:       #4f46e5;
            --accent-grad:    linear-gradient(135deg, #7c3aed, #4f46e5);
            --danger:         #ef4444;
            --success:        #10b981;
            --warning:        #f59e0b;
            --info:           #3b82f6;
            --text-primary:   #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted:     #475569;
            --sidebar-w:      260px;
            --input-bg:       rgba(255,255,255,0.05);
            --topbar-bg:      rgba(11,13,20,0.8);
            --modal-bg:       #1a1d27;
            --table-hover:    rgba(255,255,255,0.03);
            --alert-success-bg: rgba(16,185,129,0.1);
            --alert-danger-bg:  rgba(239,68,68,0.1);
            --select-bg:     #1a1d27;
        }

        /* ── Light Theme ─────────────────────────── */
        [data-theme="light"] {
            --bg-base:        #f1f5f9;
            --bg-sidebar:     #ffffff;
            --bg-card:        rgba(0,0,0,0.02);
            --bg-card-hover:  rgba(0,0,0,0.04);
            --border:         rgba(0,0,0,0.08);
            --text-primary:   #0f172a;
            --text-secondary: #475569;
            --text-muted:     #94a3b8;
            --input-bg:       rgba(0,0,0,0.03);
            --topbar-bg:      rgba(241,245,249,0.85);
            --modal-bg:       #ffffff;
            --table-hover:    rgba(0,0,0,0.02);
            --alert-success-bg: rgba(16,185,129,0.08);
            --alert-danger-bg:  rgba(239,68,68,0.08);
            --select-bg:     #ffffff;
        }

        html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-primary); transition: background .3s, color .3s; }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
            background: var(--bg-sidebar); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; z-index: 100;
            transition: background .3s, border-color .3s;
        }
        .sidebar-logo { padding: 28px 24px 20px; border-bottom: 1px solid var(--border); }
        .sidebar-logo .logo-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: var(--accent-grad);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin-bottom: 12px;
        }
        .sidebar-logo .app-name { font-size: 16px; font-weight: 700; color: var(--text-primary); }
        .sidebar-logo .app-sub  { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-label {
            font-size: 10px; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1.5px; padding: 8px 12px 6px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 8px; margin-bottom: 2px;
            color: var(--text-secondary); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all .2s; cursor: pointer;
        }
        .nav-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .nav-item.active {
            background: rgba(124,58,237,0.15); color: #a78bfa;
            border-left: 3px solid var(--accent-1); padding-left: 9px;
        }
        .nav-item .icon { width: 18px; text-align: center; font-size: 15px; }

        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border); }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px; background: var(--bg-card);
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: var(--accent-grad);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name  { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role  { font-size: 11px; color: #a78bfa; }
        .logout-btn {
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 16px; padding: 4px; transition: color .2s;
        }
        .logout-btn:hover { color: var(--danger); }

        /* ── Main Layout ─────────────────────────── */
        .main { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            background: var(--topbar-bg); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50; transition: background .3s;
        }
        .topbar-title { font-size: 18px; font-weight: 600; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .topbar-badge {
            background: rgba(124,58,237,0.15); color: #a78bfa;
            font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px;
            border: 1px solid rgba(124,58,237,0.3);
        }

        /* Toggle buttons in topbar */
        .toggle-btn {
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-secondary); font-size: 12px; font-weight: 500;
            padding: 5px 10px; border-radius: 6px; cursor: pointer;
            transition: all .2s; display: flex; align-items: center; gap: 4px;
        }
        .toggle-btn:hover { background: var(--bg-card-hover); color: var(--text-primary); }

        .content { padding: 32px; flex: 1; }

        /* ── Cards ────────────────────────────────── */
        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 24px; transition: border-color .2s, background .3s;
        }
        .card:hover { border-color: rgba(255,255,255,0.12); }
        [data-theme="light"] .card:hover { border-color: rgba(0,0,0,0.12); }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .card-title { font-size: 15px; font-weight: 600; }
        .card-subtitle { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }

        /* ── Stat Cards ───────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 20px; position: relative; overflow: hidden;
            transition: transform .2s, border-color .2s, background .3s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: var(--accent-grad);
        }
        .stat-card.danger::before  { background: linear-gradient(90deg, #ef4444, #f97316); }
        .stat-card.success::before { background: linear-gradient(90deg, #10b981, #059669); }
        .stat-card.warning::before { background: linear-gradient(90deg, #f59e0b, #f97316); }
        .stat-card.info::before    { background: linear-gradient(90deg, #3b82f6, #06b6d4); }
        .stat-icon { font-size: 24px; margin-bottom: 12px; }
        .stat-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 6px; }

        /* ── Tables ───────────────────────────────── */
        .table-wrap { overflow-x: auto; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            text-align: left; padding: 12px 16px;
            background: rgba(255,255,255,0.03); color: var(--text-muted);
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border);
        }
        [data-theme="light"] thead th { background: rgba(0,0,0,0.02); }
        tbody td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); color: var(--text-secondary); }
        [data-theme="light"] tbody td { border-bottom-color: rgba(0,0,0,0.04); }
        tbody tr { transition: background .15s; }
        tbody tr:hover td { background: var(--table-hover); color: var(--text-primary); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ───────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-purple { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.25); }
        .badge-blue   { background: rgba(59,130,246,0.15); color: #3b82f6; border: 1px solid rgba(59,130,246,0.25); }
        .badge-green  { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.25); }
        .badge-red    { background: rgba(239,68,68,0.15);  color: #ef4444; border: 1px solid rgba(239,68,68,0.25);  }
        .badge-yellow { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }

        /* ── Buttons ──────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;
            border: none; cursor: pointer; text-decoration: none; transition: all .2s;
        }
        .btn-sm { padding: 5px 11px; font-size: 12px; border-radius: 6px; }
        .btn-primary  { background: var(--accent-grad); color: #fff; }
        .btn-primary:hover  { opacity: 0.9; transform: translateY(-1px); }
        .btn-success  { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.25); }
        .btn-success:hover  { background: rgba(16,185,129,0.25); }
        .btn-danger   { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }
        .btn-danger:hover   { background: rgba(239,68,68,0.25); }
        .btn-secondary { background: var(--bg-card); color: var(--text-secondary); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--bg-card-hover); color: var(--text-primary); }

        /* ── Forms ────────────────────────────────── */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text-secondary); margin-bottom: 8px; }
        .form-control {
            width: 100%; padding: 10px 14px; border-radius: 8px; font-size: 14px;
            background: var(--input-bg); border: 1px solid var(--border);
            color: var(--text-primary); font-family: inherit; outline: none;
            transition: border-color .2s, box-shadow .2s, background .3s;
        }
        .form-control:focus { border-color: var(--accent-1); box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }
        .form-control:disabled { opacity: 0.5; cursor: not-allowed; }
        .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; }
        .form-hint  { font-size: 12px; color: var(--text-muted); margin-top: 5px; }
        select.form-control { cursor: pointer; }
        select.form-control option { background: var(--select-bg); }

        /* ── Alerts ───────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; margin-bottom: 20px; border: 1px solid; }
        .alert-success { background: var(--alert-success-bg); border-color: rgba(16,185,129,0.25); color: #10b981; }
        .alert-danger  { background: var(--alert-danger-bg); border-color: rgba(239,68,68,0.25); color: #ef4444; }

        /* ── Tabs ─────────────────────────────────── */
        .tabs { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 1px solid var(--border); }
        .tab-btn {
            padding: 10px 20px; border-radius: 8px 8px 0 0; font-size: 13.5px; font-weight: 500;
            border: none; background: none; cursor: pointer; color: var(--text-secondary);
            transition: all .2s; border-bottom: 2px solid transparent;
        }
        .tab-btn:hover { color: var(--text-primary); background: var(--bg-card); }
        .tab-btn.active { color: #a78bfa; border-bottom-color: var(--accent-1); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* ── Modal ────────────────────────────────── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px); z-index: 200; align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--modal-bg); border: 1px solid var(--border); border-radius: 16px;
            padding: 28px; max-width: 420px; width: 90%; animation: modal-in .2s ease;
        }
        @keyframes modal-in { from { transform: scale(.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-title { font-size: 16px; font-weight: 700; margin-bottom: 10px; }
        .modal-body  { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 20px; line-height: 1.6; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

        /* ── Pagination ───────────────────────────── */
        .pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 10px;
            border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
            border: 1px solid var(--border); color: var(--text-secondary);
            background: var(--bg-card); transition: all .2s;
        }
        .pagination a:hover { border-color: var(--accent-1); color: #a78bfa; }
        .pagination .active { background: rgba(124,58,237,0.2); border-color: var(--accent-1); color: #a78bfa; }

        /* ── Misc ─────────────────────────────────── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-10 { gap: 10px; }
        .gap-16 { gap: 16px; }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }
        .text-right { text-align: right; }
        .text-muted  { color: var(--text-muted); font-size: 12px; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .content { padding: 20px 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<script>
// Apply saved theme immediately to prevent flash
(function(){
    const t = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();
</script>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">💰</div>
        <div class="app-name">{{ __('admin.app_name') }}</div>
        <div class="app-sub">{{ __('admin.admin_panel') }}</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">{{ __('admin.main_menu') }}</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> {{ __('admin.dashboard') }}
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="icon">👥</span> {{ __('admin.user_management') }}
        </a>
        <a href="{{ route('admin.category.index') }}" class="nav-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
            <span class="icon">🏷️</span> {{ __('admin.category') }}
        </a>

        <div class="nav-section-label" style="margin-top:16px">{{ __('admin.data') }}</div>
        <a href="{{ route('admin.trash.index') }}" class="nav-item {{ request()->routeIs('admin.trash.*') ? 'active' : '' }}">
            <span class="icon">🗑️</span> {{ __('admin.trash_recycle') }}
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ __('admin.administrator') }}</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="{{ __('admin.logout') }}">⏏</button>
            </form>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="main">
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', __('admin.dashboard'))</div>
        <div class="topbar-actions">
            <!-- Theme Toggle -->
            <button class="toggle-btn" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">
                <span id="themeIcon">🌙</span>
            </button>
            <!-- Language Toggle -->
            <form method="POST" action="{{ route('locale.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" style="display:inline;">
                @csrf
                <button type="submit" class="toggle-btn">
                    🌐 {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                </button>
            </form>
            <span class="topbar-badge">🛡️ {{ __('admin.admin_badge') }}</span>
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ __('admin.success_prefix') }} {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ __('admin.error_prefix') }} {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<!-- Confirm Modal -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal">
        <div class="modal-title" id="confirmTitle">{{ __('admin.confirm_action') }}</div>
        <div class="modal-body" id="confirmBody">{{ __('admin.are_you_sure') }}</div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">{{ __('admin.btn_cancel') }}</button>
            <form id="confirmForm" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('admin.btn_confirm') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
// ── Theme ────────────────────────────────────
function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
}
function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) icon.textContent = theme === 'dark' ? '🌙' : '☀️';
}
updateThemeIcon(document.documentElement.getAttribute('data-theme') || 'dark');

// ── Modal ────────────────────────────────────
function openModal(title, body, action, method) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmBody').textContent = body;
    const form = document.getElementById('confirmForm');
    form.action = action;
    let input = form.querySelector('input[name="_method"]');
    if (method === 'DELETE') {
        if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = '_method'; form.appendChild(input); }
        input.value = 'DELETE';
    } else if (input) { input.remove(); }
    document.getElementById('confirmModal').classList.add('open');
}
function closeModal() { document.getElementById('confirmModal').classList.remove('open'); }
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@stack('scripts')
</body>
</html>
