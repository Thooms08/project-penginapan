<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Penginapan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Favicon dinamis dari logo hotel --}}
    @php $__faviconExists = file_exists(public_path('favicon.png')); @endphp
    @if($__faviconExists)
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23eab308'/><text x='16' y='22' text-anchor='middle' font-size='18' font-family='sans-serif' fill='%23713f12'>H</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ─── Font ─────────────────────────────────── */
        body { font-family: 'Inter', sans-serif; }

        /* ─── Tema kuning ───────────────────────────── */
        :root {
            --y:     #eab308;
            --yd:    #ca8a04;
            --yl:    #facc15;
            --y50:   #fefce8;
            --y100:  #fef9c3;
            --ytext: #713f12;
            --sbg:   #1a1500;
            --ssep:  rgba(255,255,255,0.08);
            --smut:  rgba(255,255,255,0.5);
        }

        /* ─── Sidebar ───────────────────────────────── */
        .sidebar {
            width: 260px;
            background: var(--sbg);
            transition: transform 0.28s cubic-bezier(0.4,0,0.2,1),
                        width 0.28s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        /* State: sidebar ditutup di desktop (collapse) */
        .sidebar.collapsed {
            width: 0;
            transform: translateX(0);
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
            white-space: nowrap;
        }
        .nav-item:hover  { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: var(--y); color: var(--ytext); font-weight: 600; }

        .nav-label {
            font-size: 0.67rem; font-weight: 700;
            color: var(--smut);
            letter-spacing: 0.08em; text-transform: uppercase;
            padding-left: 0.4rem; margin-bottom: 0.4rem;
            white-space: nowrap;
        }

        /* ─── Avatar ────────────────────────────────── */
        .avatar-yellow {
            width: 34px; height: 34px; border-radius: 9999px;
            background: var(--y); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: var(--ytext);
        }
        .avatar-yellow-lg {
            width: 40px; height: 40px; border-radius: 11px;
            background: var(--y); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }

        /* ─── Layout: topbar & content mengikuti lebar sidebar ─── */
        .topbar, .main-wrap {
            margin-left: 260px;
            transition: margin-left 0.28s cubic-bezier(0.4,0,0.2,1);
        }
        body.sidebar-collapsed .topbar,
        body.sidebar-collapsed .main-wrap {
            margin-left: 0;
        }

        /* ─── Toggle button (hamburger / arrow) ─── */
        .sidebar-toggle-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid #e2e8f0; background: #f8fafc;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0;
            transition: background 0.15s, border-color 0.15s;
        }
        .sidebar-toggle-btn:hover { background: #fff; border-color: #cbd5e1; }

        /* ─── Mobile overlay ────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 35;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* ─── Responsive: mobile (≤1024px) ─────────── */
        @media (max-width: 1024px) {
            /* Di mobile sidebar selalu off-canvas (translate) */
            .sidebar {
                width: 260px !important;      /* jangan collapse di mobile */
                transform: translateX(-100%);  /* default tersembunyi */
                position: fixed;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            /* Topbar & main tidak pakai margin di mobile */
            .topbar, .main-wrap {
                margin-left: 0 !important;
            }
        }

        /* ─── SweetAlert custom theme ───────────────── */
        .swal-confirm-btn {
            background: #eab308 !important;
            color: #713f12 !important;
            font-weight: 600 !important;
        }
        .swal-confirm-btn:hover { background: #ca8a04 !important; color:#fff !important; }

        .swal-delete-btn {
            background: #ef4444 !important;
            color: #fff !important;
            font-weight: 600 !important;
        }
        .swal-delete-btn:hover { background: #dc2626 !important; }

        .swal-toast-popup {
            font-family: 'Inter', sans-serif !important;
            font-size: 0.875rem !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

{{-- ══ SIDEBAR ══ --}}
@php
    /* Load foto profil dari tabel profile_users sekali di sini,
       dipakai oleh sidebar & topbar */
    $__adminProfileFoto = null;
    if (Auth::check()) {
        $__adminProfile = \Modules\Profile\Models\ProfileUser::where('user_id', Auth::id())->first();
        if ($__adminProfile && $__adminProfile->foto) {
            $__adminProfileFoto = asset($__adminProfile->foto);
        }
    }
    // Load profil hotel untuk branding sidebar
    $__hotelProfile = \Modules\Profile\Models\ProfileHotel::first();
    $__hotelName    = $__hotelProfile?->name ?: 'Penginapan';
    $__hotelLogo    = ($__hotelProfile?->logo && file_exists(public_path($__hotelProfile->logo)))
                        ? asset($__hotelProfile->logo)
                        : null;
@endphp
<aside class="sidebar fixed top-0 left-0 h-full flex flex-col z-40" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-sep-b flex items-center gap-3 px-5 py-5">
        @if($__hotelLogo)
            <img src="{{ $__hotelLogo }}" alt="Logo Hotel"
                 class="w-10 h-10 rounded-[11px] object-contain flex-shrink-0"
                 style="background:#fff;border:1px solid rgba(255,255,255,0.15);">
        @else
            <div class="avatar-yellow-lg">
                <svg class="w-5 h-5" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        @endif
        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white leading-tight truncate">{{ $__hotelName }}</p>
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

        <a href="{{ route('admin.rooms.index') }}"
           class="nav-item mt-1 {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Kelola Kamar
        </a>

        <a href="{{ route('admin.bookings.index') }}"
           class="nav-item mt-1 {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Booking
        </a>

        <a href="{{ route('admin.check.index') }}"
           class="nav-item mt-1 {{ request()->routeIs('admin.check.*') ? 'active' : '' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Check In & Out
        </a>

        <a href="#" class="nav-item mt-1">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Tamu
        </a>

        <a href="{{ route('admin.hotel.index') }}"
           class="nav-item mt-1 {{ request()->routeIs('admin.hotel.*') ? 'active' : '' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Profil Hotel
        </a>

        {{-- ── Lainnya (submenu collapsible) ── --}}
        <div class="mt-4">
            <p class="nav-label">Lainnya</p>
            @php
                $isOtherActive = request()->routeIs('admin.other.*');
            @endphp
            {{-- Parent toggle --}}
            <button type="button" id="otherMenuBtn"
                    onclick="toggleOtherMenu()"
                    class="nav-item mt-1 w-full justify-between {{ $isOtherActive ? 'active' : '' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Lainnya
                </span>
                <svg id="otherMenuChevron"
                     class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isOtherActive ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            {{-- Submenu --}}
            <div id="otherMenuSub"
                 class="{{ $isOtherActive ? '' : 'hidden' }} pl-3 mt-1 space-y-0.5">
                <a href="{{ route('admin.other.about') }}"
                   class="nav-item {{ request()->routeIs('admin.other.about') ? 'active' : '' }}"
                   style="font-size:0.82rem; padding-left:1rem;">
                    <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tentang
                </a>
                <a href="{{ route('admin.other.privacy-policy') }}"
                   class="nav-item {{ request()->routeIs('admin.other.privacy-policy') ? 'active' : '' }}"
                   style="font-size:0.82rem; padding-left:1rem;">
                    <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Kebijakan &amp; Privasi
                </a>
                <a href="{{ route('admin.other.terms-conditions') }}"
                   class="nav-item {{ request()->routeIs('admin.other.terms-conditions') ? 'active' : '' }}"
                   style="font-size:0.82rem; padding-left:1rem;">
                    <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Syarat &amp; Ketentuan
                </a>
            </div>
        </div>

        <div class="mt-4">
            <p class="nav-label">Akun</p>
            <a href="{{ route('admin.profile.index') }}"
               class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>
        </div>
    </nav>

    {{-- User + Logout --}}
    <div class="sidebar-sep-t px-3 py-4">
        <div class="flex items-center gap-3 px-1.5 py-2 mb-2 min-w-0">
            @if($__adminProfileFoto)
                <img src="{{ $__adminProfileFoto }}" alt="avatar"
                    class="w-[34px] h-[34px] rounded-full object-cover shrink-0"
                    style="border:2px solid rgba(255,255,255,0.15);">
            @elseif(Auth::user()->avatar)
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

        {{-- Logout — form tersembunyi, trigger via SweetAlert --}}
        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
        </form>
        <button type="button" onclick="confirmLogout()" class="nav-item">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </button>
    </div>
</aside>

{{-- Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ══ TOPBAR ══ --}}
<div class="topbar h-16 bg-white border-b border-slate-200
            flex items-center justify-between px-5 lg:px-7
            sticky top-0 z-30 shadow-sm">

    <div class="flex items-center gap-3">
        {{-- Toggle button — berfungsi di SEMUA ukuran layar --}}
        <button onclick="toggleSidebar()" id="sidebarToggleBtn"
                class="sidebar-toggle-btn" aria-label="Toggle sidebar">
            {{-- Icon hamburger (default) / arrow (saat sidebar terbuka di desktop) --}}
            <svg id="toggleIconBars" class="w-[18px] h-[18px] text-slate-500"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg id="toggleIconClose" class="w-[18px] h-[18px] text-slate-500 hidden"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"/>
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
        {{-- Area avatar + nama — klik untuk ke halaman Profil --}}
        <a href="{{ route('admin.profile.index') }}"
           title="Profil Saya"
           class="flex items-center gap-3 px-2.5 py-1.5 rounded-xl transition-colors
                  hover:bg-slate-100 {{ request()->routeIs('admin.profile.*') ? 'bg-yellow-50 ring-1 ring-yellow-300' : '' }}">
            <div id="topbarAvatarWrap">
                @if($__adminProfileFoto)
                    <img src="{{ $__adminProfileFoto }}"
                         alt="avatar"
                         id="topbarAvatarImg"
                         class="w-[34px] h-[34px] rounded-full object-cover flex-shrink-0"
                         style="border:2px solid var(--y100);">
                @elseif(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}"
                         alt="avatar"
                         id="topbarAvatarImg"
                         class="w-[34px] h-[34px] rounded-full object-cover flex-shrink-0"
                         style="border:2px solid var(--y100);">
                @else
                    <div class="avatar-yellow flex-shrink-0" id="topbarAvatarInitials">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="hidden sm:block leading-tight">
                <p class="text-[0.82rem] font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                <p class="text-[0.72rem] text-slate-400">Admin</p>
            </div>
        </a>
    </div>
</div>

{{-- ══ MAIN ══ --}}
<main class="main-wrap p-5 lg:p-8 min-h-[calc(100vh-4rem)]">
    @yield('content')
</main>

<script>
/* ════════════════════════════════════════════════════
   SIDEBAR TOGGLE — desktop & mobile
   Desktop  : collapse (width→0, margin→0)
   Mobile   : off-canvas slide (translateX)
════════════════════════════════════════════════════ */

const MOBILE_BP = 1024;   // px

// Baca state tersimpan dari localStorage (desktop)
let desktopOpen = localStorage.getItem('sidebarOpen') !== 'false';

function isDesktop() {
    return window.innerWidth > MOBILE_BP;
}

function updateIcons(open) {
    document.getElementById('toggleIconBars').classList.toggle('hidden', open);
    document.getElementById('toggleIconClose').classList.toggle('hidden', !open);
}

function applySidebarState() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

    if (isDesktop()) {
        // Desktop: gunakan class 'collapsed' pada sidebar & body
        if (desktopOpen) {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
        } else {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
        // Pastikan mobile state bersih
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
        updateIcons(desktopOpen);
    } else {
        // Mobile: gunakan class 'mobile-open' & overlay
        // Tidak ada perubahan otomatis saat resize — biarkan apa adanya
        const mobileOpen = sidebar.classList.contains('mobile-open');
        updateIcons(mobileOpen);
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (isDesktop()) {
        desktopOpen = !desktopOpen;
        localStorage.setItem('sidebarOpen', desktopOpen);
        applySidebarState();
    } else {
        // Mobile: toggle off-canvas
        const isOpen = sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('show', isOpen);
        updateIcons(isOpen);
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('show');
    updateIcons(false);
}

// Init on load
applySidebarState();

// Re-apply on resize (desktop↔mobile transition)
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        // Bersihkan state yang tidak relevan
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (isDesktop()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        } else {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
        }
        applySidebarState();
    }, 100);
});

/* ════════════════════════════════════════════════════
   OTHER SUBMENU TOGGLE
════════════════════════════════════════════════════ */
function toggleOtherMenu() {
    const sub     = document.getElementById('otherMenuSub');
    const chevron = document.getElementById('otherMenuChevron');
    const isHidden = sub.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180', !isHidden);
}

/* ════════════════════════════════════════════════════
   SWEETALERT — Konfirmasi Logout
════════════════════════════════════════════════════ */
function confirmLogout() {
    Swal.fire({
        title: 'Keluar dari akun?',
        text:  'Sesi Anda akan diakhiri dan diarahkan ke halaman login.',
        icon:  'question',
        showCancelButton:  true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText:  'Batal',
        reverseButtons:    true,
        focusCancel:       true,
        customClass: {
            confirmButton: 'swal-confirm-btn',
        },
        buttonsStyling: true,
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
}
</script>

@stack('scripts')
</body>
</html>
