@extends('Admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan aktivitas penginapan hari ini')

@section('content')

    {{-- ── Welcome Banner ── --}}
    <div class="theme-accent-bg"
         style="border-radius:1.25rem; padding:1.75rem 2rem; margin-bottom:2rem;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:1rem;">
        <div>
            <p style="color:rgba(255,255,255,0.75);font-size:0.85rem;margin:0 0 0.3rem;">
                Selamat datang kembali 👋
            </p>
            <h2 style="color:#fff;font-size:1.5rem;font-weight:800;margin:0;letter-spacing:-0.02em;">
                {{ Auth::user()->name }}
            </h2>
            <p style="color:rgba(255,255,255,0.65);font-size:0.85rem;margin:0.4rem 0 0;">
                {{ date('l, d F Y') }}
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="avatar"
                    style="width:56px;height:56px;border-radius:50%;object-fit:cover;
                           border:3px solid rgba(255,255,255,0.3);">
            @else
                <div style="width:56px;height:56px;border-radius:50%;
                             background:rgba(255,255,255,0.2); border:3px solid rgba(255,255,255,0.3);
                             display:flex;align-items:center;justify-content:center;">
                    <span style="color:#fff;font-size:1.25rem;font-weight:800;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Stats Grid ── --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; margin-bottom:2rem;">

        {{-- Stat card 1 --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="color:#64748b;font-size:0.8rem;font-weight:600;margin:0 0 0.5rem;
                               text-transform:uppercase;letter-spacing:0.05em;">Total Kamar</p>
                    <p style="color:#0f172a;font-size:2rem;font-weight:800;margin:0;line-height:1;">24</p>
                    <p style="color:#22c55e;font-size:0.75rem;font-weight:600;margin:0.4rem 0 0;">
                        ↑ 2 baru bulan ini
                    </p>
                </div>
                <div class="theme-icon-box"
                     style="width:48px;height:48px;border-radius:12px;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="theme-icon" style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Stat card 2 --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="color:#64748b;font-size:0.8rem;font-weight:600;margin:0 0 0.5rem;
                               text-transform:uppercase;letter-spacing:0.05em;">Booking Aktif</p>
                    <p style="color:#0f172a;font-size:2rem;font-weight:800;margin:0;line-height:1;">8</p>
                    <p style="color:#3b82f6;font-size:0.75rem;font-weight:600;margin:0.4rem 0 0;">
                        ↑ 3 hari ini
                    </p>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:24px;height:24px;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Stat card 3 --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="color:#64748b;font-size:0.8rem;font-weight:600;margin:0 0 0.5rem;
                               text-transform:uppercase;letter-spacing:0.05em;">Total Visitor</p>
                    <p style="color:#0f172a;font-size:2rem;font-weight:800;margin:0;line-height:1;">142</p>
                    <p style="color:#f59e0b;font-size:0.75rem;font-weight:600;margin:0.4rem 0 0;">
                        ↑ 12 minggu ini
                    </p>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#fffbeb;
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:24px;height:24px;color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom row ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

        {{-- Info Akun --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <h3 style="font-size:0.9rem;font-weight:700;color:#0f172a;margin:0 0 1rem;">
                Info Akun
            </h3>
            <div style="display:flex;align-items:center;gap:1rem;">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="avatar"
                        style="width:52px;height:52px;border-radius:50%;object-fit:cover;
                               border:2px solid var(--color-primary-100);">
                @else
                    <div style="width:52px;height:52px;border-radius:50%;background:var(--color-primary);
                                 display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="color:#fff;font-size:1.15rem;font-weight:800;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <p style="font-weight:700;color:#0f172a;margin:0;font-size:0.95rem;">
                        {{ Auth::user()->name }}
                    </p>
                    <p style="color:#64748b;font-size:0.8rem;margin:0.2rem 0 0.5rem;">
                        {{ Auth::user()->email }}
                    </p>
                    <span class="theme-badge"
                          style="display:inline-block;padding:0.2rem 0.75rem;
                                 border-radius:999px;font-size:0.72rem;font-weight:700;">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <h3 style="font-size:0.9rem;font-weight:700;color:#0f172a;margin:0 0 1rem;">
                Aksi Cepat
            </h3>
            <div style="display:flex;flex-direction:column;gap:0.625rem;">
                <button class="theme-btn"
                    style="padding:0.65rem 1rem;border-radius:0.625rem;border:none;
                           font-size:0.85rem;font-weight:600;cursor:pointer;
                           display:flex;align-items:center;gap:0.5rem;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kamar Baru
                </button>
                <button onclick="openThemePicker()"
                    style="padding:0.65rem 1rem;border-radius:0.625rem;border:1.5px solid #e2e8f0;
                           background:#f8fafc;font-size:0.85rem;font-weight:600;color:#475569;cursor:pointer;
                           display:flex;align-items:center;gap:0.5rem;
                           transition:border-color 0.15s;"
                    onmouseover="this.style.borderColor='var(--color-primary)'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Ubah Tema Warna
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive fix stats grid --}}
    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:repeat(3,1fr)"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media (max-width: 600px) {
            div[style*="grid-template-columns:repeat(3,1fr)"],
            div[style*="grid-template-columns:1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

@endsection
