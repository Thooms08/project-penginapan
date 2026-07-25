@extends('Admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan aktivitas penginapan hari ini')

@section('content')

    {{-- ── Welcome Banner ── --}}
    <div class="rounded-2xl p-7 mb-8 flex items-center justify-between flex-wrap gap-4"
         style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
        <div>
            <p class="text-sm mb-1" style="color:rgba(0,0,0,0.45);">Selamat datang kembali 👋</p>
            <h2 class="text-2xl font-extrabold tracking-tight" style="color:#713f12;">
                {{ Auth::user()->name }}
            </h2>
            <p class="text-sm mt-1" style="color:rgba(0,0,0,0.4);">{{ date('l, d F Y') }}</p>
        </div>
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="avatar"
                class="w-14 h-14 rounded-full object-cover"
                style="border:3px solid rgba(255,255,255,0.5);">
        @else
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl font-extrabold"
                 style="background:rgba(255,255,255,0.25);border:3px solid rgba(255,255,255,0.4);color:#713f12;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        @endif
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        {{-- Kamar --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[0.78rem] font-semibold text-slate-500 uppercase tracking-wide mb-2">Total Kamar</p>
                    <p class="text-[2rem] font-extrabold text-slate-900 leading-none">24</p>
                    <p class="text-[0.75rem] font-semibold text-green-500 mt-1.5">↑ 2 baru bulan ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#fefce8;">
                    <svg class="w-6 h-6" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Booking --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[0.78rem] font-semibold text-slate-500 uppercase tracking-wide mb-2">Booking Aktif</p>
                    <p class="text-[2rem] font-extrabold text-slate-900 leading-none">8</p>
                    <p class="text-[0.75rem] font-semibold text-blue-500 mt-1.5">↑ 3 hari ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Visitor --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[0.78rem] font-semibold text-slate-500 uppercase tracking-wide mb-2">Total Visitor</p>
                    <p class="text-[2rem] font-extrabold text-slate-900 leading-none">142</p>
                    <p class="text-[0.75rem] font-semibold mt-1.5" style="color:#eab308;">↑ 12 minggu ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#fefce8;">
                    <svg class="w-6 h-6" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Info Akun --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-[0.9rem] font-bold text-slate-900 mb-4">Info Akun</h3>
            <div class="flex items-center gap-4">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="avatar"
                        class="w-[52px] h-[52px] rounded-full object-cover shrink-0"
                        style="border:2px solid #fef9c3;">
                @else
                    <div class="w-[52px] h-[52px] rounded-full flex items-center justify-center shrink-0
                                text-xl font-extrabold"
                         style="background:#eab308;color:#713f12;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="font-bold text-slate-900 text-[0.95rem]">{{ Auth::user()->name }}</p>
                    <p class="text-[0.8rem] text-slate-500 mt-0.5 mb-2">{{ Auth::user()->email }}</p>
                    <span class="inline-block px-3 py-0.5 rounded-full text-[0.72rem] font-bold"
                          style="background:#fef9c3;color:#713f12;">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-[0.9rem] font-bold text-slate-900 mb-4">Aksi Cepat</h3>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('admin.rooms.create') }}"
                   class="btn-yellow flex items-center gap-2 px-4 py-2.5 rounded-xl
                          text-[0.85rem] font-semibold no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kamar
                </a>
            </div>
        </div>
    </div>

    <style>
        .btn-yellow {
            background: #eab308;
            color: #713f12;
        }
        .btn-yellow:hover {
            background: #ca8a04;
            color: #fff;
        }
    </style>

@endsection
