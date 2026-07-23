<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Penginapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; }

        /* Panel kiri – branding, warna amber/kuning */
        .login-panel-left {
            background: linear-gradient(160deg,#1a1500 0%,#2d2500 50%,#422d00 100%);
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi bulat transparan di background */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        /* Input styling */
        .field-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            color: #1e293b;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .field-input:focus {
            border-color: #eab308;
            box-shadow: 0 0 0 3px rgba(234,179,8,0.15);
            background: #fff;
        }
        .field-input::placeholder { color: #94a3b8; }
        .field-input.is-error { border-color: #ef4444; }

        .btn-main {
            width: 100%;
            padding: 0.8rem 1rem;
            background: #eab308;
            color: #713f12;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-main:hover  { background: #ca8a04; color: #fff; }
        .btn-main:active { transform: scale(0.98); }

        /* Tombol Google */
        .btn-google {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            background: #fff;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
        }
        .btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Animasi fade-in panel kanan */
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(18px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slideIn 0.45s ease both; }

        /* Stat pill di panel kiri */
        .stat-pill {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 1rem;
            padding: 0.875rem 1.25rem;
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body style="min-height:100vh; display:flex; background:#f1f5f9;">

    {{-- ════════════════════════════════════════════════════════
         WRAPPER UTAMA — split screen
    ════════════════════════════════════════════════════════ --}}
    <div style="display:flex; width:100%; min-height:100vh;">

        {{-- ── PANEL KIRI — Branding ───────────────────────────── --}}
        <div class="login-panel-left"
             style="flex:0 0 55%; display:flex; flex-direction:column;
                    justify-content:space-between; padding:3rem 3.5rem;
                    position:relative;"
             id="leftPanel">

            {{-- Dekorasi lingkaran --}}
            <div class="deco-circle" style="width:500px;height:500px;top:-180px;right:-140px;"></div>
            <div class="deco-circle" style="width:300px;height:300px;bottom:-80px;left:-80px;"></div>
            <div class="deco-circle" style="width:180px;height:180px;bottom:200px;right:60px; background:rgba(255,255,255,0.06);"></div>

            {{-- Logo + Nama --}}
            <div style="position:relative; z-index:1;">
                <div style="display:flex; align-items:center; gap:0.875rem; margin-bottom:0.5rem;">
                    <div style="width:48px;height:48px;border-radius:14px;
                                background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);
                                display:flex;align-items:center;justify-content:center; backdrop-filter:blur(4px);">
                        <svg style="width:26px;height:26px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span style="font-size:1.35rem; font-weight:800; color:#fff; letter-spacing:-0.02em;">Penginapan</span>
                </div>
            </div>

            {{-- Tagline & deskripsi tengah --}}
            <div style="position:relative; z-index:1; max-width:460px;">
                <h1 style="font-size:2.6rem; font-weight:800; color:#fff;
                            line-height:1.15; letter-spacing:-0.03em; margin:0 0 1.25rem;">
                    Nikmati Kenyamanan<br>
                    <span style="color:rgba(255,255,255,0.65);">Setiap Menginap</span>
                </h1>
                <p style="font-size:1rem; color:rgba(255,255,255,0.6); line-height:1.7; margin:0 0 2.5rem;">
                    Temukan kamar terbaik, booking mudah, dan pengalaman menginap yang tak terlupakan bersama kami.
                </p>

                {{-- Stat pills --}}
                <div style="display:flex; flex-direction:column; gap:0.875rem;">
                    <div class="stat-pill">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:700;font-size:0.95rem;margin:0;">24 Kamar Tersedia</p>
                            <p style="color:rgba(255,255,255,0.55);font-size:0.78rem;margin:0;">Berbagai tipe kamar pilihan</p>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:700;font-size:0.95rem;margin:0;">1.200+ Tamu Puas</p>
                            <p style="color:rgba(255,255,255,0.55);font-size:0.78rem;margin:0;">Rating 4.9/5 dari tamu setia</p>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:700;font-size:0.95rem;margin:0;">Check-in 24 Jam</p>
                            <p style="color:rgba(255,255,255,0.55);font-size:0.78rem;margin:0;">Layanan siap setiap saat</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer panel kiri --}}
            <div style="position:relative;z-index:1;">
                <p style="color:rgba(255,255,255,0.35);font-size:0.78rem;margin:0;">
                    &copy; {{ date('Y') }} Penginapan. All rights reserved.
                </p>
            </div>
        </div>

        {{-- ── PANEL KANAN — Form Login ─────────────────────────── --}}
        <div style="flex:1; display:flex; align-items:center; justify-content:center;
                    background:#fff; padding:2rem 2rem;"
             id="rightPanel">

            <div class="animate-slide-in" style="width:100%; max-width:420px;">

                {{-- Header form --}}
                <div style="margin-bottom:2rem;">
                    <h2 style="font-size:1.65rem;font-weight:800;color:#0f172a;
                                letter-spacing:-0.025em;margin:0 0 0.4rem;">Masuk ke Akun</h2>
                    <p style="color:#64748b;font-size:0.9rem;margin:0;">
                        Gunakan email atau login cepat via Google.
                    </p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div style="margin-bottom:1.25rem; padding:1rem 1.1rem;
                                background:#fef2f2; border:1px solid #fecaca;
                                border-radius:0.75rem;">
                        @foreach ($errors->all() as $error)
                            <p style="color:#dc2626;font-size:0.85rem;margin:0 0 0.2rem;">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Session status --}}
                @if (session('status'))
                    <div style="margin-bottom:1.25rem; padding:1rem 1.1rem;
                                background:#f0fdf4; border:1px solid #bbf7d0;
                                border-radius:0.75rem;">
                        <p style="color:#16a34a;font-size:0.85rem;margin:0;">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.post') }}" style="display:flex;flex-direction:column;gap:1.1rem;">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" style="display:block;font-size:0.85rem;font-weight:600;
                                                   color:#374151;margin-bottom:0.45rem;">
                            Alamat Email
                        </label>
                        <input
                            id="email" type="email" name="email"
                            value="{{ old('email') }}"
                            required autofocus autocomplete="username"
                            placeholder="email@contoh.com"
                            class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" style="display:block;font-size:0.85rem;font-weight:600;
                                                      color:#374151;margin-bottom:0.45rem;">
                            Password
                        </label>
                        <div style="position:relative;">
                            <input
                                id="password" type="password" name="password"
                                required autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                                style="padding-right:3rem;"
                            >
                            {{-- Toggle show/hide password --}}
                            <button type="button" onclick="togglePassword()"
                                style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);
                                       background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;"
                                aria-label="Tampilkan password">
                                <svg id="eyeIcon" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <input id="remember" name="remember" type="checkbox"
                            style="width:16px;height:16px;border-radius:4px;
                                   accent-color:#eab308;cursor:pointer;">
                        <label for="remember" style="font-size:0.85rem;color:#64748b;cursor:pointer;">
                            Ingat saya selama 30 hari
                        </label>
                    </div>

                    {{-- CAPTCHA ── --}}
                    <div>
                        <label for="captcha" style="display:block;font-size:0.85rem;font-weight:600;
                                                     color:#374151;margin-bottom:0.45rem;">
                            Kode Verifikasi
                        </label>
                        <div style="display:flex;gap:0.625rem;align-items:stretch;">
                            {{-- Kotak tampilan captcha --}}
                            <div id="captchaBox"
                                 style="display:flex;align-items:center;justify-content:center;
                                        min-width:110px;padding:0 1rem;
                                        background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);
                                        border-radius:0.75rem;border:1.5px solid #e2e8f0;
                                        position:relative;overflow:hidden;user-select:none;
                                        flex-shrink:0;">
                                {{-- Noise lines dekorasi --}}
                                <svg style="position:absolute;inset:0;width:100%;height:100%;opacity:0.12;"
                                     preserveAspectRatio="none" viewBox="0 0 110 48">
                                    <line x1="0"   y1="38" x2="110" y2="10"  stroke="white" stroke-width="1.2"/>
                                    <line x1="0"   y1="15" x2="110" y2="42"  stroke="white" stroke-width="0.8"/>
                                    <line x1="20"  y1="0"  x2="40"  y2="48"  stroke="white" stroke-width="0.7"/>
                                    <line x1="70"  y1="0"  x2="90"  y2="48"  stroke="white" stroke-width="0.7"/>
                                </svg>
                                <span id="captchaText"
                                      style="font-family:'SF Mono','Fira Code','Courier New',monospace;
                                             font-size:1.45rem;font-weight:800;letter-spacing:0.25em;
                                             color:#fff;position:relative;z-index:1;
                                             text-shadow:0 1px 4px rgba(0,0,0,0.5);
                                             filter:blur(0);transform:skewX(-4deg);display:block;">
                                    {{ session('login_captcha', '----') }}
                                </span>
                            </div>

                            {{-- Input + tombol refresh --}}
                            <div style="flex:1;display:flex;flex-direction:column;gap:0.375rem;">
                                <div style="display:flex;gap:0.375rem;flex:1;">
                                    <input
                                        id="captcha" name="captcha"
                                        type="text" maxlength="4"
                                        autocomplete="off" spellcheck="false"
                                        placeholder="Ketik kode"
                                        style="flex:1;padding:0.75rem 1rem;
                                               background:#f8fafc;border:1.5px solid #e2e8f0;
                                               border-radius:0.75rem;font-size:1rem;
                                               font-family:'SF Mono','Fira Code',monospace;
                                               font-weight:700;letter-spacing:0.18em;
                                               color:#0f172a;text-transform:uppercase;
                                               outline:none;transition:border-color 0.15s,box-shadow 0.15s;
                                               {{ $errors->has('captcha') ? 'border-color:#ef4444;' : '' }}"
                                        onfocus="this.style.borderColor='#eab308';
                                                 this.style.boxShadow='0 0 0 3px rgba(234,179,8,0.15)'"
                                        onblur="this.style.borderColor='{{ $errors->has('captcha') ? '#ef4444' : '#e2e8f0' }}';
                                                this.style.boxShadow='none'"
                                        oninput="this.value=this.value.toUpperCase()"
                                    >
                                    {{-- Tombol refresh captcha --}}
                                    <button type="button" onclick="refreshCaptcha()"
                                        id="captchaRefreshBtn"
                                        title="Ganti kode"
                                        style="width:44px;flex-shrink:0;border-radius:0.75rem;
                                               border:1.5px solid #e2e8f0;background:#f8fafc;
                                               cursor:pointer;display:flex;align-items:center;
                                               justify-content:center;transition:border-color 0.15s,background 0.15s;"
                                        onmouseover="this.style.borderColor='#eab308';this.style.background='#fff'"
                                        onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                                        <svg id="refreshIcon" style="width:16px;height:16px;color:#64748b;
                                                                       transition:transform 0.4s ease;"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </div>
                                <p style="font-size:0.72rem;color:#94a3b8;margin:0;">
                                    Tidak bisa dibaca?
                                    <button type="button" onclick="refreshCaptcha()"
                                        style="background:none;border:none;cursor:pointer;
                                               color:#eab308;font-size:0.72rem;
                                               font-weight:600;padding:0;text-decoration:underline;">
                                        Ganti kode
                                    </button>
                                </p>
                            </div>
                        </div>

                        {{-- Error captcha --}}
                        @error('captcha')
                            <p style="color:#dc2626;font-size:0.78rem;margin:0.3rem 0 0;">
                                ⚠ {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- /CAPTCHA ── --}}

                    {{-- Submit --}}
                    <button type="submit" class="btn-main" style="margin-top:0.25rem;">
                        Masuk Sekarang
                    </button>
                </form>

                {{-- Divider --}}
                <div style="display:flex;align-items:center;margin:1.5rem 0;gap:0.75rem;">
                    <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                    <span style="font-size:0.8rem;color:#94a3b8;white-space:nowrap;">atau lanjutkan dengan</span>
                    <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                </div>

                {{-- Google --}}
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg style="width:20px;height:20px;flex-shrink:0;" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                {{-- Info akun demo --}}
                <div style="margin-top:1.75rem; padding:1rem 1.1rem;
                            background:#f8fafc; border:1px solid #e2e8f0;
                            border-radius:0.75rem;">
                    <p style="font-size:0.78rem;font-weight:600;color:#475569;margin:0 0 0.5rem;">
                        🔑 Demo akun Admin:
                    </p>
                    <p style="font-size:0.78rem;color:#64748b;margin:0 0 0.2rem;">
                        <strong>admin@penginapan.com</strong> / admin123
                    </p>
                    <p style="font-size:0.78rem;color:#64748b;margin:0;">
                        <strong>manager@penginapan.com</strong> / manager123
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Responsive: sembunyikan panel kiri di layar kecil --}}
    <style>
        @media (max-width: 768px) {
            #leftPanel  { display: none !important; }
            #rightPanel {
                padding: 2rem 1.5rem !important;
                background: linear-gradient(180deg, #f8fafc 0%, #fff 100%) !important;
            }
            /* Tampilkan mini brand di atas form saat mobile */
            #mobileBrand { display: flex !important; }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            #leftPanel { flex: 0 0 45% !important; padding: 2.5rem 2.5rem !important; }
            #leftPanel h1 { font-size: 2rem !important; }
        }
    </style>

    {{-- Mobile brand header (hidden di desktop) --}}
    <div id="mobileBrand"
         style="display:none; position:fixed; top:0; left:0; right:0; z-index:50;
                align-items:center; gap:0.75rem; padding:1rem 1.5rem;
                background:white; border-bottom:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="width:36px;height:36px;border-radius:10px;
                    background:#eab308;display:flex;align-items:center;justify-content:center;">
            <svg style="width:20px;height:20px;color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <span style="font-size:1.1rem;font-weight:800;color:#0f172a;">Penginapan</span>
    </div>

    <script>
        function togglePassword() {
            var inp = document.getElementById('password');
            inp.type = inp.type === 'password' ? 'text' : 'password';
        }

        // ── Refresh captcha via AJAX ──────────────────────────
        function refreshCaptcha() {
            var icon = document.getElementById('refreshIcon');
            var btn  = document.getElementById('captchaRefreshBtn');

            // Animasi putar icon
            icon.style.transform = 'rotate(360deg)';
            btn.disabled = true;

            fetch('{{ route('captcha.refresh') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                // Update teks captcha di kotak
                var box = document.getElementById('captchaText');
                box.style.opacity = '0';
                box.style.transform = 'skewX(-4deg) scale(0.85)';
                box.style.transition = 'opacity 0.15s, transform 0.15s';

                setTimeout(function(){
                    box.textContent = data.captcha;
                    box.style.opacity = '1';
                    box.style.transform = 'skewX(-4deg) scale(1)';
                }, 150);

                // Kosongkan input
                var input = document.getElementById('captcha');
                if (input) { input.value = ''; input.focus(); }
            })
            .catch(function(){ /* silent */ })
            .finally(function(){
                setTimeout(function(){
                    icon.style.transform = 'rotate(0deg)';
                    btn.disabled = false;
                }, 420);
            });
        }
    </script>
</body>
</html>
