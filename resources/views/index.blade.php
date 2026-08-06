@extends('Visitors.layouts.app')

@section('title', 'Beranda')

@push('head')
<style>
    /* ─── Hero Slider ─── */
    .hero-slider { position: relative; width: 100%; height: 520px; overflow: hidden; }
    @media (max-width: 767px) { .hero-slider { height: 320px; } }
    @media (min-width: 768px) and (max-width: 1023px) { .hero-slider { height: 420px; } }

    .hero-slide {
        position: absolute; inset: 0;
        opacity: 0; transition: opacity 0.9s ease;
        background-size: cover; background-position: center;
    }
    .hero-slide.active { opacity: 1; }
    .hero-slide-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to bottom,
            rgba(0,0,0,0.15) 0%,
            rgba(0,0,0,0.45) 60%,
            rgba(0,0,0,0.75) 100%);
    }

    /* Dot indicators */
    .hero-dots { position: absolute; bottom: 1.25rem; left: 50%; transform: translateX(-50%);
                 display: flex; gap: 0.45rem; z-index: 5; }
    .hero-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: rgba(255,255,255,0.4); cursor: pointer;
        transition: background 0.25s, width 0.25s; border: none; padding: 0;
    }
    .hero-dot.active { background: #eab308; width: 24px; border-radius: 4px; }

    /* Arrows */
    .hero-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        z-index: 5; width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(4px); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s; color: white;
    }
    .hero-arrow:hover { background: rgba(255,255,255,0.28); }
    .hero-arrow.prev { left: 1.25rem; }
    .hero-arrow.next { right: 1.25rem; }
    @media (max-width: 480px) {
        .hero-arrow { display: none; }
    }

    /* ─── Search container ─── */
    .search-container {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        padding: 1.5rem;
        margin: -3.5rem 1rem 0;
        position: relative; z-index: 10;
    }
    @media (max-width: 767px) {
        .search-container { margin: -2rem 0.75rem 0; padding: 1.25rem; border-radius: 1.25rem; }
    }

    /* ─── Filter tabs ─── */
    .filter-tab {
        padding: 0.45rem 1rem; border-radius: 0.625rem; font-size: 0.82rem;
        font-weight: 600; cursor: pointer; border: 1.5px solid transparent;
        transition: all 0.15s; background: #f1f5f9; color: #64748b;
    }
    .filter-tab.active, .filter-tab:hover { background: #fefce8; color: #713f12; border-color: #fef9c3; }
    .filter-tab.active { border-color: #eab308; }

    /* ─── Card grid ─── */
    .rooms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 1.25rem; }
    @media (min-width: 641px) and (max-width: 900px) { .rooms-grid { grid-template-columns: repeat(2, 1fr); } }

    /* ── Mobile 2-col card overrides (max 640px) ── */
    @media (max-width: 640px) {
        .rooms-grid { grid-template-columns: repeat(2, 1fr); gap: 0.625rem; }
        .rooms-grid .vis-room-card .card-photo { height: 7.5rem; }
        .rooms-grid .vis-room-card .card-status-badge { font-size:0.62rem; padding:0.2rem 0.45rem; top:0.5rem; left:0.5rem; }
        .rooms-grid .vis-room-card .card-badge-right { font-size:0.6rem; padding:0.18rem 0.35rem; top:0.5rem; right:0.5rem; }
        .rooms-grid .vis-room-card .card-body { padding: 0.625rem; }
        .rooms-grid .vis-room-card .card-name { font-size:0.82rem; margin-bottom:0.25rem; }
        .rooms-grid .vis-room-card .card-meta { font-size:0.7rem; margin-bottom:0.375rem; gap:0.35rem; }
        .rooms-grid .vis-room-card .card-meta-sep,
        .rooms-grid .vis-room-card .card-meta-facility { display:none; }
        .rooms-grid .vis-room-card .card-price { font-size:0.9rem; }
        .rooms-grid .vis-room-card .card-price-strike { font-size:0.68rem; }
        .rooms-grid .vis-room-card .card-price-unit { font-size:0.65rem; }
        .rooms-grid .vis-room-card .card-actions { margin-top:0.5rem; padding-top:0.5rem; gap:0.375rem; }
        .rooms-grid .vis-room-card .card-btn { padding:0.5rem 0.25rem; font-size:0.72rem; border-radius:0.625rem; gap:0.25rem; }
        .rooms-grid .vis-room-card .card-btn svg { display:none; }
        .rooms-grid .vis-room-card .card-btn-unavail { padding:0.5rem 0.25rem; font-size:0.68rem; border-radius:0.625rem; }
        .rooms-grid .vis-room-card .card-hint { font-size:0.62rem; }
    }

    /* ─── Section ─── */
    .section-wrap { max-width: 1200px; margin: 0 auto; padding: 0 1.25rem; }
    @media (min-width: 1024px) { .section-wrap { padding: 0 2rem; } }

    /* ─── Skeleton loading ─── */
    .skeleton { background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
                background-size: 400% 100%; animation: shimmer 1.5s infinite; border-radius: 0.75rem; }
    @keyframes shimmer { 0%{background-position:100% 0} 100%{background-position:-100% 0} }

    /* ─── Empty state ─── */
    .empty-state { text-align:center; padding: 3rem 1rem; color:#94a3b8; }
    .empty-state svg { margin: 0 auto 1rem; opacity: 0.4; }

    /* ─── Booking Modal ─── */
    .booking-modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 100;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        align-items: flex-end; justify-content: center;
    }
    @media (min-width: 768px) { .booking-modal-overlay { align-items: center; } }
    .booking-modal-overlay.show { display: flex; }
    .booking-modal {
        background: white; width: 100%; max-width: 480px;
        border-radius: 1.5rem 1.5rem 0 0; padding: 1.75rem;
        animation: slideUp 0.3s ease;
    }
    @media (min-width: 768px) { .booking-modal { border-radius: 1.5rem; } }
    @keyframes slideUp { from{transform:translateY(100%)} to{transform:translateY(0)} }

    /* ─── About / Info section ─── */
    .info-card {
        background: white; border-radius: 1.25rem; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════
     HERO SECTION — slider foto hotel
══════════════════════════════════════════════════ --}}
<section class="hero-slider" id="heroSlider" aria-label="Foto Hotel">


    @php
        $__hotelName = $hotel?->name ?: 'Penginapan';
        $__photos    = $hotelPhotos;
    @endphp

    @if($__photos->count() > 0)
        @foreach($__photos as $i => $photo)
            <div class="hero-slide {{ $i === 0 ? 'active' : '' }}"
                 style="background-image:url('{{ asset($photo->photo) }}');"
                 role="img" aria-label="Foto hotel {{ $i + 1 }}">
                <div class="hero-slide-overlay"></div>
            </div>
        @endforeach
    @else
        {{-- Fallback gradient jika belum ada foto --}}
        <div class="hero-slide active"
             style="background:linear-gradient(135deg,#1a1500 0%,#3d2e00 50%,#6b4e00 100%);">
            <div class="hero-slide-overlay"></div>
        </div>
    @endif

    {{-- Arrow navigation --}}
    @if($__photos->count() > 1)
        <button class="hero-arrow prev" onclick="heroSlide(-1)" aria-label="Foto sebelumnya">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button class="hero-arrow next" onclick="heroSlide(1)" aria-label="Foto berikutnya">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    @endif

    {{-- Hero text overlay --}}
    <div class="absolute inset-0 flex flex-col items-start justify-end z-3 pb-16 px-6 md:px-10 lg:px-14"
         style="z-index:4;">
        <h1 class="text-white font-extrabold text-2xl md:text-4xl lg:text-5xl
                   leading-tight tracking-tight mb-2 drop-shadow-lg max-w-xl">
            {{ $hotel?->trans('name') ?? $__hotelName }}
        </h1>
        @php $heroDesc = $hotel?->trans('description'); @endphp
        @if($heroDesc)
            <p class="text-white/75 text-[0.9rem] md:text-base max-w-lg leading-relaxed drop-shadow">
                {{ Str::limit($heroDesc, 120) }}
            </p>
        @else
            <p class="text-white/70 text-[0.9rem] md:text-base max-w-lg drop-shadow">
                {{ app()->getLocale() === 'en' ? 'Find the comfort of an unforgettable stay.' : 'Temukan kenyamanan menginap yang tak terlupakan.' }}
            </p>
        @endif
    </div>

    {{-- Dot indicators --}}
    @if($__photos->count() > 1)
        <div class="hero-dots" id="heroDots" style="z-index:5;">
            @foreach($__photos as $i => $photo)
                <button class="hero-dot {{ $i === 0 ? 'active' : '' }}"
                        onclick="heroGoTo({{ $i }})"
                        aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>

{{-- ══════════════════════════════════════════════════
     SEARCH & FILTER CONTAINER
══════════════════════════════════════════════════ --}}
<div class="section-wrap">
    <div class="search-container" data-search-form>
        {{-- Title --}}
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <div>
                <h2 class="text-[1rem] font-bold text-slate-900">{{ __('visitor.search_room') }}</h2>
                <p class="text-[0.78rem] text-slate-500 mt-0.5">{{ __('visitor.filter_by_date') }}</p>
            </div>
            {{-- Kamar tersedia badge --}}
            <span class="text-[0.78rem] font-semibold px-3 py-1 rounded-full"
                  style="background:#fefce8;color:#713f12;border:1px solid #fef9c3;"
                  id="availableBadge">
                {{ $rooms->where('status', 'available')->count() }} {{ __('visitor.available_badge') }}
            </span>
        </div>

        {{-- Filter tabs --}}
        <div class="flex gap-2 mb-4 flex-wrap">
            <button class="filter-tab active" onclick="filterRooms('all', this)" data-filter="all">
                {{ __('visitor.all') }}
            </button>
            <button class="filter-tab" onclick="filterRooms('available', this)" data-filter="available">
                {{ __('visitor.available') }}
            </button>
            <button class="filter-tab" onclick="filterRooms('unavailable', this)" data-filter="unavailable">
                {{ __('visitor.full') }}
            </button>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ route('index') }}" id="searchForm"
              class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">

            {{-- Check-in --}}
            <div>
                <label for="check_in" class="block text-[0.8rem] font-semibold text-slate-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('visitor.check_in') }}
                </label>
                <input type="date" id="check_in" name="check_in"
                       value="{{ request('check_in', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200
                              text-[0.875rem] text-slate-800 bg-slate-50
                              focus:outline-none focus:border-yellow-400 focus:ring-2
                              focus:ring-yellow-100 transition-all"
                       onchange="updateMinCheckout()">
            </div>

            {{-- Check-out --}}
            <div>
                <label for="check_out" class="block text-[0.8rem] font-semibold text-slate-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('visitor.check_out') }}
                </label>
                <input type="date" id="check_out" name="check_out"
                       value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200
                              text-[0.875rem] text-slate-800 bg-slate-50
                              focus:outline-none focus:border-yellow-400 focus:ring-2
                              focus:ring-yellow-100 transition-all">
            </div>

            {{-- Button cari --}}
            <div>
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                               rounded-xl font-semibold text-[0.875rem] text-[#713f12]
                               border-none cursor-pointer transition-all duration-150 active:scale-95"
                        style="background:#eab308;"
                        onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                        onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('visitor.search') }}
                </button>
            </div>
        </form>

        {{-- Nights info --}}
        <div class="mt-3 flex items-center gap-2" id="nightsInfo">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="text-[0.78rem] text-slate-500" id="nightsText">
                {{ __('visitor.nights_info') }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     KAMAR SECTION
══════════════════════════════════════════════════ --}}
<section id="kamarSection" class="py-8 md:py-10">
    <div class="section-wrap">

        {{-- Section header --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ __('visitor.room_choices') }}</h2>
                <p class="text-[0.82rem] text-slate-500 mt-0.5">
                    {{ __('visitor.all_rooms_shown') }}
                </p>
            </div>
            @if(request('check_in') && request('check_out'))
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[0.78rem] font-semibold"
                     style="background:#fefce8;color:#713f12;border:1px solid #fef9c3;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse(request('check_in'))->format('d M') }}
                    → {{ \Carbon\Carbon::parse(request('check_out'))->format('d M Y') }}
                </div>
            @endif
        </div>

        {{-- Grid Kamar --}}
        @if($rooms->count() > 0)
            <div class="rooms-grid" id="roomsGrid">
                @foreach($rooms as $room)
                    @include('Visitors.partials.card', [
                        'room'     => $room,
                        'checkIn'  => request('check_in'),
                        'checkOut' => request('check_out'),
                    ])
                @endforeach
            </div>

            {{-- Empty filter result --}}
            <div class="empty-state hidden" id="emptyFilter">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="font-semibold text-slate-500 text-lg">Tidak ada kamar ditemukan</p>
                <p class="text-sm mt-1">Coba ganti filter atau tanggal menginap</p>
            </div>
        @else
            <div class="empty-state">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold text-slate-500 text-lg">Belum ada kamar tersedia</p>
                <p class="text-sm mt-1">Silakan hubungi kami untuk informasi lebih lanjut</p>
            </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     TENTANG / INFO SECTION
══════════════════════════════════════════════════ --}}
<section id="tentangSection" class="py-8 md:py-10 bg-white border-t border-slate-100">
    <div class="section-wrap">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl font-extrabold text-slate-900 mb-6 tracking-tight">Tentang Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Deskripsi --}}
                <div class="info-card md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background:#fefce8;">
                            <svg class="w-5 h-5" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    <h3 class="font-bold text-slate-900 text-[0.95rem]">{{ $hotel?->name ?? 'Penginapan' }}</h3>
                    </div>
                    <p class="text-[0.875rem] text-slate-600 leading-relaxed">
                        {{ $hotel?->description ?: 'Kami menyediakan pengalaman menginap yang nyaman dengan pelayanan terbaik untuk setiap tamu.' }}
                    </p>
                    @if($hotel?->address)
                        <div class="flex items-start gap-2 mt-3.5">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-[0.82rem] text-slate-500">{{ $hotel->address }}</p>
                        </div>
                    @endif
                </div>

                {{-- Kontak --}}
                <div class="info-card">
                    <h3 class="font-bold text-slate-900 text-[0.9rem] mb-3">Kontak</h3>
                    <div class="flex flex-col gap-3">
                        @if($hotel?->wa)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $hotel->wa) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl
                                      text-[0.82rem] font-semibold no-underline
                                      transition-all hover:scale-[1.02]"
                               style="background:#dcfce7;color:#15803d;">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.528 5.845L0 24l6.351-1.505A11.935 11.935 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.797 9.797 0 01-5.015-1.376l-.36-.214-3.726.883.897-3.633-.234-.371A9.818 9.818 0 012.182 12c0-5.413 4.405-9.818 9.818-9.818s9.818 4.405 9.818 9.818-4.405 9.818-9.818 9.818z"/>
                                </svg>
                                WhatsApp
                            </a>
                        @endif
                        @if($hotel?->email)
                            <a href="mailto:{{ $hotel->email }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl
                                      text-[0.82rem] font-semibold no-underline
                                      transition-all hover:scale-[1.02]"
                               style="background:#eff6ff;color:#1d4ed8;">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email
                            </a>
                        @endif
                        @if(!$hotel?->wa && !$hotel?->email)
                            <p class="text-[0.82rem] text-slate-400">Informasi kontak belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-slate-200 bg-white py-6">
    <div class="section-wrap text-center">
        <p class="text-[0.78rem] text-slate-400">
            &copy; {{ date('Y') }} {{ $hotel?->name ?? 'Penginapan' }}. All rights reserved.
        </p>
    </div>
</footer>

{{-- ══════════════════════════════════════════════════
     BOOKING MODAL
══════════════════════════════════════════════════ --}}
<div class="booking-modal-overlay" id="bookingModalOverlay"
     onclick="if(event.target===this) closeBookingModal()">
    <div class="booking-modal" id="bookingModal">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-extrabold text-slate-900 text-lg">Pesan Kamar</h3>
            <button type="button" onclick="closeBookingModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg
                           bg-slate-100 hover:bg-slate-200 border-none cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Info kamar --}}
        <div class="p-4 rounded-xl mb-5" style="background:#fefce8;border:1px solid #fef9c3;">
            <p class="font-bold text-slate-900 text-[0.95rem]" id="modalRoomName">—</p>
            <p class="text-[0.82rem] font-semibold mt-0.5" style="color:#eab308;" id="modalRoomPrice">—</p>
        </div>

        <form method="POST" action="#" id="bookingForm">
            @csrf
            <input type="hidden" name="room_uuid" id="modalRoomUuid">

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-[0.8rem] font-semibold text-slate-700 mb-1.5">Check In</label>
                    <input type="date" name="check_in" id="modalCheckIn"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-[0.875rem]
                                  bg-slate-50 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100"
                           value="{{ request('check_in', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}"
                           onchange="calcModalNights()">
                </div>
                <div>
                    <label class="block text-[0.8rem] font-semibold text-slate-700 mb-1.5">Check Out</label>
                    <input type="date" name="check_out" id="modalCheckOut"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-[0.875rem]
                                  bg-slate-50 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100"
                           value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           onchange="calcModalNights()">
                </div>
            </div>

            {{-- Nights summary --}}
            <div class="flex items-center justify-between px-4 py-3 rounded-xl mb-4
                        border border-slate-100" style="background:#f8fafc;">
                <span class="text-[0.82rem] text-slate-500" id="modalNightsLabel">1 malam</span>
                <span class="text-[0.875rem] font-bold text-slate-900" id="modalTotalPrice">—</span>
            </div>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3
                           rounded-xl font-bold text-[0.9rem] text-[#713f12]
                           border-none cursor-pointer transition-all active:scale-95"
                    style="background:#eab308;"
                    onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                    onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>
                Konfirmasi Pemesanan
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════════
   HERO SLIDER
═══════════════════════════════════════════════ */
(function () {
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.hero-dot');
    if (slides.length <= 1) return;

    let current = 0;
    let timer   = null;

    function goTo(idx) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (idx + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startAuto() {
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), 5000);
    }

    window.heroSlide = function (dir) { goTo(current + dir); startAuto(); };
    window.heroGoTo  = function (idx) { goTo(idx); startAuto(); };

    // Pause on hover
    const slider = document.getElementById('heroSlider');
    if (slider) {
        slider.addEventListener('mouseenter', () => clearInterval(timer));
        slider.addEventListener('mouseleave', startAuto);
    }

    // Touch swipe
    let tx = 0;
    if (slider) {
        slider.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
        slider.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - tx;
            if (Math.abs(dx) > 40) { goTo(current + (dx < 0 ? 1 : -1)); startAuto(); }
        }, { passive: true });
    }

    startAuto();
})();

/* ═══════════════════════════════════════════════
   SEARCH: date helpers
═══════════════════════════════════════════════ */
function updateMinCheckout() {
    const ci  = document.getElementById('check_in');
    const co  = document.getElementById('check_out');
    if (!ci || !co) return;
    const ciDate = new Date(ci.value);
    ciDate.setDate(ciDate.getDate() + 1);
    const minCo = ciDate.toISOString().split('T')[0];
    co.min = minCo;
    if (co.value <= ci.value) co.value = minCo;
    updateNightsInfo();
}

function updateNightsInfo() {
    const ci = document.getElementById('check_in')?.value;
    const co = document.getElementById('check_out')?.value;
    const el = document.getElementById('nightsText');
    if (!ci || !co || !el) return;
    const nights = Math.max(1, Math.round((new Date(co) - new Date(ci)) / 86400000));
    el.textContent = nights === 1 ? 'Menampilkan harga per malam (1 malam)' : `Menampilkan harga untuk ${nights} malam`;
}

document.addEventListener('DOMContentLoaded', updateNightsInfo);

/* ═══════════════════════════════════════════════
   FILTER TABS (client-side)
═══════════════════════════════════════════════ */
function filterRooms(type, btnEl) {
    // Update tab styles
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    const cards    = document.querySelectorAll('#roomsGrid > .vis-room-card');
    const emptyMsg = document.getElementById('emptyFilter');
    let visible    = 0;

    cards.forEach(card => {
        const isAvailable = card.dataset.available === '1';
        const show = type === 'all'
            || (type === 'available'   && isAvailable)
            || (type === 'unavailable' && !isAvailable);

        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    if (emptyMsg) emptyMsg.classList.toggle('hidden', visible > 0);
}

/* ═══════════════════════════════════════════════
   BOOKING MODAL
═══════════════════════════════════════════════ */
let _modalRoomPrice = 0;

function openBookingModal(uuid, name, priceStr) {
    document.getElementById('modalRoomUuid').value  = uuid;
    document.getElementById('modalRoomName').textContent  = name;
    document.getElementById('modalRoomPrice').textContent = priceStr + ' /malam';

    // Sync tanggal dari search
    const ci = document.getElementById('check_in')?.value;
    const co = document.getElementById('check_out')?.value;
    if (ci) document.getElementById('modalCheckIn').value  = ci;
    if (co) document.getElementById('modalCheckOut').value = co;

    // Extract numeric price dari priceStr (misal "Rp 350.000")
    _modalRoomPrice = parseInt(priceStr.replace(/\D/g, '')) || 0;

    calcModalNights();
    document.getElementById('bookingModalOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeBookingModal() {
    document.getElementById('bookingModalOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

function calcModalNights() {
    const ci = document.getElementById('modalCheckIn')?.value;
    const co = document.getElementById('modalCheckOut')?.value;
    if (!ci || !co) return;

    // Ensure checkout > checkin
    if (co <= ci) {
        const d = new Date(ci);
        d.setDate(d.getDate() + 1);
        document.getElementById('modalCheckOut').value = d.toISOString().split('T')[0];
    }

    const nights = Math.max(1, Math.round((new Date(co) - new Date(ci)) / 86400000));
    const label  = document.getElementById('modalNightsLabel');
    const total  = document.getElementById('modalTotalPrice');

    if (label) label.textContent = nights + ' malam';
    if (total && _modalRoomPrice) {
        const t = nights * _modalRoomPrice;
        total.textContent = 'Total: Rp ' + t.toLocaleString('id-ID');
    }
}

// Close modal on ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeBookingModal();
});
</script>
@endpush
