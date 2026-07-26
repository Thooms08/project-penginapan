<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Visitor') — Penginapan</title>
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
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ─── Navbar link aktif ─── */
        .nav-active {
            color: #eab308;
            font-weight: 600;
        }
        .nav-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #eab308;
            display: inline-block;
            flex-shrink: 0;
        }

        /* ─── Logout button ─── */
        .logout-btn {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            border-radius: 0.625rem;
            border: 1.5px solid #e2e8f0;
            background: transparent;
            font-size: 0.82rem; font-weight: 600; color: #64748b;
            cursor: pointer; transition: all 0.15s;
        }
        .logout-btn:hover { border-color: #fca5a5; color: #ef4444; background: #fef2f2; }

        /* ─── Avatar visitor ─── */
        .avatar-visitor {
            width: 34px; height: 34px; border-radius: 9999px;
            background: #eab308;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.78rem; font-weight: 700; color: #713f12;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- ── Navbar ── --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">

            {{-- Brand --}}
            <div class="flex items-center gap-2.5">
                <div class="w-[34px] h-[34px] rounded-[9px] flex items-center justify-center"
                     style="background:#eab308;">
                    <svg class="w-[18px] h-[18px]" style="color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-base font-extrabold text-slate-900">Penginapan</span>
            </div>

            {{-- Nav links (desktop) --}}
            <div class="hidden sm:flex items-center gap-6">
                <a href="{{ route('visitor.dashboard') }}"
                   class="nav-active flex items-center gap-1.5 text-[0.85rem] no-underline">
                    <span class="nav-dot"></span>
                    Dashboard
                </a>
                <a href="#" class="text-[0.85rem] font-medium text-slate-500 no-underline hover:text-slate-800 transition-colors">
                    Kamar
                </a>
                <a href="#" class="text-[0.85rem] font-medium text-slate-500 no-underline hover:text-slate-800 transition-colors">
                    Booking Saya
                </a>
            </div>

            {{-- User + Logout --}}
            <div class="flex items-center gap-3.5">
                <div class="flex items-center gap-2">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="avatar"
                            class="w-[34px] h-[34px] rounded-full object-cover"
                            style="border:2px solid #fef9c3;">
                    @else
                        <div class="avatar-visitor">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="hidden md:block text-[0.85rem] font-semibold text-slate-700">
                        {{ Auth::user()->name }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- ── Content ── --}}
    <main class="max-w-[1200px] mx-auto px-6 py-8">
        @yield('content')
    </main>

    {{-- ── Footer ── --}}
    <footer class="mt-16 border-t border-slate-200 py-6 text-center text-[0.78rem] text-slate-400">
        &copy; {{ date('Y') }} Penginapan. All rights reserved.
    </footer>

</body>
</html>
