@extends('Visitors.layouts.app')

@section('title', 'Dashboard Visitor')

@section('content')

    {{-- ── Welcome Banner ── --}}
    <div class="theme-accent-bg"
         style="border-radius:1.25rem; padding:1.75rem 2rem; margin-bottom:2rem;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:1rem;">
        <div>
            <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin:0 0 0.3rem;">
                Selamat datang 👋
            </p>
            <h1 style="color:#fff;font-size:1.5rem;font-weight:800;margin:0;letter-spacing:-0.02em;">
                {{ Auth::user()->name }}
            </h1>
            <span style="display:inline-block;margin-top:0.5rem;
                         background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.25);
                         padding:0.2rem 0.75rem;border-radius:999px;
                         font-size:0.75rem;font-weight:600;color:#fff;">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="avatar"
                style="width:60px;height:60px;border-radius:50%;object-fit:cover;
                       border:3px solid rgba(255,255,255,0.3);">
        @else
            <div style="width:60px;height:60px;border-radius:50%;
                         background:rgba(255,255,255,0.2);border:3px solid rgba(255,255,255,0.3);
                         display:flex;align-items:center;justify-content:center;">
                <span style="color:#fff;font-size:1.3rem;font-weight:800;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
        @endif
    </div>

    {{-- ── Section Title ── --}}
    <div style="margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin:0;">Kamar Tersedia</h2>
            <p style="color:#64748b;font-size:0.82rem;margin:0.25rem 0 0;">
                Temukan kamar yang sesuai kebutuhan Anda.
            </p>
        </div>
        <span style="font-size:0.78rem;font-weight:600;background:var(--color-primary-50);
                     color:var(--color-primary-text);padding:0.3rem 0.75rem;border-radius:999px;">
            3 kamar
        </span>
    </div>

    {{-- ── Room Cards ── --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;">
        @foreach([
            ['name' => 'Kamar Deluxe',   'price' => '350.000', 'bed' => 'King Bed',   'available' => true,  'tag' => 'Populer'],
            ['name' => 'Kamar Superior', 'price' => '250.000', 'bed' => 'Queen Bed',  'available' => true,  'tag' => ''],
            ['name' => 'Kamar Standar',  'price' => '150.000', 'bed' => 'Single Bed', 'available' => false, 'tag' => ''],
        ] as $room)
        <div style="background:#fff;border-radius:1rem;overflow:hidden;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;
                    transition:box-shadow 0.2s, transform 0.2s;"
             onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'">

            {{-- Image placeholder --}}
            <div class="theme-accent-bg"
                 style="height:160px; display:flex; align-items:center; justify-content:center;
                        position:relative; overflow:hidden;">
                <svg style="width:48px;height:48px;color:rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                @if($room['tag'])
                    <div style="position:absolute;top:0.75rem;left:0.75rem;
                                background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);
                                padding:0.2rem 0.6rem;border-radius:999px;
                                font-size:0.7rem;font-weight:700;color:#fff;backdrop-filter:blur(4px);">
                        ⭐ {{ $room['tag'] }}
                    </div>
                @endif
            </div>

            {{-- Card body --}}
            <div style="padding:1.25rem;">
                <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:0.375rem;">
                    <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;margin:0;">
                        {{ $room['name'] }}
                    </h3>
                    <span style="flex-shrink:0;margin-left:0.5rem;padding:0.15rem 0.6rem;
                                 border-radius:999px;font-size:0.7rem;font-weight:700;
                                 {{ $room['available'] ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;' }}">
                        {{ $room['available'] ? 'Tersedia' : 'Penuh' }}
                    </span>
                </div>

                <p style="color:#94a3b8;font-size:0.8rem;margin:0 0 1rem;
                           display:flex;align-items:center;gap:0.25rem;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    {{ $room['bed'] }}
                </p>

                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <span class="theme-text-primary"
                              style="font-size:1.15rem;font-weight:800;">
                            Rp {{ $room['price'] }}
                        </span>
                        <span style="color:#94a3b8;font-size:0.75rem;">/malam</span>
                    </div>
                    @if($room['available'])
                        <button class="theme-btn"
                            style="padding:0.5rem 1.1rem;border-radius:0.5rem;border:none;
                                   font-size:0.82rem;font-weight:600;cursor:pointer;">
                            Pesan
                        </button>
                    @else
                        <button disabled
                            style="padding:0.5rem 1.1rem;border-radius:0.5rem;border:none;
                                   background:#f1f5f9;color:#94a3b8;font-size:0.82rem;
                                   font-weight:600;cursor:not-allowed;">
                            Penuh
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Responsive grid --}}
    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:repeat(3,1fr)"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media (max-width: 560px) {
            div[style*="grid-template-columns:repeat(3,1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

@endsection
