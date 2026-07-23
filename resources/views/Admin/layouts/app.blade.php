<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Penginapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ── Warna tema: Amber/Kuning (hardcode) ── */
        :root {
            --primary:       #eab308;
            --primary-dark:  #ca8a04;
            --primary-light: #facc15;
            --primary-50:    #fefce8;
            --primary-100:   #fef9c3;
            --sidebar-bg:    #1a1500;
            --sidebar-sep:   rgba(255,255,255,0.08);
            --sidebar-muted: rgba(255,255,255,0.5);
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f8fafc; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px; min-height: 100vh;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
            background: var(--sidebar-bg);
            z-index: 40;
            transition: transform 0.25s ease;
        }
        .sep-b { border-bottom: 1px solid var(--sidebar-sep); }
        .sep-t { border-top:    1px solid var(--sidebar-sep); }

        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 0.875rem; border-radius: 0.625rem;
            font-size: 0.875rem; font-weight: 500;
            color: var(--sidebar-muted);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            background: none; border: none; cursor: pointer; text-align: left; width: 100%;
        }
        .nav-item:hover  { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: var(--primary); color: #713f12; font-weight: 600; }

        /* ── Topbar ── */
        .topbar {
            margin-left: 260px; height: 64px;
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky; top: 0; z-index: 30;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        /* ── Main content ── */
        .main-content { margin-left: 260px; padding: 2rem; min-height: calc(100vh - 64px); }

        /* ── Mobile overlay ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0; z-index: 35;
            background: rgba(0,0,0,0.4);
        }
        .sidebar-overlay.open { display: block; }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
        @media (max-width: 640px) {
            .main-content { padding: 1.25rem 1rem; }
            .hide-sm { display: none; }
        }
        @media (max-width: 480px) { .hide-xs { display: none; } }
        @media (max-width: 1024px) { #hamburgerBtn { display: flex !important; } }
    </style>
</head>
<body>

{{-- ══════ SIDEBAR ══════ --}}
<aside class="sidebar" id="sidebar">
    <div class="sep-b" style="padding:1.25rem;display:flex;align-items:center;gap:0.875rem;">
        <div style="width:40px;height:40px;border-radius:11px;background:var(--primary);
                    flex-shrink:0;display:flex;align-items:center;justify-content:center;">
            <svg style="width:22px;height:22px;color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p style="font-size:0.95rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">Penginapan</p>
            <p style="font-size:0.72rem;color:var(--sidebar-muted);margin:0;line-height:1.4;">Admin Panel</p>
        </div>
    </div>

    <nav style="flex:1;padding:1rem 0.75rem;overflow-y:auto;">
        <p style="font-size:0.68rem;font-weight:700;color:var(--sidebar-muted);letter-spacing:0.08em;
                   text-transform:uppercase;margin:0 0 0.5rem 0.4rem;">Menu</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zM13 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>
        <a href="#" class="nav-item" style="margin-top:0.25rem;">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Kamar
        </a>
        <a href="#" class="nav-item" style="margin-top:0.25rem;">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Booking
        </a>
        <a href="#" class="nav-item" style="margin-top:0.25rem;">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Tamu
        </a>
    </nav>

    <div class="sep-t" style="padding:1rem 0.75rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0.375rem;margin-bottom:0.5rem;">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="avatar"
                    style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:var(--primary);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="color:#713f12;font-size:0.8rem;font-weight:700;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            <div style="min-width:0;">
                <p style="font-size:0.82rem;font-weight:600;color:#fff;margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Auth::user()->name }}
                </p>
                <p style="font-size:0.72rem;color:var(--sidebar-muted);margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ══════ TOPBAR ══════ --}}
<div class="topbar">
    <div style="display:flex;align-items:center;gap:0.875rem;">
        <button onclick="toggleSidebar()" id="hamburgerBtn"
            style="width:36px;height:36px;border-radius:8px;border:1px solid #e2e8f0;
                   background:#f8fafc;cursor:pointer;display:none;
                   align-items:center;justify-content:center;"
            aria-label="Toggle menu">
            <svg style="width:18px;height:18px;color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h1 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0;line-height:1.2;">
                @yield('page_title', 'Dashboard')
            </h1>
            <p style="font-size:0.75rem;color:#94a3b8;margin:0;">
                @yield('page_subtitle', date('l, d F Y'))
            </p>
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:0.75rem;">
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="avatar"
                style="width:34px;height:34px;border-radius:50%;object-fit:cover;
                       border:2px solid var(--primary-100);">
        @else
            <div style="width:34px;height:34px;border-radius:50%;background:var(--primary);
                         display:flex;align-items:center;justify-content:center;">
                <span style="color:#713f12;font-size:0.8rem;font-weight:700;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
        @endif
        <div class="hide-sm">
            <p style="font-size:0.82rem;font-weight:600;color:#0f172a;margin:0;">{{ Auth::user()->name }}</p>
            <p style="font-size:0.72rem;color:#94a3b8;margin:0;">Admin</p>
        </div>
    </div>
</div>

{{-- ══════ MAIN CONTENT ══════ --}}
<main class="main-content">
    @yield('content')
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
</script>
</body>
</html>
