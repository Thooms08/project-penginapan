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
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 50;
            height: 68px;
            background: rgba(255,255,255,0.97);
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.08);
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
            @endphp
            <div class="flex items-center gap-2.5">
                @if($__pubAvatar)
                    <img src="{{ $__pubAvatar }}" alt="avatar" class="pub-topbar-avatar">
                @else
                    <div class="pub-topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
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

        {{-- Tentang --}}
        <a href="#tentang" class="bbar-item" aria-label="Tentang"
           onclick="document.getElementById('tentangSection')?.scrollIntoView({behavior:'smooth'}); return false;">
            <div class="bbar-icon-wrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span>Info</span>
        </a>

        {{-- Akun --}}
        @auth
            <button type="button" onclick="pubMobileMenu()" class="bbar-item" aria-label="Akun">
                <div class="bbar-icon-wrap" id="bbarAccountIcon">
                    @php
                        $__mobileAvatar = null;
                        if (isset($__pubAvatar)) { $__mobileAvatar = $__pubAvatar; }
                        elseif (Auth::check()) {
                            $__mp = \Modules\Profile\Models\ProfileUser::where('user_id', Auth::id())->first();
                            if ($__mp?->foto) $__mobileAvatar = asset($__mp->foto);
                            elseif (Auth::user()->avatar) $__mobileAvatar = Auth::user()->avatar;
                        }
                    @endphp
                    @if($__mobileAvatar)
                        <img src="{{ $__mobileAvatar }}" alt="avatar"
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

{{-- Logout form (shared) --}}
@auth
<form method="POST" action="{{ route('logout') }}" id="mobileLogoutForm">@csrf</form>
@endauth

@stack('scripts')
<script>
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
