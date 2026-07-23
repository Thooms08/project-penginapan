<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Visitor') — Penginapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f8fafc; }

        .navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 30;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
        }
        .navbar-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 1.5rem; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logout-btn {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.5rem 0.875rem; border-radius: 0.625rem;
            border: 1.5px solid #e2e8f0; background: transparent;
            font-size: 0.82rem; font-weight: 600; color: #64748b;
            cursor: pointer; text-decoration: none;
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }
        .logout-btn:hover { border-color: #fca5a5; color: #ef4444; background: #fef2f2; }
        .main-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }
        footer { margin-top: 4rem; border-top: 1px solid #e2e8f0; padding: 1.5rem;
                 text-align: center; font-size: 0.78rem; color: #94a3b8; }
        @media (max-width: 640px) {
            .main-wrap { padding: 1.25rem 1rem; }
            .navbar-inner { padding: 0 1rem; }
            .user-name { display: none; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-inner">
            <div style="display:flex;align-items:center;gap:0.625rem;">
                <div style="width:34px;height:34px;border-radius:9px;background:#eab308;
                             display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span style="font-size:1rem;font-weight:800;color:#0f172a;">Penginapan</span>
            </div>

            <div style="display:flex;align-items:center;gap:1.5rem;" class="nav-links">
                <a href="{{ route('visitor.dashboard') }}"
                   style="font-size:0.85rem;font-weight:600;color:#eab308;text-decoration:none;
                          display:flex;align-items:center;gap:0.375rem;">
                    <span style="width:7px;height:7px;border-radius:50%;background:#eab308;display:inline-block;"></span>
                    Dashboard
                </a>
                <a href="#" style="font-size:0.85rem;font-weight:500;color:#64748b;text-decoration:none;">Kamar</a>
                <a href="#" style="font-size:0.85rem;font-weight:500;color:#64748b;text-decoration:none;">Booking Saya</a>
            </div>

            <div style="display:flex;align-items:center;gap:0.875rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="avatar"
                            style="width:34px;height:34px;border-radius:50%;object-fit:cover;
                                   border:2px solid #fef9c3;">
                    @else
                        <div style="width:34px;height:34px;border-radius:50%;background:#eab308;
                                     display:flex;align-items:center;justify-content:center;">
                            <span style="color:#713f12;font-size:0.78rem;font-weight:700;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <span class="user-name" style="font-size:0.85rem;font-weight:600;color:#374151;">
                        {{ Auth::user()->name }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="main-wrap">
        @yield('content')
    </div>

    <footer>
        &copy; {{ date('Y') }} Penginapan. All rights reserved.
    </footer>

</body>
</html>
