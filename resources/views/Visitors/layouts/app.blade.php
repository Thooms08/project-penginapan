<!DOCTYPE html>
<html lang="id" data-theme="indigo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Visitor') — Penginapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">
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
            padding: 0 1.5rem;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .theme-nav-indicator {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--color-primary);
            display: inline-block;
        }

        .logout-btn {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            border-radius: 0.625rem;
            border: 1.5px solid #e2e8f0;
            background: transparent;
            font-size: 0.82rem; font-weight: 600; color: #64748b;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }
        .logout-btn:hover {
            border-color: #fca5a5;
            color: #ef4444;
            background: #fef2f2;
        }

        .main-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }

        footer {
            margin-top: 4rem;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: #94a3b8;
        }

        @media (max-width: 640px) {
            .main-wrap { padding: 1.25rem 1rem; }
            .navbar-inner { padding: 0 1rem; }
            .user-name { display: none; }
        }
    </style>
</head>
<body>

    {{-- ── Navbar ── --}}
    <nav class="navbar">
        <div class="navbar-inner">
            {{-- Brand --}}
            <div style="display:flex;align-items:center;gap:0.625rem;">
                <div style="width:34px;height:34px;border-radius:9px;
                             background:var(--color-primary);
                             display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span style="font-size:1rem;font-weight:800;color:#0f172a;">Penginapan</span>
            </div>

            {{-- Nav links (desktop) --}}
            <div style="display:flex;align-items:center;gap:1.5rem;" class="nav-links">
                <a href="{{ route('visitor.dashboard') }}"
                   style="font-size:0.85rem;font-weight:600;color:var(--color-primary);
                          text-decoration:none;display:flex;align-items:center;gap:0.375rem;">
                    <span class="theme-nav-indicator"></span> Dashboard
                </a>
                <a href="#" style="font-size:0.85rem;font-weight:500;color:#64748b;text-decoration:none;">Kamar</a>
                <a href="#" style="font-size:0.85rem;font-weight:500;color:#64748b;text-decoration:none;">Booking Saya</a>
            </div>

            {{-- User + Logout --}}
            <div style="display:flex;align-items:center;gap:0.875rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="avatar"
                            style="width:34px;height:34px;border-radius:50%;object-fit:cover;
                                   border:2px solid var(--color-primary-100);">
                    @else
                        <div style="width:34px;height:34px;border-radius:50%;
                                     background:var(--color-primary);
                                     display:flex;align-items:center;justify-content:center;">
                            <span style="color:#fff;font-size:0.78rem;font-weight:700;">
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

    {{-- ── Content ── --}}
    <div class="main-wrap">
        @yield('content')
    </div>

    {{-- ── Footer ── --}}
    <footer>
        &copy; {{ date('Y') }} Penginapan. All rights reserved.
    </footer>

    <script>
        // Terapkan tema tersimpan
        (function() {
            var saved = localStorage.getItem('penginapan_color');
            if (!saved) return;
            var hex = saved;
            function fromHex(h){h=h.replace('#','');return[parseInt(h.slice(0,2),16),parseInt(h.slice(2,4),16),parseInt(h.slice(4,6),16)];}
            function toHex(r,g,b){return'#'+[r,g,b].map(x=>Math.max(0,Math.min(255,Math.round(x))).toString(16).padStart(2,'0')).join('');}
            function lighten(r,g,b,t){return[r+(255-r)*t,g+(255-g)*t,b+(255-b)*t];}
            function darken(r,g,b,t){return[r*(1-t),g*(1-t),b*(1-t)];}
            function luma(r,g,b){return 0.2126*r+0.7152*g+0.0722*b;}
            function hslToRgb(h,s,l){s/=100;l/=100;var k=function(n){return(n+h/30)%12;},a=s*Math.min(l,1-l),f=function(n){return l-a*Math.max(-1,Math.min(k(n)-3,Math.min(9-k(n),1)));};return[Math.round(f(0)*255),Math.round(f(8)*255),Math.round(f(4)*255)];}
            function rgbToHsv(r,g,b){r/=255;g/=255;b/=255;var mx=Math.max(r,g,b),mn=Math.min(r,g,b),d=mx-mn,hh=0;if(d>0){if(mx===r)hh=((g-b)/d)%6;else if(mx===g)hh=(b-r)/d+2;else hh=(r-g)/d+4;hh*=60;if(hh<0)hh+=360;}return[hh];}
            var rgb=fromHex(hex),r=rgb[0],g=rgb[1],b=rgb[2];
            var dark=darken(r,g,b,0.18),light=lighten(r,g,b,0.1);
            var v100=lighten(r,g,b,0.85);
            var hh=rgbToHsv(r,g,b)[0];
            var sideHS=hslToRgb(hh,45,12);
            var sideBg='rgb('+sideHS[0]+','+sideHS[1]+','+sideHS[2]+')';
            var onPrimary=luma(r,g,b)>140?'#0f172a':'#ffffff';
            var lT=hslToRgb(hh,50,8),lM=hslToRgb(hh,45,18),lB=hslToRgb(hh,40,28);
            var badgeText=toHex(...darken(r,g,b,0.35));
            var root=document.documentElement;
            root.removeAttribute('data-theme');
            root.style.setProperty('--color-primary',hex);
            root.style.setProperty('--color-primary-dark',toHex(...dark));
            root.style.setProperty('--color-primary-light',toHex(...light));
            root.style.setProperty('--color-primary-50',toHex(...lighten(r,g,b,0.93)));
            root.style.setProperty('--color-primary-100',toHex(...v100));
            root.style.setProperty('--color-primary-text',badgeText);
            root.style.setProperty('--color-primary-rgb',r+','+g+','+b);
            root.style.setProperty('--on-primary',onPrimary);
            root.style.setProperty('--sidebar-bg',sideBg);
            root.style.setProperty('--sidebar-active',hex);
            root.style.setProperty('--sidebar-hover','rgba(255,255,255,0.08)');
            root.style.setProperty('--sidebar-text-muted','rgba(255,255,255,0.55)');
            root.style.setProperty('--btn-primary-bg',hex);
            root.style.setProperty('--btn-primary-hover',toHex(...dark));
            root.style.setProperty('--badge-bg',toHex(...v100));
            root.style.setProperty('--badge-text',badgeText);
            root.style.setProperty('--accent-gradient','linear-gradient(135deg,'+hex+' 0%,'+toHex(...light)+' 100%)');
            root.style.setProperty('--login-panel-bg','linear-gradient(160deg,rgb('+lT.join(',')+')'
                +' 0%,rgb('+lM.join(',')+')'
                +' 50%,rgb('+lB.join(',')+')'
                +' 100%)');
        })();
    </script>
</body>
</html>
