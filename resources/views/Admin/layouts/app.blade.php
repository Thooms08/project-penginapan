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
        /* ─── Font ─────────────────────────────────── */
        body { font-family: 'Inter', sans-serif; }

        /* ─── Warna tema kuning (CSS vars) ─────────── */
        :root {
            --y:     #eab308;   /* yellow-500  */
            --yd:    #ca8a04;   /* yellow-600  */
            --yl:    #facc15;   /* yellow-400  */
            --y50:   #fefce8;
            --y100:  #fef9c3;
            --ytext: #713f12;   /* teks di atas kuning */
            --sbg:   #1a1500;   /* sidebar bg  */
            --ssep:  rgba(255,255,255,0.08);
            --smut:  rgba(255,255,255,0.5);
        }

        /* ─── Sidebar ───────────────────────────────── */
        .sidebar {
            width: 260px;
            background: var(--sbg);
            transition: transform 0.25s ease;
        }
        .sidebar-sep-b { border-bottom: 1px solid var(--ssep); }
        .sidebar-sep-t { border-top:    1px solid var(--ssep); }

        /* ─── Nav item ──────────────────────────────── */
        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 0.875rem; border-radius: 0.625rem;
            font-size: 0.875rem; font-weight: 500;
            color: var(--smut);
            text-decoration: none;
            background: transparent; border: none;
            cursor: pointer; width: 100%; text-align: left;
            transition: background 0.15s, color 0.15s;
        }
        .nav-item:hover  { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: var(--y); color: var(--ytext); font-weight: 600; }

        /* ─── Section label di nav ───────────────────── */
        .nav-label {
            font-size: 0.67rem; font-weight: 700;
            color: var(--smut);
            letter-spacing: 0.08em; text-transform: uppercase;
            padding-left: 0.4rem; margin-bottom: 0.4rem;
        }

        /* ─── Avatar initials ────────────────────────── */
        .avatar-yellow {
            width: 34px; height: 34px; border-radius: 9999px;
            background: var(--y);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: var(--ytext);
            flex-shrink: 0;
        }
        .avatar-yellow-lg {
            width: 40px; height: 40px; border-radius: 11px;
            background: var(--y);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* ─── Hamburger button ──────────────────────── */
        #hamburgerBtn { display: none; }

        /* ─── Responsive ────────────────────────────── */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar-offset { margin-left: 0 !important; }
            .content-offset { margin-left: 0 !important; }
            #hamburgerBtn { display: flex; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar fixed top-0 left-0 h-full flex flex-col z-40" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-sep-b flex items-center gap-3 px-5 py-5">
        <div class="avatar-yellow-lg">
            <svg class="w-5 h-5" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-extrabold text-white leading-tight">Penginapan</p>
            <p class="text-xs leading-snug" style="color:var(--smut);">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <p class="nav-label">Menu</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zM13 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>
        <a href="#" class="nav-item mt-1">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Kamar
        </a>
        <a href="#" class="nav-item mt-1">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Booking
        </a>
        <a href="#" class="nav-item mt-1">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Tamu
        </a>
    </nav>

    {{-- User + Logout --}}
    <div class="sidebar-sep-t px-3 py-4">
        <div class="flex items-center gap-3 px-1.5 py-2 mb-2 min-w-0">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="avatar"
                    class="w-[34px] h-[34px] rounded-full object-cover shrink-0">
            @else
                <div class="avatar-yellow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-[0.82rem] font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[0.72rem] truncate" style="color:var(--smut);">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="fixed inset-0 z-[35] bg-black/40 hidden" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ══ TOPBAR ══ --}}
<div class="topbar-offset ml-[260px] h-16 bg-white border-b border-slate-200
            flex items-center justify-between px-7
            sticky top-0 z-30 shadow-sm">

    <div class="flex items-center gap-3">
        {{-- Hamburger --}}
        <button onclick="toggleSidebar()" id="hamburgerBtn"
            class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50
                   flex items-center justify-center cursor-pointer"
            aria-label="Toggle menu">
            <svg class="w-[18px] h-[18px] text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h1 class="text-[1.05rem] font-bold text-slate-900 leading-tight">
                @yield('page_title', 'Dashboard')
            </h1>
            <p class="text-[0.75rem] text-slate-400">
                @yield('page_subtitle', date('l, d F Y'))
            </p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="avatar"
                class="w-[34px] h-[34px] rounded-full object-cover"
                style="border:2px solid var(--y100);">
        @else
            <div class="avatar-yellow">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        @endif
        <div class="hidden sm:block">
            <p class="text-[0.82rem] font-semibold text-slate-900">{{ Auth::user()->name }}</p>
            <p class="text-[0.72rem] text-slate-400">Admin</p>
        </div>
    </div>
</div>

{{-- ══ MAIN ══ --}}
<main class="content-offset ml-[260px] p-8 min-h-[calc(100vh-4rem)]">
    @yield('content')
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}
</script>
</body>
</html>
