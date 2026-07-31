<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ optional(\Modules\Profile\Models\ProfileHotel::first())->name ?? 'Penginapan' }}</title>
    {{-- Favicon dinamis --}}
    @if(file_exists(public_path('favicon.png')))
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23eab308'/><text x='16' y='22' text-anchor='middle' font-size='18' font-family='sans-serif' fill='%23713f12'>H</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        /* ─── Layout wrapper ─── */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ─── Panel kiri (branding) ─── */
        .login-left {
            flex: 0 0 50%;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 3.5rem;
            background: linear-gradient(160deg,#1a1500 0%,#2d2500 50%,#422d00 100%);
        }
        .deco-circle {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }
        .stat-pill {
            display: flex; align-items: center; gap: 0.75rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 1rem;
            padding: 0.875rem 1.25rem;
            backdrop-filter: blur(8px);
        }

        /* ─── Panel kanan (form) ─── */
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 3rem 2rem;
            min-height: 100vh;
        }
        .login-right-inner {
            width: 100%;
            max-width: 420px;
        }

        /* ─── Form inputs ─── */
        .field-input {
            width: 100%;
            padding: 0.72rem 1rem;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.9rem; color: #1e293b;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .field-input::placeholder { color: #94a3b8; }
        .field-input:focus {
            border-color: #eab308;
            box-shadow: 0 0 0 3px rgba(234,179,8,0.15);
            background: #fff;
        }
        .field-input.is-error { border-color: #ef4444; }

        /* ─── Tombol utama ─── */
        .btn-main {
            width: 100%; padding: 0.8rem 1rem;
            background: #eab308; color: #713f12;
            font-weight: 600; font-size: 0.95rem;
            border: none; border-radius: 0.75rem;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, color 0.15s;
        }
        .btn-main:hover  { background: #ca8a04; color: #fff; }
        .btn-main:active { transform: scale(0.98); }

        /* ─── Tombol Google ─── */
        .btn-google {
            width: 100%; display: flex; align-items: center;
            justify-content: center; gap: 0.625rem;
            padding: 0.75rem 1rem;
            background: #fff; color: #1e293b;
            font-weight: 600; font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem; text-decoration: none;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
        }
        .btn-google:hover {
            background: #f8fafc; border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* ─── Captcha refresh button ─── */
        .btn-captcha-refresh {
            width: 44px; flex-shrink: 0;
            border-radius: 0.75rem;
            border: 1.5px solid #e2e8f0; background: #f8fafc;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center;
            transition: border-color 0.15s, background 0.15s;
        }
        .btn-captcha-refresh:hover { border-color: #eab308; background: #fff; }

        /* ─── Animasi masuk panel kanan ─── */
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(16px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slideIn 0.4s ease both; }

        /* ══════════════════════════════════
           RESPONSIVE — Mobile & Tablet
        ══════════════════════════════════ */
        @media (max-width: 768px) {
            /* Sembunyikan panel kiri di mobile */
            .login-left { display: none; }

            .login-right {
                padding: 1.5rem 1.25rem;
                justify-content: flex-start;
                min-height: 100vh;
            }
            .login-right-inner { max-width: 100%; }

            /* Tampilkan topbar brand di mobile */
            .mobile-brand { display: flex !important; }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .login-left {
                flex: 0 0 42%;
                padding: 2.5rem 2rem;
            }
            .login-left h1 { font-size: 2rem !important; }
        }

        @media (min-width: 1025px) {
            .login-left { flex: 0 0 50%; }
        }
    </style>
</head>
<body>

@php
    $__hotel     = \Modules\Profile\Models\ProfileHotel::first();
    $__hotelName = $__hotel?->name ?: 'Penginapan';
    $__hotelLogo = ($__hotel?->logo && file_exists(public_path($__hotel->logo)))
                     ? asset($__hotel->logo)
                     : null;
@endphp

    {{-- ── Topbar brand (mobile only) ── --}}
    <div class="mobile-brand"
         style="display:none; position:sticky; top:0; left:0; right:0; z-index:50;
                align-items:center; gap:0.75rem; padding:1rem 1.25rem;
                background:#fff; border-bottom:1px solid #e2e8f0;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        @if($__hotelLogo)
            <img src="{{ $__hotelLogo }}" alt="Logo"
                 style="width:34px;height:34px;border-radius:9px;object-fit:contain;background:#fefce8;flex-shrink:0;">
        @else
            <div style="width:34px;height:34px;border-radius:9px;background:#eab308;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:18px;height:18px;color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        @endif
        <span style="font-size:1.1rem;font-weight:800;color:#0f172a;">{{ $__hotelName }}</span>
    </div>

    <div class="login-wrapper">

        {{-- ══ PANEL KIRI — Branding ══ --}}
        <div class="login-left" id="leftPanel">
            <div class="deco-circle" style="width:480px;height:480px;top:-180px;right:-130px;"></div>
            <div class="deco-circle" style="width:280px;height:280px;bottom:-80px;left:-80px;"></div>
            <div class="deco-circle" style="width:160px;height:160px;bottom:180px;right:60px;background:rgba(255,255,255,0.06);"></div>

            {{-- Logo + Nama Hotel — tengah vertikal, besar & jelas --}}
            <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;text-align:center;gap:1.75rem;">
                {{-- Logo --}}
                @if($__hotelLogo)
                    <img src="{{ $__hotelLogo }}" alt="{{ $__hotelName }}"
                         style="width:140px;height:140px;border-radius:2rem;object-fit:contain;
                                background:rgba(255,255,255,0.12);
                                border:2px solid rgba(255,255,255,0.2);
                                backdrop-filter:blur(4px);
                                box-shadow:0 8px 32px rgba(0,0,0,0.3);">
                @else
                    <div style="width:140px;height:140px;border-radius:2rem;
                                display:flex;align-items:center;justify-content:center;
                                background:rgba(255,255,255,0.15);
                                border:2px solid rgba(255,255,255,0.2);
                                backdrop-filter:blur(4px);
                                box-shadow:0 8px 32px rgba(0,0,0,0.3);">
                        <svg style="width:72px;height:72px;color:rgba(255,255,255,0.9);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                @endif

                {{-- Nama Hotel --}}
                <div>
                    <h1 style="font-size:2.6rem;font-weight:900;color:#fff;
                               letter-spacing:-0.02em;line-height:1.1;
                               text-shadow:0 2px 12px rgba(0,0,0,0.4);">
                        {{ $__hotelName }}
                    </h1>
                </div>

                {{-- Garis dekoratif --}}
                <div style="width:60px;height:3px;border-radius:2px;background:rgba(234,179,8,0.7);"></div>
            </div>

            {{-- Footer kiri --}}
            <div style="position:relative;z-index:1;text-align:center;">
                <p class="text-[0.75rem]" style="color:rgba(255,255,255,0.3);">
                    &copy; {{ date('Y') }} {{ $__hotelName }}. All rights reserved.
                </p>
            </div>
        </div>

        {{-- ══ PANEL KANAN — Form ══ --}}
        <div class="login-right" id="rightPanel">
            <div class="login-right-inner animate-slide-in">

                {{-- Header --}}
                <div class="mb-7">
                    <a href="{{ route('index') }}"
                       class="inline-flex items-center gap-2 text-[0.9rem] font-semibold text-slate-600 hover:text-slate-900 mb-4 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                    <h2 class="text-[1.6rem] font-extrabold text-slate-900 tracking-tight mb-1">
                        Masuk ke Akun
                    </h2>
                    <p class="text-[0.9rem] text-slate-500">Gunakan email atau login cepat via Google.</p>
                </div>

                {{-- SweetAlert per error field --}}
                @if($errors->any())
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if($errors->has('email'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Email Tidak Ditemukan',
                            text: @json($errors->first('email')),
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#eab308',
                            customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-semibold text-yellow-900' },
                        });
                    @elseif($errors->has('password'))
                        Swal.fire({
                            icon: 'warning',
                            title: 'Password Salah',
                            text: @json($errors->first('password')),
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#eab308',
                            customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-semibold text-yellow-900' },
                        });
                    @elseif($errors->has('captcha'))
                        Swal.fire({
                            icon: 'info',
                            title: 'Kode Verifikasi Salah',
                            text: @json($errors->first('captcha')),
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#eab308',
                            customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-semibold text-yellow-900' },
                        });
                    @else
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            html: '<ul class="text-left text-sm text-slate-600 space-y-1">'
                                  + @json($errors->all()).map(function(e){ return '<li>• ' + e + '</li>'; }).join('')
                                  + '</ul>',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#eab308',
                        });
                    @endif
                });
                </script>
                @endif

                {{-- Session status --}}
                @if (session('status'))
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success', title: 'Info',
                        text: @json(session('status')),
                        timer: 4000, timerProgressBar: true,
                        showConfirmButton: false, toast: true, position: 'top-end',
                    });
                });
                </script>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-[0.85rem] font-semibold text-slate-700 mb-1.5">
                            Alamat Email
                        </label>
                        <input id="email" type="email" name="email"
                            value="{{ old('email') }}"
                            required autofocus autocomplete="username"
                            placeholder="email@contoh.com"
                            class="field-input {{ $errors->has('email') ? 'is-error' : '' }}">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-[0.85rem] font-semibold text-slate-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                required autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                                style="padding-right:3rem;">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2
                                       bg-transparent border-none cursor-pointer text-slate-400 p-0"
                                aria-label="Tampilkan password">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 rounded cursor-pointer"
                            style="accent-color:#eab308;">
                        <label for="remember" class="text-[0.85rem] text-slate-500 cursor-pointer">
                            Ingat saya selama 30 hari
                        </label>
                    </div>

                    {{-- CAPTCHA --}}
                    <div>
                        <label for="captcha" class="block text-[0.85rem] font-semibold text-slate-700 mb-1.5">
                            Kode Verifikasi
                        </label>
                        <div class="flex gap-2.5 items-stretch">

                            {{-- Kotak kode --}}
                            <div class="flex items-center justify-center relative overflow-hidden shrink-0 rounded-xl select-none"
                                 style="min-width:110px;padding:0 1rem;height:50px;
                                        background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);
                                        border:1.5px solid #e2e8f0;">
                                <svg class="absolute inset-0 w-full h-full opacity-[0.12]"
                                     preserveAspectRatio="none" viewBox="0 0 110 48">
                                    <line x1="0"  y1="38" x2="110" y2="10" stroke="white" stroke-width="1.2"/>
                                    <line x1="0"  y1="15" x2="110" y2="42" stroke="white" stroke-width="0.8"/>
                                    <line x1="20" y1="0"  x2="40"  y2="48" stroke="white" stroke-width="0.7"/>
                                    <line x1="70" y1="0"  x2="90"  y2="48" stroke="white" stroke-width="0.7"/>
                                </svg>
                                <span id="captchaText"
                                      class="relative z-10"
                                      style="font-family:'SF Mono','Fira Code','Courier New',monospace;
                                             font-size:1.4rem;font-weight:800;letter-spacing:0.25em;
                                             color:#fff;text-shadow:0 1px 4px rgba(0,0,0,0.5);
                                             display:block;transform:skewX(-4deg);">
                                    {{ session('login_captcha', '----') }}
                                </span>
                            </div>

                            {{-- Input + tombol refresh --}}
                            <div class="flex-1 flex flex-col gap-1.5">
                                <div class="flex gap-1.5">
                                    <input id="captcha" name="captcha"
                                        type="text" maxlength="4"
                                        autocomplete="off" spellcheck="false"
                                        placeholder="Ketik kode"
                                        class="field-input flex-1 uppercase {{ $errors->has('captcha') ? 'is-error' : '' }}"
                                        style="font-family:'SF Mono','Fira Code',monospace;
                                               font-weight:700;letter-spacing:0.18em;"
                                        onfocus="this.style.borderColor='#eab308';this.style.boxShadow='0 0 0 3px rgba(234,179,8,0.15)'"
                                        onblur="this.style.borderColor='{{ $errors->has('captcha') ? '#ef4444' : '#e2e8f0' }}';this.style.boxShadow='none'"
                                        oninput="this.value=this.value.toUpperCase()">
                                    <button type="button" onclick="refreshCaptcha()"
                                        id="captchaRefreshBtn" title="Ganti kode"
                                        class="btn-captcha-refresh">
                                        <svg id="refreshIcon" class="w-4 h-4 text-slate-400"
                                             style="transition:transform 0.45s ease;"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-[0.72rem] text-slate-400">
                                    Tidak bisa dibaca?
                                    <button type="button" onclick="refreshCaptcha()"
                                        class="bg-transparent border-none cursor-pointer
                                               text-[0.72rem] font-semibold underline p-0"
                                        style="color:#eab308;">
                                        Ganti kode
                                    </button>
                                </p>
                            </div>
                        </div>
                        @error('captcha')
                            <p class="text-[0.78rem] text-red-600 mt-1.5">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-main mt-1">Masuk Sekarang</button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-6">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-[0.8rem] text-slate-400 whitespace-nowrap">atau lanjutkan dengan</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                {{-- Google --}}
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                {{-- Demo credentials --}}
                <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <p class="text-[0.78rem] font-semibold text-slate-600 mb-2">🔑 Demo akun Admin:</p>
                    <p class="text-[0.78rem] text-slate-500 mb-1">
                        <strong>admin@penginapan.com</strong> / admin123
                    </p>
                    <p class="text-[0.78rem] text-slate-500">
                        <strong>manager@penginapan.com</strong> / manager123
                    </p>
                </div>

                {{-- Footer copyright (mobile) --}}
                <p class="text-center text-[0.73rem] text-slate-400 mt-8 md:hidden">
                    &copy; {{ date('Y') }} Penginapan. All rights reserved.
                </p>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var inp = document.getElementById('password');
            inp.type = inp.type === 'password' ? 'text' : 'password';
        }

        function refreshCaptcha() {
            var icon  = document.getElementById('refreshIcon');
            var btn   = document.getElementById('captchaRefreshBtn');
            var box   = document.getElementById('captchaText');
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            icon.style.transition = 'transform 0.45s ease';
            icon.style.transform  = 'rotate(360deg)';
            btn.disabled = true;

            fetch('{{ route('captcha.refresh') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     token
                },
                body: JSON.stringify({})
            })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(data) {
                if (!data.captcha) throw new Error('empty');
                box.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                box.style.opacity    = '0';
                box.style.transform  = 'skewX(-4deg) scale(0.8)';
                setTimeout(function() {
                    box.textContent     = data.captcha;
                    box.style.opacity   = '1';
                    box.style.transform = 'skewX(-4deg) scale(1)';
                }, 160);
                var input = document.getElementById('captcha');
                if (input) { input.value = ''; input.focus(); }
            })
            .catch(function() { window.location.reload(); })
            .finally(function() {
                setTimeout(function() {
                    icon.style.transform = 'rotate(0deg)';
                    btn.disabled = false;
                }, 460);
            });
        }
    </script>
</body>
</html>
