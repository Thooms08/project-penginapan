<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — {{ optional(\Modules\Profile\Models\ProfileHotel::first())->name ?? 'Penginapan' }}</title>
    {{-- Favicon dinamis: logo hotel → favicon.png → SVG fallback --}}
    @php
        $__faviconHotel  = \Modules\Profile\Models\ProfileHotel::first();
        $__faviconLogo   = $__faviconHotel?->logo;
        $__faviconLogoOk = $__faviconLogo && file_exists(public_path($__faviconLogo));
        $__faviconPngOk  = file_exists(public_path('favicon.png'));
    @endphp
    @if($__faviconLogoOk)
        <link rel="icon" type="image/png" href="{{ asset($__faviconLogo) }}?v={{ filemtime(public_path($__faviconLogo)) }}">
    @elseif($__faviconPngOk)
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23eab308'/><text x='16' y='22' text-anchor='middle' font-size='18' font-family='sans-serif' fill='%23713f12'>H</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }

        /* ─── CSS Variables ─── */
        :root {
            --y: #eab308; --yd: #ca8a04; --yl: #facc15;
            --y50: #fefce8; --y100: #fef9c3; --ytext: #713f12;
        }

        /* ─── TOPBAR (Desktop) ─── */
        .pub-topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            height: 64px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 1px 12px rgba(0,0,0,0.06);
            display: flex; align-items: center;
            padding: 0 1.5rem; gap: 1rem;
        }
        .pub-topbar-brand { display: flex; align-items: center; gap: 0.65rem; text-decoration: none; }
        .pub-topbar-brand-logo {
            width: 36px; height: 36px; border-radius: 10px;
            object-fit: contain; background: var(--y50);
        }
        .pub-topbar-brand-text { font-size: 1.05rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .pub-topbar-nav { display: flex; align-items: center; gap: 0.25rem; flex: 1; justify-content: center; }
        .pub-topbar-nav a {
            font-size: 0.875rem; font-weight: 500; color: #475569;
            text-decoration: none; padding: 0.45rem 0.875rem;
            border-radius: 0.625rem; transition: background 0.15s, color 0.15s;
        }
        .pub-topbar-nav a:hover, .pub-topbar-nav a.active {
            background: var(--y50); color: var(--ytext);
        }
        .pub-topbar-right { display: flex; align-items: center; gap: 0.75rem; }
        .pub-topbar-btn-login {
            font-size: 0.875rem; font-weight: 600; color: var(--ytext);
            background: var(--y); border: none; border-radius: 0.625rem;
            padding: 0.5rem 1.125rem; cursor: pointer; text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .pub-topbar-btn-login:hover { background: var(--yd); color: #fff; }
        .pub-topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            object-fit: cover; border: 2px solid var(--y100);
            background: var(--y); display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 0.875rem;
            color: var(--ytext); text-decoration: none; cursor: pointer;
        }

        /* ─── BOTTOM BAR (Mobile only) ─── */
        .pub-bottombar {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 60;
            height: 68px;
            background: rgba(255,255,255,0.97);
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -1px 0 rgba(15, 23, 42, 0.04);
            padding: 0 0.25rem;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .pub-bottombar-inner {
            display: flex; height: 100%; align-items: center;
            justify-content: space-around;
        }
        .bbar-item {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 3px; flex: 1; height: 100%;
            text-decoration: none; color: #94a3b8;
            font-size: 0.62rem; font-weight: 600; border: none;
            background: transparent; cursor: pointer; padding: 0;
            transition: color 0.15s, transform 0.15s;
            -webkit-tap-highlight-color: transparent;
            border-radius: 12px; position: relative;
        }
        .bbar-item:active { transform: scale(0.92); }
        .bbar-item.active { color: var(--ytext); }
        .bbar-item.active .bbar-icon-wrap {
            background: var(--y); box-shadow: 0 2px 8px rgba(234,179,8,0.35);
        }
        .bbar-item.active svg { color: var(--ytext); }
        .bbar-icon-wrap {
            width: 36px; height: 36px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            background: transparent; transition: background 0.15s;
        }
        .bbar-item:not(.active):hover .bbar-icon-wrap { background: #f1f5f9; }
        .bbar-badge {
            position: absolute; top: 2px; right: calc(50% - 22px);
            width: 8px; height: 8px; border-radius: 50%;
            background: #ef4444; border: 2px solid #fff;
        }

        /* ─── Main content padding ─── */
        .pub-main {
            padding-top: 64px;   /* topbar height */
            padding-bottom: 0;
        }

        /* ─── Responsive ─── */
        @media (max-width: 767px) {
            .pub-topbar { display: none !important; }
            .pub-bottombar { display: block; }
            .pub-main { padding-top: 0; padding-bottom: 76px; }
            .pub-topbar-nav { display: none; }
        }

        /* ─── SweetAlert theme ─── */
        .swal-confirm-btn { background: #eab308 !important; color: #713f12 !important; font-weight:600!important; }
        .swal-confirm-btn:hover { background: #ca8a04 !important; color:#fff!important; }

        /* ─── Topbar nav button (with chevron) ─── */
        .pub-topbar-nav-btn {
            font-size: 0.875rem; font-weight: 500; color: #475569;
            display: inline-flex; align-items: center; gap: 0.15rem;
            padding: 0.45rem 0.875rem; border-radius: 0.625rem;
            background: transparent; border: none; cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .pub-topbar-nav-btn:hover,
        .pub-topbar-nav-btn.active { background: var(--y50); color: var(--ytext); }

        /* ─── Dropdown menu ─── */
        .pub-topbar-dropdown { position: relative; }
        .pub-drop-menu {
            position: absolute; top: calc(100% + 6px); left: 50%;
            transform: translateX(-50%);
            min-width: 200px;
            background: white; border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.10);
            padding: 0.375rem;
            z-index: 100;
            animation: dropFade 0.15s ease;
        }
        @keyframes dropFade {
            from { opacity:0; transform: translateX(-50%) translateY(-6px); }
            to   { opacity:1; transform: translateX(-50%) translateY(0); }
        }
        .pub-drop-item {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.55rem 0.75rem; border-radius: 0.625rem;
            font-size: 0.845rem; font-weight: 500; color: #374151;
            text-decoration: none; transition: background 0.12s, color 0.12s;
        }
        .pub-drop-item:hover { background: var(--y50); color: var(--ytext); }
        .pub-drop-item.active { background: var(--y100); color: var(--ytext); font-weight: 600; }

        /* ─── Info bottom sheet (mobile) ─── */
        .info-sheet-overlay {
            display: none; position: fixed; inset: 0; z-index: 70;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);
        }
        .info-sheet-overlay.show { display: block; }
        .info-sheet {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 71;
            background: white; border-radius: 1.25rem 1.25rem 0 0;
            padding: 0 0 env(safe-area-inset-bottom, 0px);
            transform: translateY(100%);
            transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 -4px 24px rgba(0,0,0,0.12);
        }
        .info-sheet.show { transform: translateY(0); }
        .info-sheet-handle {
            width: 36px; height: 4px; border-radius: 2px;
            background: #e2e8f0; margin: 0.75rem auto 0;
        }
        .info-sheet-item {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 0.875rem 1.25rem; text-decoration: none;
            color: #1e293b; font-size: 0.9rem; font-weight: 500;
            border-bottom: 1px solid #f1f5f9; transition: background 0.12s;
        }
        .info-sheet-item:last-child { border-bottom: none; }
        .info-sheet-item:hover { background: #f8fafc; }
        .info-sheet-item:active { background: var(--y50); }
        .info-sheet-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 min-h-screen">

@php
    $__hotel     = \Modules\Profile\Models\ProfileHotel::first();
    $__hotelName = $__hotel?->name ?: 'Penginapan';
    $__hotelLogo = ($__hotel?->logo && file_exists(public_path($__hotel->logo)))
                     ? asset($__hotel->logo) : null;
@endphp

{{-- ══ TOPBAR (Desktop ≥ 768px) ══ --}}
<header class="pub-topbar" id="pubTopbar">
    {{-- Brand --}}
    <a href="{{ route('index') }}" class="pub-topbar-brand">
        @if($__hotelLogo)
            <img src="{{ $__hotelLogo }}" alt="{{ $__hotelName }}" class="pub-topbar-brand-logo">
        @else
            <div class="pub-topbar-brand-logo flex items-center justify-center"
                 style="background:var(--y);">
                <svg class="w-5 h-5" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        @endif
        <span class="pub-topbar-brand-text">{{ $__hotelName }}</span>
    </a>

    {{-- Nav Links --}}
    <nav class="pub-topbar-nav">
        <a href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('index') }}#kamar">Kamar</a>
        <a href="{{ route('index') }}#tentang">Tentang</a>

        {{-- Dropdown: Info Lainnya --}}
        <div class="pub-topbar-dropdown" id="infoDropdown">
            <button type="button"
                    class="pub-topbar-nav-btn {{ request()->routeIs('about') || request()->routeIs('privacy-policy') || request()->routeIs('terms-conditions') ? 'active' : '' }}"
                    onclick="toggleInfoDropdown()" id="infoDropBtn"
                    aria-haspopup="true" aria-expanded="false">
                Info
                <svg class="w-3 h-3 ml-0.5 transition-transform duration-150" id="infoDropChevron"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="infoDropMenu"
                 class="pub-drop-menu hidden"
                 role="menu">
                <a href="{{ route('about') }}" class="pub-drop-item {{ request()->routeIs('about') ? 'active' : '' }}" role="menuitem">
                    <svg class="w-4 h-4 shrink-0" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tentang Kami
                </a>
                <a href="{{ route('privacy-policy') }}" class="pub-drop-item {{ request()->routeIs('privacy-policy') ? 'active' : '' }}" role="menuitem">
                    <svg class="w-4 h-4 shrink-0" style="color:#9d174d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Kebijakan &amp; Privasi
                </a>
                <a href="{{ route('terms-conditions') }}" class="pub-drop-item {{ request()->routeIs('terms-conditions') ? 'active' : '' }}" role="menuitem">
                    <svg class="w-4 h-4 shrink-0" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Syarat &amp; Ketentuan
                </a>
            </div>
        </div>

        @if($__hotel?->wa)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $__hotel->wa) }}" target="_blank" rel="noopener">Kontak</a>
        @endif
    </nav>

    {{-- Right side --}}
    <div class="pub-topbar-right">
        @auth
            {{-- User badge --}}
            @php
                $__pubAvatar = null;
                $__pubProfile = \Modules\Profile\Models\ProfileUser::where('user_id', Auth::id())->first();
                if ($__pubProfile?->foto) $__pubAvatar = asset($__pubProfile->foto);
                elseif (Auth::user()->avatar) $__pubAvatar = Auth::user()->avatar;
                $__isVisitor = Auth::user()->role === 'visitor';
            @endphp
            <div class="flex items-center gap-2.5">
                {{-- Avatar: klik ke profil visitor / dashboard admin --}}
                @if($__isVisitor)
                    <a href="{{ route('visitor.profile.index') }}" title="Profil Saya"
                       class="pub-topbar-avatar {{ request()->routeIs('visitor.profile.*') ? 'ring-2 ring-yellow-400' : '' }}">
                @else
                    <a href="{{ route('admin.profile.index') }}" title="Profil Admin"
                       class="pub-topbar-avatar">
                @endif
                    @if($__pubAvatar)
                        <img src="{{ $__pubAvatar }}" alt="avatar"
                             style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--y100);">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </a>
                <div class="hidden md:block leading-tight">
                    <p class="text-[0.82rem] font-semibold text-slate-900 leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[0.7rem] text-slate-400 mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                <a href="{{ route('admin.dashboard') }}"
                   class="text-[0.82rem] font-semibold px-3 py-1.5 rounded-lg text-slate-600
                          border border-slate-200 hover:bg-slate-50 transition-colors">
                    Dashboard
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" id="topbarLogoutForm">
                @csrf
            </form>
            <button type="button" onclick="pubConfirmLogout()"
                    class="text-[0.82rem] font-semibold px-3 py-1.5 rounded-lg text-red-600
                           border border-red-200 hover:bg-red-50 transition-colors">
                Logout
            </button>
        @else
            <a href="{{ route('login') }}" class="pub-topbar-btn-login">Masuk</a>
        @endauth
    </div>
</header>

{{-- ══ MAIN CONTENT ══ --}}
<main class="pub-main">
    @yield('content')
</main>

{{-- ══ BOTTOM BAR (Mobile ≤ 767px) ══ --}}
<nav class="pub-bottombar" id="pubBottombar" role="navigation" aria-label="Menu navigasi">
    <div class="pub-bottombar-inner">

        {{-- Beranda --}}
        <a href="{{ route('index') }}" class="bbar-item {{ request()->routeIs('index') ? 'active' : '' }}"
           aria-label="Beranda">
            <div class="bbar-icon-wrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span>Beranda</span>
        </a>

        {{-- Kamar --}}
        <a href="{{ route('index') }}#kamar" class="bbar-item" aria-label="Kamar"
           onclick="document.getElementById('kamarSection')?.scrollIntoView({behavior:'smooth'}); return false;">
            <div class="bbar-icon-wrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <span>Kamar</span>
        </a>

        {{-- Booking / CTA --}}
        @auth
            <a href="#" class="bbar-item" id="bbarBookingBtn" aria-label="Pesan Kamar"
               onclick="document.querySelector('[data-search-form]')?.scrollIntoView({behavior:'smooth'}); return false;">
                <div class="bbar-icon-wrap"
                     style="background:var(--y);box-shadow:0 3px 10px rgba(234,179,8,0.4);width:46px;height:46px;border-radius:14px;">
                    <svg class="w-6 h-6" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span style="color:var(--ytext);font-weight:700;">Pesan</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="bbar-item" aria-label="Pesan Kamar">
                <div class="bbar-icon-wrap"
                     style="background:var(--y);box-shadow:0 3px 10px rgba(234,179,8,0.4);width:46px;height:46px;border-radius:14px;">
                    <svg class="w-6 h-6" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span style="color:var(--ytext);font-weight:700;">Pesan</span>
            </a>
        @endauth

        {{-- Info (trigger bottom sheet) --}}
        <button type="button"
                onclick="openInfoSheet()"
                class="bbar-item {{ request()->routeIs('about') || request()->routeIs('privacy-policy') || request()->routeIs('terms-conditions') ? 'active' : '' }}"
                aria-label="Info">
            <div class="bbar-icon-wrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span>Info</span>
        </button>

        {{-- Akun / Profil --}}
        @auth
            @php
                $__bbarAvatar = null;
                $__bbarProfile = \Modules\Profile\Models\ProfileUser::where('user_id', Auth::id())->first();
                if ($__bbarProfile?->foto) $__bbarAvatar = asset($__bbarProfile->foto);
                elseif (Auth::user()->avatar) $__bbarAvatar = Auth::user()->avatar;
                $__bbarIsVisitor = Auth::user()->role === 'visitor';
            @endphp
            @if($__bbarIsVisitor)
                {{-- Visitor: langsung ke halaman profil --}}
                <a href="{{ route('visitor.profile.index') }}"
                   class="bbar-item {{ request()->routeIs('visitor.profile.*') ? 'active' : '' }}"
                   aria-label="Profil Saya">
                    <div class="bbar-icon-wrap" id="bbarAccountIcon">
                        @if($__bbarAvatar)
                            <img src="{{ $__bbarAvatar }}" alt="avatar"
                                 style="width:28px;height:28px;border-radius:50%;object-fit:cover;
                                        border:2px solid {{ request()->routeIs('visitor.profile.*') ? '#eab308' : '#e2e8f0' }};">
                        @else
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--y);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:0.75rem;font-weight:700;color:var(--ytext);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <span>Profil</span>
                </a>
            @else
                {{-- Admin / non-visitor: buka menu akun --}}
                <button type="button" onclick="pubMobileMenu()" class="bbar-item" aria-label="Akun">
                    <div class="bbar-icon-wrap" id="bbarAccountIcon">
                        @if($__bbarAvatar)
                            <img src="{{ $__bbarAvatar }}" alt="avatar"
                                 style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                        @else
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--y);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:0.75rem;font-weight:700;color:var(--ytext);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <span>Akun</span>
                </button>
            @endif
        @else
            <a href="{{ route('login') }}" class="bbar-item" aria-label="Masuk">
                <div class="bbar-icon-wrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span>Masuk</span>
            </a>
        @endauth

    </div>
</nav>

{{-- ══ INFO BOTTOM SHEET (Mobile) ══ --}}
<div class="info-sheet-overlay" id="infoSheetOverlay" onclick="closeInfoSheet()"></div>
<div class="info-sheet" id="infoSheet" role="dialog" aria-modal="true" aria-label="Informasi">
    <div class="info-sheet-handle"></div>
    <div class="px-1 py-3">
        <p class="text-[0.68rem] font-bold text-slate-400 uppercase tracking-widest px-4 mb-1">Informasi</p>
        <a href="{{ route('about') }}" class="info-sheet-item" onclick="closeInfoSheet()">
            <div class="info-sheet-icon" style="background:#fef9c3;">
                <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-900 text-[0.875rem]">Tentang Kami</p>
                <p class="text-[0.72rem] text-slate-400 mt-0.5">Profil &amp; sejarah penginapan</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <a href="{{ route('privacy-policy') }}" class="info-sheet-item" onclick="closeInfoSheet()">
            <div class="info-sheet-icon" style="background:#fce7f3;">
                <svg class="w-4 h-4" style="color:#9d174d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-900 text-[0.875rem]">Kebijakan &amp; Privasi</p>
                <p class="text-[0.72rem] text-slate-400 mt-0.5">Perlindungan data tamu</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <a href="{{ route('terms-conditions') }}" class="info-sheet-item" onclick="closeInfoSheet()">
            <div class="info-sheet-icon" style="background:#ede9fe;">
                <svg class="w-4 h-4" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-900 text-[0.875rem]">Syarat &amp; Ketentuan</p>
                <p class="text-[0.72rem] text-slate-400 mt-0.5">Aturan penggunaan layanan</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

{{-- Logout form (shared) --}}
@auth
<form method="POST" action="{{ route('logout') }}" id="mobileLogoutForm">@csrf</form>
@endauth

@stack('scripts')
<script>
/* ── Info Dropdown (desktop) ── */
function toggleInfoDropdown() {
    const menu    = document.getElementById('infoDropMenu');
    const chevron = document.getElementById('infoDropChevron');
    const btn     = document.getElementById('infoDropBtn');
    const isHidden = menu.classList.toggle('hidden');
    chevron.style.transform = isHidden ? '' : 'rotate(180deg)';
    btn.setAttribute('aria-expanded', !isHidden);
}
// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const dd = document.getElementById('infoDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('infoDropMenu')?.classList.add('hidden');
        const chevron = document.getElementById('infoDropChevron');
        if (chevron) chevron.style.transform = '';
        document.getElementById('infoDropBtn')?.setAttribute('aria-expanded', 'false');
    }
});

/* ── Info Bottom Sheet (mobile) ── */
function openInfoSheet() {
    document.getElementById('infoSheetOverlay').classList.add('show');
    setTimeout(() => document.getElementById('infoSheet').classList.add('show'), 10);
    document.body.style.overflow = 'hidden';
}
function closeInfoSheet() {
    document.getElementById('infoSheet').classList.remove('show');
    document.getElementById('infoSheetOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

/* ── Info Dropdown (desktop) ── */
function toggleInfoDropdown() {
    const menu    = document.getElementById('infoDropMenu');
    const chevron = document.getElementById('infoDropChevron');
    const btn     = document.getElementById('infoDropBtn');
    const isHidden = menu.classList.toggle('hidden');
    chevron.style.transform = isHidden ? '' : 'rotate(180deg)';
    btn.setAttribute('aria-expanded', String(!isHidden));
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('infoDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('infoDropMenu')?.classList.add('hidden');
        const chevron = document.getElementById('infoDropChevron');
        if (chevron) chevron.style.transform = '';
        document.getElementById('infoDropBtn')?.setAttribute('aria-expanded', 'false');
    }
});

/* ── Info Bottom Sheet (mobile) ── */
function openInfoSheet() {
    document.getElementById('infoSheetOverlay').classList.add('show');
    setTimeout(() => document.getElementById('infoSheet').classList.add('show'), 10);
    document.body.style.overflow = 'hidden';
}
function closeInfoSheet() {
    document.getElementById('infoSheet').classList.remove('show');
    document.getElementById('infoSheetOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

function pubConfirmLogout() {
    Swal.fire({
        title: 'Keluar dari akun?', text: 'Sesi Anda akan diakhiri.',
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya, Logout', cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { confirmButton: 'swal-confirm-btn' },
        buttonsStyling: true,
    }).then(r => { if (r.isConfirmed) document.getElementById('topbarLogoutForm')?.submit() || document.getElementById('mobileLogoutForm')?.submit(); });
}
function pubMobileMenu() {
    Swal.fire({
        title: '{{ Auth::check() ? addslashes(Auth::user()->name) : "" }}',
        html: `<div style="display:flex;flex-direction:column;gap:10px;text-align:left;">
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:#f8fafc;color:#0f172a;text-decoration:none;font-weight:600;font-size:0.9rem;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    Dashboard Admin
                </a>
            @endif
            <button onclick="Swal.close(); setTimeout(pubConfirmLogout, 200);" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:#fef2f2;color:#dc2626;border:none;cursor:pointer;font-weight:600;font-size:0.9rem;width:100%;">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </div>`,
        showConfirmButton: false, showCloseButton: true,
    });
}
</script>
</body>
</html>
