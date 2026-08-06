@extends('Visitors.layouts.app')

@section('title', $room->trans('name'))

@push('head')
<style>
    :root {
        --y: #eab308; --yd: #ca8a04; --yl: #facc15;
        --y50: #fefce8; --y100: #fef9c3; --ytext: #713f12;
    }

    /* Layout mobile: strip harga/CTA ditempatkan di atas bottom nav */
    @media (max-width: 767px) {
        .show-wrap { padding-bottom: 8rem; }
    }

    /* ─── Layout ─── */
    .show-wrap   { max-width: 1100px; margin: 0 auto; padding: 0 1.25rem 4rem; }
    @media (min-width: 1024px) { .show-wrap { padding: 0 2rem 4rem; } }

    /* ─── Room Price Strip — fixed di atas pub-bottombar (mobile only) ─── */
    .room-price-strip {
        display: none;
        position: fixed; left: 0.75rem; right: 0.75rem; bottom: calc(76px + env(safe-area-inset-bottom, 0px) + 0.5rem); z-index: 65;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 0.75rem 0.875rem;
        align-items: center; gap: 0.75rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    @media (max-width: 767px) { .room-price-strip { display: flex; } }
    .rps-price-wrap { flex: 1; min-width: 0; }
    .rps-label  { font-size: 0.65rem; color: #94a3b8; font-weight: 500; line-height: 1; margin-bottom: 0.1rem; }
    .rps-amount { font-size: 1.05rem; font-weight: 800; line-height: 1.2; font-variant-numeric: tabular-nums; }
    .rps-amount.disc  { color: #059669; }
    .rps-amount.plain { color: var(--y); }
    .rps-strike { font-size: 0.68rem; color: #94a3b8; text-decoration: line-through; font-weight: 400; }
    .rps-btn {
        flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;
        padding: 0.625rem 1.1rem; border-radius: 0.75rem;
        font-weight: 700; font-size: 0.82rem; border: none; cursor: pointer;
        background: var(--y); color: var(--ytext);
        text-decoration: none; white-space: nowrap;
        transition: background 0.15s, transform 0.1s;
        -webkit-tap-highlight-color: transparent;
    }
    .rps-btn:hover  { background: var(--yd); color: #fff; }
    .rps-btn:active { transform: scale(0.97); }
    .rps-btn.unavail { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

    /* ─── Gallery ─── */
    .gallery-main {
        position: relative; width: 100%; border-radius: 1.25rem;
        overflow: hidden; background: #f1f5f9;
        aspect-ratio: 16/9;
    }
    @media (max-width: 767px) { .gallery-main { aspect-ratio: 4/3; border-radius: 0.875rem; } }
    .gallery-main img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.45s ease, opacity 0.45s ease;
        will-change: transform, opacity;
    }
    .gallery-thumb-wrap {
        display: flex; gap: 0.5rem; margin-top: 0.625rem;
        overflow-x: auto; padding-bottom: 0.25rem;
        scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
    }
    .gallery-thumb {
        flex-shrink: 0; width: 80px; height: 60px;
        border-radius: 0.625rem; overflow: hidden;
        border: 2.5px solid transparent; cursor: pointer;
        transition: border-color 0.15s, transform 0.15s;
    }
    .gallery-thumb:hover  { transform: scale(1.04); }
    .gallery-thumb.active { border-color: var(--y); }
    .gallery-thumb img    { width: 100%; height: 100%; object-fit: cover; }

    /* Gallery arrows */
    .gal-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        z-index: 5; width: 38px; height: 38px; border-radius: 50%;
        background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(4px); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white; transition: background 0.15s;
    }
    .gal-arrow:hover { background: rgba(255,255,255,0.3); }
    .gal-arrow.prev { left: 0.875rem; }
    .gal-arrow.next { right: 0.875rem; }
    @media (max-width: 480px) {
        .gal-arrow { width: 36px; height: 36px; }
    }

    /* Photo counter */
    .gal-counter {
        position: absolute; bottom: 0.875rem; right: 0.875rem;
        padding: 0.25rem 0.625rem; border-radius: 0.5rem;
        background: rgba(0,0,0,0.5); color: white;
        font-size: 0.72rem; font-weight: 600;
        backdrop-filter: blur(4px);
    }

    /* ─── Sticky CTA (desktop) ─── */
    .sticky-cta {
        position: sticky; top: 80px; align-self: flex-start;
    }

    /* ─── Section card ─── */
    .detail-card {
        background: white; border-radius: 1.25rem; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 1rem;
    }
    .detail-card h3 {
        font-size: 0.95rem; font-weight: 700; color: #0f172a;
        margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .detail-card h3 .icon-dot {
        width: 8px; height: 8px; border-radius: 50%; background: var(--y); flex-shrink: 0;
    }

    /* ─── Facility chips ─── */
    .facility-chip {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.45rem 0.875rem; border-radius: 0.75rem;
        background: #f8fafc; border: 1px solid #e2e8f0;
        font-size: 0.8rem; font-weight: 500; color: #374151;
    }
    .facility-chip .qty-badge {
        background: var(--y100); color: var(--ytext);
        font-size: 0.68rem; font-weight: 700;
        padding: 0.1rem 0.375rem; border-radius: 0.375rem;
    }

    /* ─── Check time pill ─── */
    .time-pill {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 1rem; border-radius: 0.875rem;
        background: #f8fafc; border: 1px solid #e2e8f0;
        margin-bottom: 0.5rem;
    }
    .time-pill .time-val {
        font-size: 1.05rem; font-weight: 800;
        font-variant-numeric: tabular-nums; letter-spacing: 0.03em;
    }
    .time-pill .time-date {
        font-size: 0.72rem; color: #94a3b8; font-weight: 500;
    }
    .time-pill-ci  { border-color: #bbf7d0; background: #f0fdf4; }
    .time-pill-ci  .time-val { color: #15803d; }
    .time-pill-co  { border-color: #fecdd3; background: #fff1f2; }
    .time-pill-co  .time-val { color: #be185d; }

    /* ─── Price box ─── */
    .price-box {
        background: white; border-radius: 1.25rem; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    }

    /* ─── Status badge ─── */
    .status-badge-av   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-badge-unav { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }

    /* ─── Other rooms ─── */
    .other-card {
        display: flex; gap: 0.875rem; padding: 0.875rem;
        border-radius: 1rem; border: 1px solid #e2e8f0;
        background: white; text-decoration: none; color: inherit;
        transition: box-shadow 0.15s, transform 0.15s;
    }
    .other-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .other-card-thumb {
        width: 80px; height: 64px; border-radius: 0.625rem;
        overflow: hidden; flex-shrink: 0; background: var(--y50);
    }
    .other-card-thumb img { width: 100%; height: 100%; object-fit: cover; }

    /* ─── Breadcrumb ─── */
    .breadcrumb { display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap;
                  font-size: 0.8rem; color: #64748b; margin-bottom: 1.25rem; }
    .breadcrumb a { color: #64748b; text-decoration: none; }
    .breadcrumb a:hover { color: var(--ytext); }
    .breadcrumb .sep { color: #cbd5e1; }
    .breadcrumb .current { color: #0f172a; font-weight: 500; }

    /* ─── Surcharge pills ─── */
    .surcharge-pill {
        display: flex; align-items: flex-start; gap: 0.75rem;
        padding: 0.75rem 1rem; border-radius: 0.875rem;
        margin-bottom: 0.5rem; border: 1px solid;
    }
    .surcharge-pill-ci { border-color: #bbf7d0; background: #f0fdf4; }
    .surcharge-pill-co { border-color: #fecdd3; background: #fff1f2; }

    /* ─── Lightbox / Photo Modal ─── */
    .photo-modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.92);
        align-items: center; justify-content: center;
        flex-direction: column;
    }
    .photo-modal-overlay.show { display: flex; }
    .photo-modal-img {
        max-width: 92vw; max-height: 82vh;
        object-fit: contain; border-radius: 0.75rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        transition: opacity 0.2s;
    }
    .photo-modal-close {
        position: absolute; top: 1rem; right: 1rem;
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
        backdrop-filter: blur(6px); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white; transition: background 0.15s; z-index: 10;
    }
    .photo-modal-close:hover { background: rgba(255,255,255,0.3); }
    .photo-modal-counter {
        margin-top: 1rem; color: rgba(255,255,255,0.65);
        font-size: 0.82rem; font-weight: 600; letter-spacing: 0.04em;
    }
    .photo-modal-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(4px); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white; transition: background 0.15s; z-index: 10;
    }
    .photo-modal-arrow:hover { background: rgba(255,255,255,0.25); }
    .photo-modal-arrow.prev { left: 1rem; }
    .photo-modal-arrow.next { right: 1rem; }
    @media (max-width: 480px) {
        .photo-modal-arrow { width: 36px; height: 36px; }
        .photo-modal-arrow.prev { left: 0.5rem; }
        .photo-modal-arrow.next { right: 0.5rem; }
    }

    /* Kursor pointer pada gambar galeri agar user tahu bisa diklik */
    #galleryMain img { cursor: zoom-in; }
    .gallery-thumb { cursor: pointer; }

    /* ─── Date filter check-in/out schedule ─── */
    .schedule-date-filter {
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 1rem; padding: 0.625rem 0.875rem;
        border-radius: 0.875rem; background: #fefce8;
        border: 1px solid #fef9c3;
    }
    .schedule-date-filter label {
        font-size: 0.75rem; font-weight: 700; color: var(--ytext);
        white-space: nowrap; flex-shrink: 0;
    }
    .schedule-date-filter input[type="date"] {
        flex: 1; padding: 0.35rem 0.625rem; border-radius: 0.5rem;
        border: 1px solid #fef9c3; background: white;
        font-size: 0.78rem; color: #374151;
        focus-outline: none; min-width: 0;
    }
    .schedule-date-filter input[type="date"]:focus {
        outline: none; border-color: var(--y); box-shadow: 0 0 0 2px rgba(234,179,8,0.2);
    }
    .schedule-no-data {
        padding: 0.75rem 1rem; border-radius: 0.875rem;
        background: #f8fafc; border: 1px dashed #e2e8f0;
        font-size: 0.8rem; color: #94a3b8; text-align: center;
    }
</style>
@endpush

@section('content')

{{-- ── Semua foto digabung: cover dulu, lalu sisanya ── --}}
@php
    $allPhotos = $room->photos->sortByDesc('is_cover')->values();
    $totalPhotos = $allPhotos->count();
    // Array path foto untuk lightbox
    $photoUrls = $allPhotos->map(fn($p) => asset($p->path))->toJson();
@endphp

<div class="show-wrap pt-6 md:pt-8">

    {{-- ── Breadcrumb ── --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('index') }}">{{ __('visitor.breadcrumb_home') }}</a>
        <span class="sep">›</span>
        <a href="{{ route('index') }}#kamar">{{ __('visitor.breadcrumb_rooms') }}</a>
        <span class="sep">›</span>
        <span class="current">{{ $room->name }}</span>
    </nav>

    {{-- ── Main layout: kiri konten | kanan sticky CTA ── --}}
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

        {{-- ════════════════════════════════════════
             KOLOM KIRI — Galeri + Detail
        ════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- ── GALLERY ── --}}
            <div class="gallery-main" id="galleryMain">
                @if($totalPhotos > 0)
                    @foreach($allPhotos as $i => $photo)
                        <img src="{{ asset($photo->path) }}"
                             alt="{{ $room->name }} — Foto {{ $i + 1 }}"
                             id="galImg_{{ $i }}"
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-400
                                    {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
                             style="position:absolute;"
                             onclick="openPhotoModal({{ $i }})"
                             title="Klik untuk perbesar">
                    @endforeach

                    @if($totalPhotos > 1)
                        <button class="gal-arrow prev" onclick="galSlide(-1)" aria-label="Foto sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button class="gal-arrow next" onclick="galSlide(1)" aria-label="Foto berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div class="gal-counter" id="galCounter">1 / {{ $totalPhotos }}</div>
                    @endif
                @else
                    {{-- Fallback --}}
                    <div class="w-full h-full flex flex-col items-center justify-center gap-2"
                         style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
                        <svg class="w-14 h-14" style="color:rgba(0,0,0,0.18);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-[0.78rem] font-semibold" style="color:rgba(0,0,0,0.3);">{{ __('visitor.no_photo') }}</span>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($totalPhotos > 1)
                <div class="gallery-thumb-wrap" id="galThumbs">
                    @foreach($allPhotos as $i => $photo)
                        <button class="gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                                onclick="galGoTo({{ $i }})"
                                aria-label="Lihat foto {{ $i + 1 }}">
                            <img src="{{ asset($photo->path) }}" alt="Thumb {{ $i + 1 }}"
                                 loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- ── NAMA & STATUS ── --}}
            <div class="mt-5 flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        {{ $room->name }}
                    </h1>
                    <div class="flex items-center gap-2.5 mt-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[0.75rem] font-bold
                                     {{ $isAvailable ? 'status-badge-av' : 'status-badge-unav' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isAvailable ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $isAvailable ? __('visitor.room_status_available') : __('visitor.room_status_unavail') }}
                        </span>
                        <span class="text-[0.8rem] text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('visitor.capacity_label') }} {{ $room->capacity }} {{ __('visitor.persons') }}
                        </span>
                    </div>
                </div>
                {{-- Harga singkat di header (mobile helper) --}}
                <div class="lg:hidden text-right">
                    @if($hasDiscount)
                        <p class="text-[0.78rem] text-slate-400 line-through">{{ $originalPriceDisplay }}</p>
                        <p class="text-xl font-extrabold text-emerald-600">{{ $priceDisplay }}</p>
                    @else
                        <p class="text-xl font-extrabold" style="color:var(--y);">{{ $priceDisplay }}</p>
                    @endif
                    <p class="text-[0.72rem] text-slate-400">{{ __('visitor.per_night') }}</p>
                </div>
            </div>

            {{-- ── DESKRIPSI ── --}}
            @if($room->description)
                <div class="detail-card mt-5">
                    <h3><span class="icon-dot"></span> {{ __('visitor.room_description') }}</h3>
                    <p class="text-[0.875rem] text-slate-600 leading-relaxed whitespace-pre-line">{{ $room->description }}</p>
                </div>
            @endif

            {{-- ── FASILITAS ── --}}
            @if($room->facilities->count() > 0)
                <div class="detail-card">
                    <h3>
                        <span class="icon-dot"></span> {{ __('visitor.facilities') }}
                        <span class="ml-auto text-[0.72rem] font-semibold px-2 py-0.5 rounded-full"
                              style="background:var(--y100);color:var(--ytext);">
                            {{ $room->facilities->count() }} item
                        </span>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($room->facilities as $facility)
                            <div class="facility-chip">
                                <svg class="w-3.5 h-3.5 shrink-0" style="color:var(--y);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ $facility->getTransName() }}</span>
                                @if($facility->qty > 1)
                                    <span class="qty-badge">×{{ $facility->qty }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    {{-- Deskripsi fasilitas jika ada --}}
                    @php $facilitiesWithDesc = $room->facilities->filter(fn($f) => $f->getTransDescription()); @endphp
                    @if($facilitiesWithDesc->count() > 0)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-[0.75rem] font-semibold text-slate-500 uppercase tracking-wide mb-2.5">{{ __('visitor.facility_detail') }}</p>
                            <div class="flex flex-col gap-1.5">
                                @foreach($facilitiesWithDesc as $facility)
                                    <div class="flex items-start gap-2 text-[0.8rem] text-slate-600">
                                        <span class="font-semibold text-slate-800 shrink-0">{{ $facility->getTransName() }}:</span>
                                        <span>{{ $facility->getTransDescription() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── JADWAL CHECK-IN & CHECK-OUT ── --}}
            @php
                // Kumpulkan semua tanggal unik dari kedua setting
                $allDates = $checkInSettings->pluck('date')
                    ->merge($checkOutSettings->pluck('date'))
                    ->unique()
                    ->sort()
                    ->values();

                // Tanggal awal yang dipilih: ambil dari query 'schedule_date', fallback tanggal pertama, fallback hari ini
                $selectedScheduleDate = request('schedule_date') ?? ($allDates->first()?->toDateString() ?? now()->toDateString());

                // Filter setting berdasarkan tanggal terpilih
                $filteredCheckIn  = $checkInSettings->filter(fn($s) => $s->date->toDateString() === $selectedScheduleDate)->values();
                $filteredCheckOut = $checkOutSettings->filter(fn($s) => $s->date->toDateString() === $selectedScheduleDate)->values();
            @endphp
            <div class="detail-card">
                <h3><span class="icon-dot"></span> {{ __('visitor.checkin_schedule') }}</h3>

                {{-- ── Filter Tanggal ── --}}
                @if($allDates->count() > 0)
                    <div class="schedule-date-filter">
                        <svg class="w-3.5 h-3.5 shrink-0" style="color:var(--y);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <label for="scheduleFilter">{{ __('visitor.select_date') }}</label>
                        <select id="scheduleFilter"
                                onchange="filterSchedule(this.value)"
                                style="flex:1;padding:0.35rem 0.625rem;border-radius:0.5rem;border:1px solid #fef9c3;background:white;font-size:0.78rem;color:#374151;outline:none;min-width:0;cursor:pointer;">
                            @foreach($allDates as $date)
                                <option value="{{ $date->toDateString() }}"
                                    {{ $date->toDateString() === $selectedScheduleDate ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="scheduleGrid">

                    {{-- Check-In --}}
                    <div id="checkInSchedule">
                        <p class="text-[0.75rem] font-bold text-slate-500 uppercase tracking-wide mb-2.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Check-In
                        </p>

                        {{-- Data dari database untuk tanggal terpilih --}}
                        @if($filteredCheckIn->count() > 0)
                            @foreach($filteredCheckIn as $setting)
                                <div class="time-pill time-pill-ci schedule-pill"
                                     data-date="{{ $setting->date->toDateString() }}"
                                     data-type="check_in">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         style="background:#dcfce7;">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="time-val">{{ $setting->formatted_time }}</p>
                                        <p class="time-date">
                                            {{ \Carbon\Carbon::parse($setting->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </p>
                                        @if($setting->notes)
                                            <p class="text-[0.7rem] text-slate-500 mt-0.5">{{ $setting->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @elseif($checkInSettings->count() > 0)
                            {{-- Ada data tapi bukan di tanggal ini --}}
                            <div class="schedule-no-data" id="noCheckInData">
                                {{ __('visitor.no_checkin_date') }}
                            </div>
                        @else
                            {{-- Fallback default --}}
                            <div class="time-pill time-pill-ci">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background:#dcfce7;">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="time-val">{{ $defaultCheckInTime }}</p>
                                    <p class="time-date">{{ __('visitor.default_checkin') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Check-Out --}}
                    <div id="checkOutSchedule">
                        <p class="text-[0.75rem] font-bold text-slate-500 uppercase tracking-wide mb-2.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Check-Out
                        </p>

                        @if($filteredCheckOut->count() > 0)
                            @foreach($filteredCheckOut as $setting)
                                <div class="time-pill time-pill-co schedule-pill"
                                     data-date="{{ $setting->date->toDateString() }}"
                                     data-type="check_out">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         style="background:#ffe4e6;">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="time-val">{{ $setting->formatted_time }}</p>
                                        <p class="time-date">
                                            {{ \Carbon\Carbon::parse($setting->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </p>
                                        @if($setting->notes)
                                            <p class="text-[0.7rem] text-slate-500 mt-0.5">{{ $setting->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @elseif($checkOutSettings->count() > 0)
                            <div class="schedule-no-data" id="noCheckOutData">
                                {{ __('visitor.no_checkout_date') }}
                            </div>
                        @else
                            <div class="time-pill time-pill-co">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background:#ffe4e6;">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="time-val">{{ $defaultCheckOutTime }}</p>
                                    <p class="time-date">{{ __('visitor.default_checkout') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Semua data setting (hidden, untuk client-side filter) --}}
                @if($allDates->count() > 0)
                @php
                    $scheduleJsonData = json_encode([
                        'checkIn'  => $checkInSettings->map(fn($s) => [
                            'date'       => $s->date->toDateString(),
                            'time'       => $s->formatted_time,
                            'date_label' => \Carbon\Carbon::parse($s->date)->locale('id')->isoFormat('D MMMM YYYY'),
                            'notes'      => $s->notes,
                        ])->values()->all(),
                        'checkOut' => $checkOutSettings->map(fn($s) => [
                            'date'       => $s->date->toDateString(),
                            'time'       => $s->formatted_time,
                            'date_label' => \Carbon\Carbon::parse($s->date)->locale('id')->isoFormat('D MMMM YYYY'),
                            'notes'      => $s->notes,
                        ])->values()->all(),
                        'defaultCheckIn'  => $defaultCheckInTime,
                        'defaultCheckOut' => $defaultCheckOutTime,
                    ]);
                @endphp
                <script>
                    window.__scheduleData = {!! $scheduleJsonData !!};
                </script>
                @endif
            </div>

            {{-- ── BIAYA TAMBAHAN (Early CI / Late CO) ── --}}
            @if($earlyCheckinFees->count() > 0 || $lateCheckoutFees->count() > 0)
            <div class="detail-card">
                    <h3>
                    <span class="icon-dot" style="background:#f59e0b;"></span>
                    {{ __('visitor.extra_charge') }}
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Early Check-In Fees --}}
                    @if($earlyCheckinFees->count() > 0)
                    <div>
                        <p class="text-[0.72rem] font-bold text-slate-400 uppercase tracking-wide mb-2.5
                                   flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Early Check-In
                        </p>
                        <div class="flex flex-col gap-2">
                            @foreach($earlyCheckinFees as $fee)
                            <div class="surcharge-pill surcharge-pill-ci">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background:#dcfce7;">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[0.8rem] font-bold text-slate-800 leading-snug">
                                        {{ $fee->auto_label }}
                                    </p>
                                    <p class="text-[0.72rem] text-slate-500 mt-0.5">
                                        {{ __('visitor.checkin_before') }}
                                        <strong class="text-emerald-700">{{ $fee->formatted_threshold }}</strong>
                                        {{ __('visitor.add_fee') }}
                                        <strong class="text-emerald-700">{{ $fee->formatted_fee }}</strong>
                                        @if($fee->fee_type === 'percent')
                                            {{ __('visitor.from_price_night') }}
                                        @endif
                                    </p>
                                    @if($fee->description)
                                        <p class="text-[0.68rem] text-slate-400 mt-0.5 leading-relaxed">
                                            {{ $fee->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Late Check-Out Fees --}}
                    @if($lateCheckoutFees->count() > 0)
                    <div>
                        <p class="text-[0.72rem] font-bold text-slate-400 uppercase tracking-wide mb-2.5
                                   flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Late Check-Out
                        </p>
                        <div class="flex flex-col gap-2">
                            @foreach($lateCheckoutFees as $fee)
                            <div class="surcharge-pill surcharge-pill-co">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background:#ffe4e6;">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[0.8rem] font-bold text-slate-800 leading-snug">
                                        {{ $fee->auto_label }}
                                    </p>
                                    <p class="text-[0.72rem] text-slate-500 mt-0.5">
                                        {{ __('visitor.checkout_after') }}
                                        <strong class="text-rose-600">{{ $fee->formatted_threshold }}</strong>
                                        {{ __('visitor.add_fee') }}
                                        <strong class="text-rose-600">{{ $fee->formatted_fee }}</strong>
                                        @if($fee->fee_type === 'percent')
                                            {{ __('visitor.from_price_night') }}
                                        @endif
                                    </p>
                                    @if($fee->description)
                                        <p class="text-[0.68rem] text-slate-400 mt-0.5 leading-relaxed">
                                            {{ $fee->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                <p class="mt-4 text-[0.72rem] text-slate-400 flex items-start gap-1.5 pt-3 border-t border-slate-100">
                    <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('visitor.extra_charge_note') }}
                </p>
            </div>
            @endif

            {{-- ── KAMAR LAIN (Rekomendasi) ── --}}
            @if($otherRooms->count() > 0)
                <div class="detail-card">
                    <h3><span class="icon-dot"></span> {{ __('visitor.other_rooms') }}</h3>
                    <div class="flex flex-col gap-2.5">
                        @foreach($otherRooms as $other)
                            <a href="{{ route('room.show', $other->uuid) }}@if($checkIn || $checkOut)?{{ http_build_query(array_filter(['check_in'=>$checkIn,'check_out'=>$checkOut])) }}@endif"
                               class="other-card">
                                <div class="other-card-thumb">
                                    @if($other->coverPhoto)
                                        <img src="{{ asset($other->coverPhoto->path) }}"
                                             alt="{{ $other->name }}" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center"
                                             style="background:linear-gradient(135deg,#eab308,#facc15);">
                                            <svg class="w-6 h-6" style="color:rgba(0,0,0,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 text-[0.875rem] truncate">{{ $other->name }}</p>
                                    <p class="text-[0.75rem] text-slate-500 mt-0.5">{{ $other->capacity }} {{ __('visitor.persons') }}</p>
                                    <p class="text-[0.875rem] font-extrabold mt-1" style="color:var(--y);">
                                        {{ $other->_priceDisplay }}
                                        <span class="text-[0.7rem] font-normal text-slate-400">{{ __('visitor.per_night') }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center shrink-0">
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- /kolom kiri --}}

        {{-- ════════════════════════════════════════
             KOLOM KANAN — Sticky Price Box
        ════════════════════════════════════════ --}}
        <div class="w-full lg:w-[340px] shrink-0 sticky-cta">
            <div class="price-box" id="priceBoxAnchor">

                {{-- Harga --}}
                <div class="mb-4 pb-4 border-b border-slate-100">
                    @if($hasDiscount)
                        <p class="text-[0.8rem] text-slate-400 line-through mb-0.5">{{ $originalPriceDisplay }}</p>
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <p class="text-[1.75rem] font-extrabold text-emerald-600 leading-none">
                                {{ $priceDisplay }}
                            </p>
                            <span class="text-[0.8rem] text-slate-400">{{ __('visitor.per_night') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                         text-[0.68rem] font-bold bg-red-500 text-white">
                                {{ __('visitor.save_badge') }} {{ $room->formatted_discount }}
                            </span>
                            @if($room->discount_min_nights > 0)
                                <span class="text-[0.7rem] text-slate-400">
                                    {{ __('visitor.min_nights', ['n' => $room->discount_min_nights]) }}
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <p class="text-[1.75rem] font-extrabold leading-none" style="color:var(--y);">
                                {{ $priceDisplay }}
                            </p>
                            <span class="text-[0.8rem] text-slate-400">{{ __('visitor.per_night') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Ringkasan tanggal + malam --}}
                @if($checkIn && $checkOut)
                    <div class="mb-4 p-3 rounded-xl border border-slate-100" style="background:#f8fafc;">
                        <div class="grid grid-cols-2 gap-2 text-[0.8rem] mb-2">
                            <div>
                                <p class="text-slate-400 font-medium">Check-In</p>
                                <p class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($checkIn)->locale('id')->isoFormat('D MMM YYYY') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium">Check-Out</p>
                                <p class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($checkOut)->locale('id')->isoFormat('D MMM YYYY') }}
                                </p>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 pt-2 flex items-center justify-between text-[0.8rem]">
                            <span class="text-slate-500">{{ __('visitor.nights_count', ['n' => $nights]) }} × {{ $priceDisplay }}</span>
                            <span class="font-bold text-slate-900">
                                Rp {{ number_format($discountedPrice * $nights, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($totalSaving > 0)
                            <div class="flex items-center justify-between text-[0.78rem] mt-1">
                                <span class="text-emerald-600">{{ __('visitor.save_total') }}</span>
                                <span class="font-bold text-emerald-600">
                                    – Rp {{ number_format($totalSaving, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Status kamar --}}
                <div class="flex items-center gap-2 mb-4 px-3 py-2.5 rounded-xl
                            {{ $isAvailable ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200' }}">
                    <div class="w-2 h-2 rounded-full {{ $isAvailable ? 'bg-emerald-500' : 'bg-red-500' }} shrink-0
                                {{ $isAvailable ? 'animate-pulse' : '' }}"></div>
                    <p class="text-[0.8rem] font-semibold {{ $isAvailable ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $isAvailable ? __('visitor.room_available_msg') : __('visitor.room_unavailable_msg') }}
                    </p>
                </div>

                {{-- Tombol pesan / CTA --}}
                @if($isAvailable)
                    @auth
                        <a href="{{ route('booking.create', $room->uuid) }}{{ ($checkIn ?? '') || ($checkOut ?? '') ? '?' . http_build_query(array_filter(['check_in' => $checkIn ?? '', 'check_out' => $checkOut ?? ''])) : '' }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3.5
                                  rounded-xl font-bold text-[0.9rem] text-[#713f12] no-underline
                                  transition-all active:scale-95"
                           style="background:var(--y);"
                           onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                           onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('visitor.book_now') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3.5
                                  rounded-xl font-bold text-[0.9rem] text-[#713f12] no-underline
                                  transition-all active:scale-95"
                           style="background:var(--y);"
                           onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                           onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            {{ __('visitor.login_to_book') }}
                        </a>
                    @endauth
                @else
                    <div class="w-full flex items-center justify-center gap-2 px-4 py-3.5
                                rounded-xl font-bold text-[0.875rem] bg-slate-100 text-slate-400
                                cursor-not-allowed select-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        {{ __('visitor.not_available') }}
                    </div>
                @endif

                {{-- Ubah tanggal --}}
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <p class="text-[0.75rem] font-semibold text-slate-500 mb-2">{{ __('visitor.change_dates') }}</p>
                    <form method="GET" action="{{ route('room.show', $room->uuid) }}"
                          class="flex flex-col gap-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[0.7rem] text-slate-400 mb-1">{{ __('visitor.check_in') }}</label>
                                <input type="date" name="check_in"
                                       value="{{ $checkIn ?? date('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-2.5 py-2 rounded-lg border border-slate-200
                                              text-[0.78rem] bg-slate-50 focus:outline-none
                                              focus:border-yellow-400 focus:ring-1 focus:ring-yellow-100">
                            </div>
                            <div>
                                <label class="block text-[0.7rem] text-slate-400 mb-1">{{ __('visitor.check_out') }}</label>
                                <input type="date" name="check_out"
                                       value="{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="w-full px-2.5 py-2 rounded-lg border border-slate-200
                                              text-[0.78rem] bg-slate-50 focus:outline-none
                                              focus:border-yellow-400 focus:ring-1 focus:ring-yellow-100">
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full py-2 rounded-lg text-[0.78rem] font-semibold
                                       border border-slate-200 bg-white text-slate-600
                                       hover:bg-slate-50 transition-colors cursor-pointer">
                            {{ __('visitor.update_price') }}
                        </button>
                    </form>
                </div>

            </div>{{-- /price-box --}}
        </div>{{-- /kolom kanan --}}

    </div>{{-- /flex layout --}}
</div>{{-- /show-wrap --}}

{{-- ══════════════════════════════════════════════
     ROOM PRICE STRIP — mobile only, menempel di atas pub-bottombar
══════════════════════════════════════════════ --}}
<div class="room-price-strip" id="roomPriceStrip" aria-label="Harga kamar">

    {{-- Harga ringkas --}}
    <div class="rps-price-wrap">
        <p class="rps-label">{{ __('visitor.per_night') }}</p>
        @if($hasDiscount)
            <p class="rps-strike">{{ $originalPriceDisplay }}</p>
            <p class="rps-amount disc">{{ $priceDisplay }}</p>
        @else
            <p class="rps-amount plain">{{ $priceDisplay }}</p>
        @endif
    </div>

    {{-- Tombol CTA --}}
    @if($isAvailable)
        @auth
            <button type="button" class="rps-btn"
                    onclick="document.getElementById('priceBoxAnchor').scrollIntoView({behavior:'smooth',block:'center'})">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ __('visitor.book_now_mobile') }}
            </button>
        @else
            <a href="{{ route('login') }}" class="rps-btn">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('visitor.login_to_book_short') }}
            </a>
        @endauth
    @else
        <div class="rps-btn unavail">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('visitor.not_available') }}
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════
     PHOTO LIGHTBOX MODAL
══════════════════════════════════════════════ --}}
@if($totalPhotos > 0)
<div class="photo-modal-overlay" id="photoModalOverlay"
     onclick="if(event.target===this||event.target.id==='photoModalOverlay') closePhotoModal()"
     role="dialog" aria-modal="true" aria-label="Lihat foto">

    {{-- Tombol tutup --}}
    <button class="photo-modal-close" onclick="closePhotoModal()" aria-label="Tutup foto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Panah kiri --}}
    @if($totalPhotos > 1)
    <button class="photo-modal-arrow prev" onclick="photoModalSlide(-1)" aria-label="Foto sebelumnya">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    @endif

    {{-- Gambar --}}
    <img id="photoModalImg"
         src=""
         alt="{{ $room->name }}"
         class="photo-modal-img">

    {{-- Panah kanan --}}
    @if($totalPhotos > 1)
    <button class="photo-modal-arrow next" onclick="photoModalSlide(1)" aria-label="Foto berikutnya">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    @endif

    {{-- Counter --}}
    <p class="photo-modal-counter" id="photoModalCounter">1 / {{ $totalPhotos }}</p>
</div>
@endif

@endsection

@push('scripts')
@php
$showLang = [
    'no_checkin'  => __('visitor.no_checkin_date'),
    'no_checkout' => __('visitor.no_checkout_date'),
    'default_ci'  => __('visitor.default_checkin'),
    'default_co'  => __('visitor.default_checkout'),
];
@endphp
<script>
window.__showLang = @json($showLang);
/* ═══════════════════════════════════════════════
   PHOTO LIGHTBOX MODAL
═══════════════════════════════════════════════ */
(function () {
    const photoUrls  = {!! $photoUrls !!};
    const totalModal = photoUrls.length;
    let modalCurrent = 0;

    const overlay  = document.getElementById('photoModalOverlay');
    const imgEl    = document.getElementById('photoModalImg');
    const counterEl= document.getElementById('photoModalCounter');

    function updateModal(idx) {
        modalCurrent = (idx + totalModal) % totalModal;
        if (imgEl)     imgEl.src = photoUrls[modalCurrent];
        if (counterEl) counterEl.textContent = (modalCurrent + 1) + ' / ' + totalModal;
    }

    window.openPhotoModal = function (startIdx) {
        if (!overlay) return;
        updateModal(startIdx ?? 0);
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closePhotoModal = function () {
        if (!overlay) return;
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    };

    window.photoModalSlide = function (dir) {
        updateModal(modalCurrent + dir);
    };

    // Keyboard: ESC menutup, arrow kiri/kanan navigasi
    document.addEventListener('keydown', function (e) {
        if (!overlay?.classList.contains('show')) return;
        if (e.key === 'Escape')      closePhotoModal();
        if (e.key === 'ArrowLeft')   photoModalSlide(-1);
        if (e.key === 'ArrowRight')  photoModalSlide(1);
    });

    // Swipe support di modal
    if (overlay) {
        let tx = 0;
        overlay.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
        overlay.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - tx;
            if (Math.abs(dx) > 40) photoModalSlide(dx < 0 ? 1 : -1);
        }, { passive: true });
    }
})();

/* ═══════════════════════════════════════════════
   GALLERY (main gallery navigation)
═══════════════════════════════════════════════ */
(function () {
    const total  = {{ $totalPhotos }};
    if (total <= 1) return;

    const imgs   = Array.from({length: total}, (_, i) => document.getElementById('galImg_' + i));
    const thumbs = document.querySelectorAll('.gallery-thumb');
    const counter = document.getElementById('galCounter');
    let current   = 0;

    function goTo(idx) {
        const prev = current;
        current = (idx + total) % total;

        if (imgs[prev]) {
            imgs[prev].style.opacity = '0';
            imgs[prev].style.transform = 'translateX(-16px) scale(0.98)';
        }
        thumbs[prev]?.classList.remove('active');

        if (imgs[current]) {
            imgs[current].style.opacity = '1';
            imgs[current].style.transform = 'translateX(0) scale(1)';
        }
        thumbs[current]?.classList.add('active');
        if (counter) counter.textContent = (current + 1) + ' / ' + total;

        // Scroll thumbnail ke posisi aktif
        thumbs[current]?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    window.galSlide = function (dir) { goTo(current + dir); };
    window.galGoTo  = function (idx) { goTo(idx); };

    // Geser otomatis setiap 8 detik secara berulang
    setInterval(function () {
        galSlide(1);
    }, 8000);

    // Touch swipe di galeri utama
    const main = document.getElementById('galleryMain');
    if (main) {
        let tx = 0;
        main.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
        main.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - tx;
            if (Math.abs(dx) > 40) galSlide(dx < 0 ? 1 : -1);
        }, { passive: true });
    }
})();

/* ═══════════════════════════════════════════════
   FILTER JADWAL CHECK-IN / CHECK-OUT
═══════════════════════════════════════════════ */
function filterSchedule(selectedDate) {
    const data = window.__scheduleData;
    if (!data) return;

    const ciContainer  = document.getElementById('checkInSchedule');
    const coContainer  = document.getElementById('checkOutSchedule');
    if (!ciContainer || !coContainer) return;

    // Filter data berdasarkan tanggal terpilih
    const ciData = data.checkIn.filter(s => s.date === selectedDate);
    const coData = data.checkOut.filter(s => s.date === selectedDate);

    // Rebuild check-in pills
    const ciLabel = ciContainer.querySelector('p');
    ciContainer.innerHTML = '';
    ciContainer.appendChild(ciLabel);

    if (ciData.length > 0) {
        ciData.forEach(s => {
            ciContainer.insertAdjacentHTML('beforeend', `
                <div class="time-pill time-pill-ci schedule-pill" data-date="${s.date}" data-type="check_in">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#dcfce7;">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="time-val">${s.time}</p>
                        <p class="time-date">${s.date_label}</p>
                        ${s.notes ? `<p class="text-[0.7rem] text-slate-500 mt-0.5">${s.notes}</p>` : ''}
                    </div>
                </div>`);
        });
    } else if (data.checkIn.length > 0) {
        ciContainer.insertAdjacentHTML('beforeend',
            `<div class="schedule-no-data">${window.__showLang.no_checkin}</div>`);
    } else {
        ciContainer.insertAdjacentHTML('beforeend', `
            <div class="time-pill time-pill-ci">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#dcfce7;">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="time-val">${data.defaultCheckIn}</p>
                    <p class="time-date">${window.__showLang.default_ci}</p>
                </div>
            </div>`);
    }

    // Rebuild check-out pills
    const coLabel = coContainer.querySelector('p');
    coContainer.innerHTML = '';
    coContainer.appendChild(coLabel);

    if (coData.length > 0) {
        coData.forEach(s => {
            coContainer.insertAdjacentHTML('beforeend', `
                <div class="time-pill time-pill-co schedule-pill" data-date="${s.date}" data-type="check_out">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#ffe4e6;">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="time-val">${s.time}</p>
                        <p class="time-date">${s.date_label}</p>
                        ${s.notes ? `<p class="text-[0.7rem] text-slate-500 mt-0.5">${s.notes}</p>` : ''}
                    </div>
                </div>`);
        });
    } else if (data.checkOut.length > 0) {
        coContainer.insertAdjacentHTML('beforeend',
            `<div class="schedule-no-data">${window.__showLang.no_checkout}</div>`);
    } else {
        coContainer.insertAdjacentHTML('beforeend', `
            <div class="time-pill time-pill-co">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#ffe4e6;">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="time-val">${data.defaultCheckOut}</p>
                    <p class="time-date">${window.__showLang.default_co}</p>
                </div>
            </div>`);
    }
}

/* ═══════════════════════════════════════════════
   DATE: pastikan checkout > checkin di form
═══════════════════════════════════════════════ */
(function () {
    const ci = document.querySelectorAll('input[name="check_in"]');
    const co = document.querySelectorAll('input[name="check_out"]');
    ci.forEach((inp, i) => {
        inp.addEventListener('change', function () {
            if (!co[i]) return;
            const d = new Date(this.value);
            d.setDate(d.getDate() + 1);
            const minVal = d.toISOString().split('T')[0];
            co[i].min = minVal;
            if (co[i].value <= this.value) co[i].value = minVal;
        });
    });
})();
</script>
@endpush
