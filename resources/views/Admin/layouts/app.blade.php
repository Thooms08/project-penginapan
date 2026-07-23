<!DOCTYPE html>
<html lang="id" data-theme="indigo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Penginapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f1f5f9; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            background-color: var(--sidebar-bg);
            z-index: 40;
            transition: transform 0.25s ease;
        }
        .sidebar-border-b { border-bottom: 1px solid var(--sidebar-border); }
        .sidebar-border-t { border-top:    1px solid var(--sidebar-border); }

        /* Nav active item */
        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.875rem; font-weight: 500;
            color: var(--sidebar-text-muted);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .nav-item:hover { background: var(--sidebar-hover); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: var(--sidebar-active); color: var(--on-primary, #fff); }

        /* ── Topbar ── */
        .topbar {
            margin-left: 260px;
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky; top: 0; z-index: 30;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        /* ── Main content ── */
        .main-content { margin-left: 260px; padding: 2rem 2rem; min-height: calc(100vh - 64px); }

        /* ── Theme Picker Modal ── */
        .theme-modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 100;
            background: rgba(15,23,42,0.55);
            backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
            padding: 1rem;
        }
        .theme-modal-overlay.open { display: flex; }
        .theme-modal {
            background: #fff;
            border-radius: 1.5rem;
            width: 100%; max-width: 480px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.25);
            animation: popIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
            overflow: hidden;
        }
        @keyframes popIn {
            from { opacity:0; transform:scale(0.88) translateY(16px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }

        /* ── Color picker canvas area ── */
        #cpCanvas {
            width: 100%; height: 220px;
            display: block; cursor: crosshair;
            border-radius: 0.75rem;
        }
        /* Hue slider */
        .hue-track {
            width: 100%; height: 14px; border-radius: 999px;
            background: linear-gradient(to right,
                #ff0000,#ffff00,#00ff00,#00ffff,#0000ff,#ff00ff,#ff0000);
            position: relative; cursor: pointer;
        }
        /* Opacity slider */
        .opacity-track {
            width: 100%; height: 14px; border-radius: 999px;
            position: relative; cursor: pointer;
            background-image:
                linear-gradient(45deg,#ccc 25%,transparent 25%),
                linear-gradient(-45deg,#ccc 25%,transparent 25%),
                linear-gradient(45deg,transparent 75%,#ccc 75%),
                linear-gradient(-45deg,transparent 75%,#ccc 75%);
            background-size: 8px 8px;
            background-position: 0 0,0 4px,4px -4px,-4px 0;
        }
        .opacity-color-overlay {
            position: absolute; inset: 0; border-radius: 999px;
        }
        /* Slider thumb */
        .slider-thumb {
            position: absolute; top: 50%; transform: translate(-50%,-50%);
            width: 20px; height: 20px;
            border-radius: 50%; border: 3px solid #fff;
            box-shadow: 0 1px 6px rgba(0,0,0,0.35);
            pointer-events: none;
        }
        /* Preset swatches */
        .preset-dot {
            width: 28px; height: 28px; border-radius: 50%;
            cursor: pointer; border: 2.5px solid transparent;
            transition: transform 0.12s, border-color 0.12s;
            flex-shrink: 0;
        }
        .preset-dot:hover { transform: scale(1.18); }
        .preset-dot.active { border-color: #0f172a; transform: scale(1.1); }
        /* Hex input */
        .hex-input {
            font-family: 'SF Mono','Fira Code',monospace;
            font-size: 0.82rem; font-weight: 600;
            border: 1.5px solid #e2e8f0; border-radius: 0.5rem;
            padding: 0.45rem 0.6rem; width: 100px;
            color: #0f172a; outline: none; letter-spacing: 0.04em;
            text-transform: uppercase;
            transition: border-color 0.15s;
        }
        .hex-input:focus { border-color: var(--color-primary); }
        .rgb-input {
            font-size: 0.78rem; font-weight: 600;
            border: 1.5px solid #e2e8f0; border-radius: 0.5rem;
            padding: 0.4rem 0.45rem; width: 52px; text-align:center;
            color: #0f172a; outline: none;
            transition: border-color 0.15s;
        }
        .rgb-input:focus { border-color: var(--color-primary); }

        /* ── Mobile overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 35;
            background: rgba(0,0,0,0.4);
        }
        .sidebar-overlay.open { display: block; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

{{-- ══════════════ SIDEBAR ══════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-border-b" style="padding:1.25rem 1.25rem; display:flex; align-items:center; gap:0.875rem;">
        <div style="width:40px;height:40px;border-radius:11px;
                    background:var(--sidebar-active); flex-shrink:0;
                    display:flex;align-items:center;justify-content:center;">
            <svg style="width:22px;height:22px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p style="font-size:0.95rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">Penginapan</p>
            <p style="font-size:0.72rem;color:var(--sidebar-text-muted);margin:0;line-height:1.4;">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1; padding:1rem 0.75rem; overflow-y:auto;">
        <p style="font-size:0.68rem;font-weight:700;color:var(--sidebar-text-muted);
                   letter-spacing:0.08em;text-transform:uppercase;margin:0 0.5rem 0.5rem;padding-left:0.4rem;">
            Menu
        </p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zM13 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>

        {{-- Placeholder nav items --}}
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

        <div style="margin: 1rem 0 0.5rem; border-top: 1px solid var(--sidebar-border); padding-top:1rem;">
            <p style="font-size:0.68rem;font-weight:700;color:var(--sidebar-text-muted);
                       letter-spacing:0.08em;text-transform:uppercase;margin:0 0.5rem 0.5rem;padding-left:0.4rem;">
                Pengaturan
            </p>
            {{-- Tombol ubah tema --}}
            <button onclick="openThemePicker()"
                class="nav-item" style="width:100%;border:none;cursor:pointer;text-align:left;">
                <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Ubah Tema Warna
            </button>
        </div>
    </nav>

    {{-- User + Logout --}}
    <div class="sidebar-border-t" style="padding:1rem 0.75rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.625rem;
                    padding:0.5rem 0.375rem;">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="avatar"
                    style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:var(--sidebar-active);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="color:#fff;font-size:0.8rem;font-weight:700;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            <div style="min-width:0;">
                <p style="font-size:0.82rem;font-weight:600;color:#fff;margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Auth::user()->name }}
                </p>
                <p style="font-size:0.72rem;color:var(--sidebar-text-muted);margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;border:none;cursor:pointer;text-align:left;">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ══════════════ TOPBAR ══════════════ --}}
<div class="topbar">
    <div style="display:flex;align-items:center;gap:0.875rem;">
        {{-- Hamburger (tablet/mobile) --}}
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
        {{-- Tema quick-change button di topbar --}}
        <button onclick="openThemePicker()"
            style="display:flex;align-items:center;gap:0.5rem;
                   padding:0.45rem 0.875rem; border-radius:0.625rem;
                   border:1.5px solid #e2e8f0; background:#f8fafc;
                   font-size:0.8rem;font-weight:600;color:#475569;cursor:pointer;
                   transition:border-color 0.15s, background 0.15s;"
            onmouseover="this.style.borderColor='var(--color-primary)';this.style.color='var(--color-primary)'"
            onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
            <span class="hide-xs">Tema</span>
        </button>

        {{-- Avatar --}}
        <div style="display:flex;align-items:center;gap:0.625rem;">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="avatar"
                    style="width:34px;height:34px;border-radius:50%;object-fit:cover;
                           border:2px solid var(--color-primary-100);">
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:var(--color-primary);
                             display:flex;align-items:center;justify-content:center;">
                    <span style="color:#fff;font-size:0.8rem;font-weight:700;">
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
</div>

{{-- ══════════════ MAIN CONTENT ══════════════ --}}
<main class="main-content">
    @yield('content')
</main>

{{-- ══════════════ THEME PICKER MODAL ══════════════ --}}
<div class="theme-modal-overlay" id="themeModalOverlay">
    <div class="theme-modal" id="themeModal">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:1.25rem 1.5rem 0;">
            <div>
                <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0;">Pilih Warna Tema</h3>
                <p style="font-size:0.78rem;color:#94a3b8;margin:0.2rem 0 0;">Preview real-time · Tersimpan otomatis</p>
            </div>
            <button onclick="CP.close()"
                style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;
                       background:#f8fafc;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:15px;height:15px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.1rem 1.5rem 0;">

            {{-- SV Canvas --}}
            <div style="position:relative;border-radius:0.875rem;overflow:hidden;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.08);">
                <canvas id="cpCanvas"></canvas>
                <div id="cpCursor"
                     style="position:absolute;width:18px;height:18px;border-radius:50%;
                            border:3px solid #fff;box-shadow:0 1px 6px rgba(0,0,0,0.4);
                            pointer-events:none;transform:translate(-50%,-50%);top:10%;left:90%;"></div>
            </div>

            {{-- Hue Slider --}}
            <div style="margin-top:0.875rem;">
                <div class="hue-track" id="hueTrack">
                    <div class="slider-thumb" id="hueThumb" style="background:#4f46e5;left:66.7%;"></div>
                </div>
            </div>

            {{-- Opacity Slider --}}
            <div style="margin-top:0.625rem;">
                <div class="opacity-track" id="opacityTrack">
                    <div class="opacity-color-overlay" id="opacityOverlay"></div>
                    <div class="slider-thumb" id="opacityThumb" style="background:rgba(0,0,0,1);left:100%;"></div>
                </div>
            </div>

            {{-- Preview + Hex + RGB --}}
            <div style="display:flex;align-items:center;gap:0.75rem;margin-top:0.875rem;flex-wrap:wrap;">
                <div style="display:flex;gap:0.375rem;flex-shrink:0;">
                    <div id="cpPreviewNew"
                         style="width:40px;height:40px;border-radius:8px;border:1px solid rgba(0,0,0,0.08);
                                box-shadow:0 2px 6px rgba(0,0,0,0.1);background:#4f46e5;"></div>
                    <div id="cpPreviewOld"
                         style="width:40px;height:40px;border-radius:8px;border:1px solid rgba(0,0,0,0.08);opacity:0.5;"></div>
                </div>
                <div style="display:flex;align-items:center;gap:0.375rem;flex-shrink:0;">
                    <span style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">HEX</span>
                    <div style="display:flex;align-items:center;border:1.5px solid #e2e8f0;border-radius:0.5rem;overflow:hidden;">
                        <span style="padding:0 0.4rem;color:#94a3b8;font-size:0.82rem;font-weight:700;">#</span>
                        <input id="cpHex" class="hex-input"
                               style="border:none;padding-left:0;border-radius:0;width:78px;"
                               maxlength="6" spellcheck="false" value="4F46E5">
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:0.3rem;">
                    <input id="cpR" class="rgb-input" type="number" min="0" max="255" placeholder="R">
                    <input id="cpG" class="rgb-input" type="number" min="0" max="255" placeholder="G">
                    <input id="cpB" class="rgb-input" type="number" min="0" max="255" placeholder="B">
                </div>
            </div>

            {{-- Preset palette --}}
            <div style="margin-top:1rem;">
                <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;
                           text-transform:uppercase;letter-spacing:0.06em;margin:0 0 0.6rem;">Preset Cepat</p>
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;" id="presetRow"></div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:1.1rem 1.5rem 1.25rem;margin-top:0.75rem;border-top:1px solid #f1f5f9;">
            <button onclick="CP.reset()"
                style="font-size:0.82rem;font-weight:600;color:#94a3b8;background:none;border:none;cursor:pointer;padding:0.4rem 0;">
                ↩ Reset Default
            </button>
            <div style="display:flex;gap:0.5rem;">
                <button onclick="CP.close()"
                    style="padding:0.6rem 1.1rem;border-radius:0.625rem;border:1.5px solid #e2e8f0;
                           background:#f8fafc;font-size:0.85rem;font-weight:600;color:#64748b;cursor:pointer;">
                    Batal
                </button>
                <button onclick="CP.apply()"
                    style="padding:0.6rem 1.25rem;border-radius:0.625rem;border:none;
                           background:var(--btn-primary-bg);color:#fff;
                           font-size:0.85rem;font-weight:600;cursor:pointer;">
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width:1024px) { #hamburgerBtn { display:flex !important; } }
    @media (max-width:640px)  { .main-content { padding: 1.25rem 1rem; } .hide-sm { display:none; } }
    @media (max-width:480px)  { .hide-xs { display:none; } }
</style>

<script>
/* ═══════════════════════════════════════════════════════════════
   COLOR PICKER ENGINE — Pure JS, no library
   SV canvas · Hue slider · Opacity slider
   Hex/RGB input · Preset swatches · Live preview
   Auto-generate full CSS vars from one hex
═══════════════════════════════════════════════════════════════ */
const CP = (() => {

    /* ── State ── */
    let h=240, s=0.7, v=0.9, a=1;
    let savedHex  = localStorage.getItem('penginapan_color') || '#4f46e5';
    let currentHex = savedHex;
    let dragging  = null;

    const PRESETS = [
        '#4f46e5','#7c3aed','#6366f1','#0284c7','#0891b2','#06b6d4',
        '#059669','#16a34a','#84cc16','#ca8a04','#d97706','#f97316',
        '#dc2626','#e11d48','#db2777','#9333ea','#475569','#0f172a',
    ];

    /* ── DOM helpers ── */
    const $  = id => document.getElementById(id);
    const cv = () => $('cpCanvas');

    /* ── Color math ── */
    function hsvToRgb(h,s,v){
        let r,g,b,i=Math.floor(h/60),f=h/60-i,
            p=v*(1-s),q=v*(1-f*s),t=v*(1-(1-f)*s);
        switch(i%6){case 0:r=v;g=t;b=p;break;case 1:r=q;g=v;b=p;break;
            case 2:r=p;g=v;b=t;break;case 3:r=p;g=q;b=v;break;
            case 4:r=t;g=p;b=v;break;case 5:r=v;g=p;b=q;break;}
        return[Math.round(r*255),Math.round(g*255),Math.round(b*255)];
    }
    function rgbToHsv(r,g,b){
        r/=255;g/=255;b/=255;
        const mx=Math.max(r,g,b),mn=Math.min(r,g,b),d=mx-mn;
        let hh=0,ss=mx===0?0:d/mx,vv=mx;
        if(d>0){
            if(mx===r) hh=((g-b)/d)%6;
            else if(mx===g) hh=(b-r)/d+2;
            else hh=(r-g)/d+4;
            hh=Math.round(hh*60);if(hh<0)hh+=360;
        }
        return[hh,ss,vv];
    }
    function toHex(r,g,b){return'#'+[r,g,b].map(x=>x.toString(16).padStart(2,'0')).join('');}
    function fromHex(hex){
        hex=hex.replace('#','');
        if(hex.length===3)hex=hex.split('').map(c=>c+c).join('');
        return[parseInt(hex.slice(0,2),16),parseInt(hex.slice(2,4),16),parseInt(hex.slice(4,6),16)];
    }
    function lighten(r,g,b,t){return[Math.min(255,r+Math.round((255-r)*t)),Math.min(255,g+Math.round((255-g)*t)),Math.min(255,b+Math.round((255-b)*t))];}
    function darken(r,g,b,t){return[Math.round(r*(1-t)),Math.round(g*(1-t)),Math.round(b*(1-t))];}
    // Perceived luminance 0-255 (WCAG-accurate sRGB)
    function luma(r,g,b){return 0.2126*r+0.7152*g+0.0722*b;}
    // HSL (h:0-360, s:0-100, l:0-100) → [r,g,b] 0-255
    function hslToRgb(h,s,l){
        s/=100;l/=100;
        const k=n=>(n+h/30)%12;
        const a=s*Math.min(l,1-l);
        const f=n=>l-a*Math.max(-1,Math.min(k(n)-3,Math.min(9-k(n),1)));
        return[Math.round(f(0)*255),Math.round(f(8)*255),Math.round(f(4)*255)];
    }

    /* ── Generate full CSS vars ── */
    function genVars(hex){
        const [r,g,b]=fromHex(hex);

        // Warna turunan
        const dark=darken(r,g,b,0.18);
        const light=lighten(r,g,b,0.1);
        const v50=lighten(r,g,b,0.93);
        const v100=lighten(r,g,b,0.85);

        // ── Sidebar: SELALU gelap ──────────────────────────────
        // Ambil hue dari warna primer, paksa value sangat rendah
        // sehingga sidebar selalu cukup gelap untuk teks putih
        const [hh,,]= rgbToHsv(r,g,b);
        // Buat warna hsl dari hue primer, saturasi 50%, lightness 10-16%
        // ini yang paling konsisten: gelap dengan nuansa warna primer
        const sideL   = 12;  // target lightness% sidebar bg
        const sideHS  = hslToRgb(hh, 45, sideL);
        const sideBg  = `rgb(${sideHS[0]},${sideHS[1]},${sideHS[2]})`;

        // Active item bg: warna primer itu sendiri (cukup kontras di sidebar gelap)
        const activeL  = luma(r,g,b);

        // Text di sidebar: selalu putih (sidebar selalu gelap)
        const sideTextMuted = 'rgba(255,255,255,0.55)';
        const sideHover     = 'rgba(255,255,255,0.08)';

        // ── On-primary: teks DI ATAS warna primer ────────────
        // Jika luminance > 140 → teks hitam, jika < 140 → teks putih
        const onPrimary = luma(r,g,b) > 140 ? '#0f172a' : '#ffffff';

        // ── On-primary-dark (teks di atas dark variant) ───────
        const onPrimaryDark = luma(...dark) > 140 ? '#0f172a' : '#ffffff';

        // ── Badge: bg terang, text dari primer yang cukup gelap ─
        const badgeText = luma(...v100) > 140
            ? toHex(...darken(r,g,b,0.35))
            : toHex(...lighten(r,g,b,0.4));

        // ── Login gradient: selalu gelap ──────────────────────
        const lT=hslToRgb(hh,50,8);
        const lM=hslToRgb(hh,45,18);
        const lB=hslToRgb(hh,40,28);

        return{
            '--color-primary':         hex,
            '--color-primary-dark':    toHex(...dark),
            '--color-primary-light':   toHex(...light),
            '--color-primary-50':      toHex(...v50),
            '--color-primary-100':     toHex(...v100),
            '--color-primary-text':    badgeText,
            '--color-primary-rgb':     `${r},${g},${b}`,
            '--on-primary':            onPrimary,
            '--on-primary-dark':       onPrimaryDark,
            '--sidebar-bg':            sideBg,
            '--sidebar-border':        'rgba(255,255,255,0.08)',
            '--sidebar-active':        hex,
            '--sidebar-hover':         sideHover,
            '--sidebar-text-muted':    sideTextMuted,
            '--btn-primary-bg':        hex,
            '--btn-primary-hover':     toHex(...dark),
            '--badge-bg':              toHex(...v100),
            '--badge-text':            badgeText,
            '--accent-gradient':       `linear-gradient(135deg,${hex} 0%,${toHex(...light)} 100%)`,
            '--login-panel-bg':        `linear-gradient(160deg,rgb(${lT.join(',')}) 0%,rgb(${lM.join(',')}) 50%,rgb(${lB.join(',')}) 100%)`,
        };
    }

    /* ── Apply vars to root ── */
    function applyVars(hex){
        const root=document.documentElement;
        root.removeAttribute('data-theme');
        Object.entries(genVars(hex)).forEach(([k,v])=>root.style.setProperty(k,v));
        currentHex=hex;
        $('cpPreviewNew').style.background=hex;
    }

    /* ── Draw SV canvas ── */
    function drawCanvas(){
        const el=cv(); if(!el)return;
        const W=el.offsetWidth||440,H=el.offsetHeight||220;
        el.width=W;el.height=H;
        const ctx=el.getContext('2d');
        const[hr,hg,hb]=hsvToRgb(h,1,1);
        const gH=ctx.createLinearGradient(0,0,W,0);
        gH.addColorStop(0,'#fff');gH.addColorStop(1,`rgb(${hr},${hg},${hb})`);
        ctx.fillStyle=gH;ctx.fillRect(0,0,W,H);
        const gV=ctx.createLinearGradient(0,0,0,H);
        gV.addColorStop(0,'rgba(0,0,0,0)');gV.addColorStop(1,'rgba(0,0,0,1)');
        ctx.fillStyle=gV;ctx.fillRect(0,0,W,H);
    }

    /* ── Sync all UI ── */
    function syncUI(){
        const[r,g,b]=hsvToRgb(h,s,v);
        const hex=toHex(r,g,b);
        $('cpHex').value=hex.replace('#','').toUpperCase();
        $('cpR').value=r;$('cpG').value=g;$('cpB').value=b;
        const el=cv();
        if(el){
            $('cpCursor').style.left=(s*el.width)+'px';
            $('cpCursor').style.top=((1-v)*el.height)+'px';
            $('cpCursor').style.background=hex;
        }
        $('hueThumb').style.left=(h/360*100)+'%';
        $('hueThumb').style.background=`hsl(${h},100%,50%)`;
        $('opacityThumb').style.left=(a*100)+'%';
        $('opacityThumb').style.background=`rgba(${r},${g},${b},${a})`;
        $('opacityOverlay').style.background=`linear-gradient(to right,rgba(${r},${g},${b},0),rgb(${r},${g},${b}))`;
        applyVars(hex);
        document.querySelectorAll('.preset-dot').forEach(d=>{
            d.classList.toggle('active',d.dataset.color===hex.toLowerCase());
        });
    }

    /* ── Canvas pick ── */
    function canvasPick(e){
        const el=cv();const rect=el.getBoundingClientRect();
        const cx=e.touches?e.touches[0].clientX:e.clientX;
        const cy=e.touches?e.touches[0].clientY:e.clientY;
        s=Math.max(0,Math.min(1,(cx-rect.left)/rect.width));
        v=Math.max(0,Math.min(1,1-(cy-rect.top)/rect.height));
        syncUI();
    }
    function huePick(e){
        const rect=$('hueTrack').getBoundingClientRect();
        const cx=e.touches?e.touches[0].clientX:e.clientX;
        h=Math.max(0,Math.min(360,((cx-rect.left)/rect.width)*360));
        drawCanvas();syncUI();
    }
    function opPick(e){
        const rect=$('opacityTrack').getBoundingClientRect();
        const cx=e.touches?e.touches[0].clientX:e.clientX;
        a=Math.max(0,Math.min(1,(cx-rect.left)/rect.width));
        syncUI();
    }

    /* ── From hex string ── */
    function setHex(hex){
        if(!/^#?[0-9a-fA-F]{6}$/.test(hex))return;
        if(!hex.startsWith('#'))hex='#'+hex;
        const[r,g,b]=fromHex(hex);
        [h,s,v]=rgbToHsv(r,g,b);a=1;
        drawCanvas();syncUI();
    }

    /* ── Build presets ── */
    function buildPresets(){
        const row=$('presetRow');row.innerHTML='';
        PRESETS.forEach(px=>{
            const d=document.createElement('div');
            d.className='preset-dot'+(px.toLowerCase()===currentHex.toLowerCase()?' active':'');
            d.dataset.color=px.toLowerCase();
            d.style.background=px;d.title=px;
            d.onclick=()=>setHex(px);
            row.appendChild(d);
        });
    }

    /* ── Bind events once ── */
    let bound=false;
    function bindEvents(){
        if(bound)return;bound=true;
        cv().addEventListener('mousedown',e=>{dragging='c';canvasPick(e);});
        cv().addEventListener('touchstart',e=>{dragging='c';canvasPick(e);},{passive:true});
        $('hueTrack').addEventListener('mousedown',e=>{dragging='h';huePick(e);});
        $('hueTrack').addEventListener('touchstart',e=>{dragging='h';huePick(e);},{passive:true});
        $('opacityTrack').addEventListener('mousedown',e=>{dragging='o';opPick(e);});
        $('opacityTrack').addEventListener('touchstart',e=>{dragging='o';opPick(e);},{passive:true});
        document.addEventListener('mousemove',e=>{
            if(dragging==='c')canvasPick(e);
            else if(dragging==='h')huePick(e);
            else if(dragging==='o')opPick(e);
        });
        document.addEventListener('touchmove',e=>{
            if(dragging==='c')canvasPick(e);
            else if(dragging==='h')huePick(e);
            else if(dragging==='o')opPick(e);
        },{passive:true});
        document.addEventListener('mouseup',()=>dragging=null);
        document.addEventListener('touchend',()=>dragging=null);
        $('cpHex').addEventListener('input',e=>{
            const val=e.target.value.replace(/[^0-9a-fA-F]/g,'').slice(0,6);
            e.target.value=val;
            if(val.length===6)setHex('#'+val);
        });
        $('cpHex').addEventListener('keydown',e=>{if(e.key==='Enter')setHex('#'+$('cpHex').value);});
        [$('cpR'),$('cpG'),$('cpB')].forEach(inp=>{
            inp.addEventListener('change',()=>{
                setHex(toHex(
                    Math.min(255,Math.max(0,parseInt($('cpR').value)||0)),
                    Math.min(255,Math.max(0,parseInt($('cpG').value)||0)),
                    Math.min(255,Math.max(0,parseInt($('cpB').value)||0))
                ));
            });
        });
        $('themeModalOverlay').addEventListener('click',e=>{
            if(e.target===$('themeModalOverlay'))CP.close();
        });
    }

    /* ── Public API ── */
    return{
        open(){
            $('cpPreviewOld').style.background=savedHex;
            setHex(savedHex);buildPresets();
            $('themeModalOverlay').classList.add('open');
            document.body.style.overflow='hidden';
            requestAnimationFrame(()=>{drawCanvas();syncUI();bindEvents();});
        },
        close(){
            setHex(savedHex);
            const root=document.documentElement;
            Object.entries(genVars(savedHex)).forEach(([k,v])=>root.style.setProperty(k,v));
            $('themeModalOverlay').classList.remove('open');
            document.body.style.overflow='';
        },
        apply(){
            savedHex=currentHex;
            localStorage.setItem('penginapan_color',savedHex);
            $('themeModalOverlay').classList.remove('open');
            document.body.style.overflow='';
        },
        reset(){
            savedHex='#4f46e5';
            localStorage.setItem('penginapan_color',savedHex);
            setHex(savedHex);
        },
        init(){
            const saved=localStorage.getItem('penginapan_color');
            if(saved){
                const root=document.documentElement;
                root.removeAttribute('data-theme');
                Object.entries(genVars(saved)).forEach(([k,v])=>root.style.setProperty(k,v));
                savedHex=saved;currentHex=saved;
            }
        }
    };
})();

CP.init();

/* ── Shim for existing onclick="openThemePicker()" ── */
function openThemePicker(){CP.open();}

/* ── Sidebar mobile ── */
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
</script>

</body>
</html>
