@extends('Visitors.layouts.app')

@section('title', 'Booking Kamar')

@push('head')
<style>
    :root {
        --y: #eab308; --yd: #ca8a04; --yl: #facc15;
        --y50: #fefce8; --y100: #fef9c3; --ytext: #713f12;
    }

    /* ─── Page wrapper ─── */
    .booking-wrap {
        max-width: 960px;
        margin: 0 auto;
        padding: 2rem 1.25rem 6rem;
    }
    @media (min-width: 768px) {
        .booking-wrap { padding: 2.5rem 2rem 3rem; }
    }

    /* ─── Card base ─── */
    .bk-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .bk-card-hdr {
        display: flex; align-items: center; gap: .75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .bk-card-hdr-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .bk-card-title { font-size: .9rem; font-weight: 700; color: #0f172a; }
    .bk-card-sub   { font-size: .72rem; color: #94a3b8; margin-top: .1rem; }
    .bk-card-body  { padding: 1.25rem; }

    /* ─── Room summary ─── */
    .room-thumb {
        width: 100%; height: 160px; border-radius: .875rem;
        object-fit: cover; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
    }

    /* ─── Room meta badges ─── */
    .room-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .2rem .6rem; border-radius: 9999px;
        font-size: .68rem; font-weight: 700;
    }

    /* ─── Date inputs ─── */
    .date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .875rem;
    }
    @media (max-width: 480px) {
        .date-grid { grid-template-columns: 1fr; }
    }
    .bk-input-label {
        display: block; font-size: .78rem; font-weight: 700;
        color: #374151; margin-bottom: .4rem;
    }
    .bk-input {
        width: 100%; padding: .625rem .875rem;
        border: 1px solid #e2e8f0; border-radius: .75rem;
        font-size: .875rem; color: #1e293b; background: #f8fafc;
        outline: none; transition: border .15s, box-shadow .15s;
        font-family: 'Inter', sans-serif;
    }
    .bk-input:focus {
        border-color: var(--yl);
        box-shadow: 0 0 0 3px rgba(234,179,8,.15);
    }

    /* ─── Duration badge ─── */
    .duration-badge {
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .5rem 1rem; border-radius: .75rem;
        background: var(--y50); border: 1px solid var(--y100);
        font-size: .8rem; font-weight: 700; color: var(--ytext);
        margin-top: .875rem;
    }

    /* ─── Payment method card ─── */
    .pay-option {
        border: 2px solid #e2e8f0;
        border-radius: 1.125rem;
        padding: 1.125rem 1.25rem;
        cursor: pointer;
        transition: border-color .18s, background .18s, box-shadow .18s, transform .12s;
        position: relative;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .pay-option:hover {
        border-color: var(--yl);
        background: var(--y50);
    }
    .pay-option.selected {
        border-color: var(--y);
        background: var(--y50);
        box-shadow: 0 0 0 3px rgba(234,179,8,.18);
    }
    .pay-option:active { transform: scale(.98); }
    .pay-radio {
        position: absolute; top: 1rem; right: 1.125rem;
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid #cbd5e1; background: #fff;
        display: flex; align-items: center; justify-content: center;
        transition: border-color .15s, background .15s;
        flex-shrink: 0;
    }
    .pay-option.selected .pay-radio {
        border-color: var(--y);
        background: var(--y);
    }
    .pay-radio-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #fff;
        opacity: 0; transition: opacity .15s;
    }
    .pay-option.selected .pay-radio-dot { opacity: 1; }


    .pay-icon-wrap {
        width: 44px; height: 44px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: background .15s;
    }
    .pay-label   { font-size: .9rem; font-weight: 800; color: #0f172a; margin-bottom: .15rem; }
    .pay-desc    { font-size: .75rem; color: #64748b; line-height: 1.45; }
    .pay-tag     {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .15rem .5rem; border-radius: .375rem;
        font-size: .65rem; font-weight: 700; margin-top: .5rem;
    }

    /* ─── Price summary ─── */
    .price-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: .45rem 0; border-bottom: 1px solid #f1f5f9;
        font-size: .85rem;
    }
    .price-row:last-child { border-bottom: none; }
    .price-row .lbl  { color: #64748b; }
    .price-row .val  { font-weight: 600; color: #0f172a; }
    .price-row.total .lbl { font-weight: 800; color: #0f172a; font-size: .9rem; }
    .price-row.total .val { font-weight: 800; color: var(--ytext); font-size: 1rem; }
    .price-row.dp   .val  { color: #16a34a; font-weight: 700; }

    /* ─── Desktop CTA ─── */
    .bk-btn-primary {
        width: 100%; padding: .875rem;
        border-radius: .875rem; border: none; cursor: pointer;
        font-size: 1rem; font-weight: 800; letter-spacing: .01em;
        background: var(--y); color: var(--ytext);
        transition: background .15s, color .15s, transform .12s, box-shadow .15s;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
    }
    .bk-btn-primary:hover  { background: var(--yd); color: #fff; box-shadow: 0 4px 16px rgba(234,179,8,.35); }
    .bk-btn-primary:active { transform: scale(.97); }
    .bk-btn-primary:disabled {
        background: #e2e8f0; color: #94a3b8; cursor: not-allowed;
        box-shadow: none; transform: none;
    }
    .bk-btn-outline {
        width: 100%; padding: .75rem;
        border-radius: .875rem; border: 1.5px solid #e2e8f0; cursor: pointer;
        font-size: .875rem; font-weight: 600; color: #64748b;
        background: #fff; transition: background .15s, color .15s, border-color .15s;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        margin-top: .625rem;
    }
    .bk-btn-outline:hover { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }

    /* ─── Mobile sticky CTA bar ─── */
    .bk-mobile-cta {
        display: none;
        position: fixed; bottom: 68px; left: 0; right: 0; z-index: 55;
        padding: .875rem 1rem;
        background: rgba(255,255,255,.97);
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 20px rgba(0,0,0,.08);
        padding-bottom: calc(.875rem + env(safe-area-inset-bottom, 0px));
    }
    @media (max-width: 767px) {
        .bk-mobile-cta { display: block; }
        .bk-desktop-cta { display: none; }
        .booking-wrap { padding-bottom: 9rem; }
    }

    /* ─── Divider ─── */
    .pay-divider {
        display: flex; align-items: center; gap: .75rem;
        margin: .875rem 0; color: #94a3b8; font-size: .75rem; font-weight: 600;
    }
    .pay-divider::before, .pay-divider::after {
        content: ''; flex: 1; height: 1px; background: #e2e8f0;
    }

    /* ─── PG logos strip ─── */
    .pg-logos {
        display: flex; align-items: center; gap: .625rem; flex-wrap: wrap;
        margin-top: .75rem;
    }
    .pg-logo {
        padding: .25rem .625rem; border-radius: .5rem;
        border: 1px solid #e2e8f0; background: #f8fafc;
        font-size: .65rem; font-weight: 700; color: #475569;
        letter-spacing: .03em;
    }

    /* ─── Stepper ─── */
    .bk-steps {
        display: flex; align-items: center; gap: 0;
        margin-bottom: 1.5rem;
    }
    .bk-step {
        display: flex; align-items: center; gap: .5rem;
        flex: 1;
    }
    .bk-step-circle {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800; flex-shrink: 0;
        transition: background .2s;
    }
    .bk-step.done   .bk-step-circle { background: var(--y); color: var(--ytext); }
    .bk-step.active .bk-step-circle { background: #0f172a; color: #fff; }
    .bk-step.idle   .bk-step-circle { background: #f1f5f9; color: #94a3b8; }
    .bk-step-label { font-size: .7rem; font-weight: 600; }
    .bk-step.done   .bk-step-label { color: var(--ytext); }
    .bk-step.active .bk-step-label { color: #0f172a; }
    .bk-step.idle   .bk-step-label { color: #94a3b8; }
    .bk-step-line { flex: 1; height: 2px; background: #e2e8f0; margin: 0 .25rem; }
    .bk-step.done + .bk-step-line,
    .bk-step-line.done { background: var(--y); }

    @media (max-width: 480px) {
        .bk-step-label { display: none; }
    }
</style>
@endpush

@section('content')
<div class="booking-wrap">

    {{-- ── Breadcrumb ── --}}
    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('index') }}"
           class="inline-flex items-center gap-1.5 text-[.8rem] font-semibold text-slate-500
                  hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Beranda
        </a>
        <span class="text-slate-300 text-xs">/</span>
        <span class="text-[.8rem] font-semibold text-slate-400">Kamar</span>
        <span class="text-slate-300 text-xs">/</span>
        <span class="text-[.8rem] font-semibold text-slate-900">Booking</span>
    </div>

    {{-- ── Stepper ── --}}
    <div class="bk-steps">
        <div class="bk-step done">
            <div class="bk-step-circle">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="bk-step-label">Pilih Kamar</span>
        </div>
        <div class="bk-step-line done"></div>
        <div class="bk-step active">
            <div class="bk-step-circle">2</div>
            <span class="bk-step-label">Detail Booking</span>
        </div>
        <div class="bk-step-line"></div>
        <div class="bk-step idle">
            <div class="bk-step-circle">3</div>
            <span class="bk-step-label">Konfirmasi</span>
        </div>
    </div>

    {{-- ── Two-column desktop layout ── --}}
    <div class="flex flex-col lg:flex-row gap-5 items-start">

        {{-- ══ LEFT COLUMN ══ --}}
        <div class="flex-1 w-full min-w-0">

            {{-- ─── Card: Ringkasan Kamar ─── --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#fef9c3;">
                        <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">Kamar yang Dipilih</p>
                        <p class="bk-card-sub">Detail kamar yang akan dipesan</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    <div class="flex gap-4 flex-col sm:flex-row">
                        {{-- Foto kamar (placeholder) --}}
                        <div class="shrink-0 w-full sm:w-36">
                            <div class="room-thumb rounded-xl overflow-hidden" style="height:120px;">
                                <div class="w-full h-full flex items-center justify-content-center"
                                     style="background: linear-gradient(135deg, #fef9c3 0%, #fde68a 100%);
                                            display:flex; align-items:center; justify-content:center;">
                                    <svg class="w-12 h-12" style="color:#b45309;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-[1.05rem] font-extrabold text-slate-900 leading-tight mb-1">
                                Deluxe Room — No. 101
                            </p>
                            <p class="text-[.78rem] text-slate-500 mb-2 leading-relaxed">
                                Kamar luas dengan kasur king-size, AC, TV layar datar, dan pemandangan taman.
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="room-badge" style="background:#fef9c3;color:#92400e;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                    2 Tamu
                                </span>
                                <span class="room-badge" style="background:#f0fdf4;color:#166534;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Tersedia
                                </span>
                                <span class="room-badge" style="background:#eff6ff;color:#1e40af;">
                                    AC
                                </span>
                                <span class="room-badge" style="background:#eff6ff;color:#1e40af;">
                                    WiFi
                                </span>
                            </div>
                            <p class="mt-3 text-[.85rem] font-bold" style="color:var(--ytext);">
                                Rp 350.000 <span class="font-normal text-slate-400 text-[.75rem]">/ malam</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Card: Tanggal & Durasi ─── --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#ede9fe;">
                        <svg class="w-4 h-4" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">Tanggal Menginap</p>
                        <p class="bk-card-sub">Pilih tanggal check-in dan check-out</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    <div class="date-grid">
                        <div>
                            <label class="bk-input-label" for="checkIn">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                                    </svg>
                                    Check-In <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="date" id="checkIn" name="check_in"
                                   class="bk-input" onchange="calcDuration()">
                        </div>
                        <div>
                            <label class="bk-input-label" for="checkOut">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                    Check-Out <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="date" id="checkOut" name="check_out"
                                   class="bk-input" onchange="calcDuration()">
                        </div>
                    </div>

                    {{-- Durasi ─── muncul setelah dua tanggal diisi --}}
                    <div id="durationBadge" class="duration-badge hidden">
                        <svg class="w-4 h-4" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="durationText">— malam</span>
                    </div>

                    {{-- Catatan tamu --}}
                    <div class="mt-4">
                        <label class="bk-input-label" for="guestNote">
                            Catatan Khusus
                            <span class="font-normal text-slate-400">(opsional)</span>
                        </label>
                        <textarea id="guestNote" name="note" rows="2"
                                  placeholder="Contoh: tiba tengah malam, butuh extra bed, alergi bulu binatang..."
                                  class="bk-input resize-none" style="height:auto;"></textarea>
                    </div>
                </div>
            </div>

            {{-- ─── Card: Metode Pembayaran ─── --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#f0fdf4;">
                        <svg class="w-4 h-4" style="color:#15803d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">Metode Pembayaran</p>
                        <p class="bk-card-sub">Pilih cara pembayaran yang diinginkan</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    <p class="text-[.78rem] text-slate-500 mb-3 leading-relaxed">
                        Pembayaran dilakukan secara aman melalui payment gateway.
                        Pilih salah satu metode di bawah ini:
                    </p>

                    {{-- Option 1: DP --}}
                    <div class="pay-option" id="payOptDP" onclick="selectPayment('dp')" role="radio"
                         aria-checked="false" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')selectPayment('dp')">
                        <div class="pay-radio" id="radioDP">
                            <div class="pay-radio-dot"></div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="pay-icon-wrap" style="background:#fef9c3;">
                                <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pr-7">
                                <p class="pay-label">Down Payment (DP)</p>
                                <p class="pay-desc">
                                    Bayar uang muka sebesar <strong>30%</strong> dari total harga sekarang
                                    untuk mengamankan kamar. Sisa pembayaran dilunasi saat check-in.
                                </p>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <span class="pay-tag" style="background:#fef9c3;color:#92400e;">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Amankan kamar sekarang
                                    </span>
                                    <span class="pay-tag" style="background:#f0fdf4;color:#166534;">
                                        Bayar lebih ringan
                                    </span>
                                </div>
                                {{-- DP amount preview (muncul jika tanggal sudah diisi) --}}
                                <div id="dpPreview" class="hidden mt-2 p-2.5 rounded-xl"
                                     style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                    <p class="text-[.73rem] text-slate-500 mb-0.5">Jumlah yang dibayar sekarang (30%)</p>
                                    <p class="text-[.95rem] font-extrabold" style="color:#16a34a;" id="dpAmountText">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pay-divider">atau</div>

                    {{-- Option 2: Tunai Penuh --}}
                    <div class="pay-option" id="payOptFull" onclick="selectPayment('full')" role="radio"
                         aria-checked="false" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')selectPayment('full')">
                        <div class="pay-radio" id="radioFull">
                            <div class="pay-radio-dot"></div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="pay-icon-wrap" style="background:#eff6ff;">
                                <svg class="w-5 h-5" style="color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pr-7">
                                <p class="pay-label">Bayar Lunas (Full)</p>
                                <p class="pay-desc">
                                    Bayar <strong>100%</strong> dari total harga sekarang via payment gateway.
                                    Booking langsung terkonfirmasi otomatis.
                                </p>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <span class="pay-tag" style="background:#eff6ff;color:#1e40af;">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Konfirmasi instan
                                    </span>
                                    <span class="pay-tag" style="background:#fce7f3;color:#9d174d;">
                                        Tidak perlu bayar lagi saat check-in
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment gateway logos --}}
                    <div class="mt-4 flex items-center gap-2 flex-wrap">
                        <p class="text-[.68rem] text-slate-400 font-semibold uppercase tracking-wide shrink-0">Diterima via:</p>
                        <div class="pg-logos">
                            <span class="pg-logo">Midtrans</span>
                            <span class="pg-logo">QRIS</span>
                            <span class="pg-logo">Transfer Bank</span>
                            <span class="pg-logo">GoPay</span>
                            <span class="pg-logo">OVO</span>
                            <span class="pg-logo">Dana</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /left column --}}

        {{-- ══ RIGHT COLUMN: Ringkasan Harga ══ --}}
        <div class="w-full lg:w-80 shrink-0">
            <div class="bk-card" style="position: sticky; top: 80px;">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#f0fdf4;">
                        <svg class="w-4 h-4" style="color:#15803d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">Ringkasan Biaya</p>
                        <p class="bk-card-sub" id="summarySubtitle">Pilih tanggal untuk melihat total</p>
                    </div>
                </div>
                <div class="bk-card-body">

                    {{-- Detail biaya --}}
                    <div id="priceDetail" class="hidden">
                        <div class="price-row">
                            <span class="lbl">Harga per malam</span>
                            <span class="val">Rp 350.000</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">Durasi</span>
                            <span class="val" id="sumNight">— malam</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">Subtotal</span>
                            <span class="val" id="sumSubtotal">Rp 0</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">Pajak &amp; Biaya Layanan</span>
                            <span class="val" id="sumTax">Rp 0</span>
                        </div>
                        <div class="price-row total mt-1 pt-2" style="border-top:1px solid #e2e8f0;">
                            <span class="lbl">Total</span>
                            <span class="val" id="sumTotal">Rp 0</span>
                        </div>
                    </div>

                    {{-- Placeholder jika belum isi tanggal --}}
                    <div id="pricePlaceholder" class="py-4 text-center">
                        <svg class="w-10 h-10 mx-auto mb-2" style="color:#e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-[.78rem] text-slate-400">Isi tanggal check-in dan check-out untuk melihat rincian biaya</p>
                    </div>

                    {{-- Info DP (muncul jika metode DP dipilih) --}}
                    <div id="dpBreakdown" class="hidden mt-3 p-3 rounded-xl"
                         style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <p class="text-[.72rem] font-bold text-green-700 mb-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rincian Down Payment
                        </p>
                        <div class="flex justify-between text-[.78rem] mb-1">
                            <span class="text-slate-500">DP sekarang (30%)</span>
                            <span class="font-bold text-green-700" id="sumDP">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-[.78rem]">
                            <span class="text-slate-500">Sisa saat check-in (70%)</span>
                            <span class="font-bold text-slate-700" id="sumRemaining">Rp 0</span>
                        </div>
                    </div>

                    {{-- Tombol CTA desktop ─── --}}
                    <div class="bk-desktop-cta mt-5">
                        <button type="button" id="ctaBtnDesktop"
                                class="bk-btn-primary" disabled
                                onclick="submitBooking()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="ctaBtnDesktopLabel">Pilih Tanggal & Metode Bayar</span>
                        </button>
                        <a href="{{ route('index') }}"
                           class="bk-btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Batalkan
                        </a>
                    </div>

                    {{-- Security note --}}
                    <p class="mt-3 text-[.68rem] text-slate-400 flex items-start gap-1.5 leading-relaxed">
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" style="color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Pembayaran diamankan dengan enkripsi SSL. Data kartu tidak disimpan di server kami.
                    </p>
                </div>
            </div>
        </div>{{-- /right column --}}

    </div>{{-- /flex row --}}
</div>{{-- /booking-wrap --}}

{{-- ══ MOBILE STICKY CTA BAR ══ --}}
<div class="bk-mobile-cta" id="mobileCta">
    <div class="flex items-center gap-3">
        {{-- Harga ringkas --}}
        <div class="flex-1 min-w-0">
            <p class="text-[.68rem] text-slate-400 font-semibold uppercase tracking-wide leading-none mb-0.5">
                <span id="mobileCtaMethod">Pilih metode bayar</span>
            </p>
            <p class="text-[.95rem] font-extrabold text-slate-900 leading-tight truncate" id="mobileCtaPrice">
                —
            </p>
        </div>
        {{-- CTA button --}}
        <button type="button" id="ctaBtnMobile"
                class="bk-btn-primary shrink-0 py-2.5 px-5 text-[.875rem]"
                style="width:auto;" disabled
                onclick="submitBooking()">
            Lanjutkan
        </button>
    </div>
</div>

@push('scripts')
<script>
/* ════════════════════════════════════════════════════════
   Booking UI — JavaScript (UI only, no actual submit)
   ════════════════════════════════════════════════════════ */

const PRICE_PER_NIGHT = 350000;
const TAX_RATE        = 0.10;   // 10%
const DP_RATE         = 0.30;   // 30%

let selectedMethod = null;  // 'dp' | 'full'
let nights = 0;

/* ── Format rupiah ── */
function formatRp(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

/* ── Pilih metode pembayaran ── */
function selectPayment(method) {
    selectedMethod = method;

    // Reset kedua opsi
    ['dp', 'full'].forEach(m => {
        const el = document.getElementById('payOpt' + (m === 'dp' ? 'DP' : 'Full'));
        const rd = document.getElementById('radio'   + (m === 'dp' ? 'DP' : 'Full'));
        el.classList.remove('selected');
        el.setAttribute('aria-checked', 'false');
    });

    // Aktifkan yang dipilih
    const active = document.getElementById('payOpt' + (method === 'dp' ? 'DP' : 'Full'));
    const radio  = document.getElementById('radio'  + (method === 'dp' ? 'DP' : 'Full'));
    active.classList.add('selected');
    active.setAttribute('aria-checked', 'true');

    updateSummary();
    updateCTA();
}

/* ── Hitung durasi dari tanggal ── */
function calcDuration() {
    const ci = document.getElementById('checkIn').value;
    const co = document.getElementById('checkOut').value;

    if (!ci || !co) { nights = 0; updateSummary(); updateCTA(); return; }

    const d1 = new Date(ci);
    const d2 = new Date(co);

    if (d2 <= d1) {
        nights = 0;
        document.getElementById('durationBadge').classList.add('hidden');
        Swal.fire({
            icon: 'warning', title: 'Tanggal Tidak Valid',
            text: 'Tanggal check-out harus setelah check-in.',
            toast: true, position: 'top-end',
            timer: 3000, timerProgressBar: true, showConfirmButton: false
        });
        updateSummary(); updateCTA(); return;
    }

    nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

    const badge = document.getElementById('durationBadge');
    document.getElementById('durationText').textContent = nights + ' malam';
    badge.classList.remove('hidden');

    updateSummary();
    updateCTA();
}

/* ── Update ringkasan harga ── */
function updateSummary() {
    const placeholder   = document.getElementById('pricePlaceholder');
    const priceDetail   = document.getElementById('priceDetail');
    const dpBreakdown   = document.getElementById('dpBreakdown');
    const dpPreview     = document.getElementById('dpPreview');
    const summarySubtitle = document.getElementById('summarySubtitle');

    if (nights <= 0) {
        placeholder.classList.remove('hidden');
        priceDetail.classList.add('hidden');
        dpBreakdown.classList.add('hidden');
        dpPreview.classList.add('hidden');
        summarySubtitle.textContent = 'Pilih tanggal untuk melihat total';
        return;
    }

    const subtotal = PRICE_PER_NIGHT * nights;
    const tax      = subtotal * TAX_RATE;
    const total    = subtotal + tax;
    const dp       = total * DP_RATE;
    const remaining = total - dp;

    placeholder.classList.add('hidden');
    priceDetail.classList.remove('hidden');

    document.getElementById('sumNight').textContent    = nights + ' malam';
    document.getElementById('sumSubtotal').textContent = formatRp(subtotal);
    document.getElementById('sumTax').textContent      = formatRp(tax);
    document.getElementById('sumTotal').textContent    = formatRp(total);
    document.getElementById('sumDP').textContent       = formatRp(dp);
    document.getElementById('sumRemaining').textContent = formatRp(remaining);
    document.getElementById('dpAmountText').textContent = formatRp(dp);

    summarySubtitle.textContent = nights + ' malam • ' + formatRp(total);

    // Tampilkan rincian DP jika metode DP
    if (selectedMethod === 'dp') {
        dpBreakdown.classList.remove('hidden');
        dpPreview.classList.remove('hidden');
    } else {
        dpBreakdown.classList.add('hidden');
        if (selectedMethod === 'full') {
            dpPreview.classList.add('hidden');
        }
    }
}

/* ── Update tombol CTA ── */
function updateCTA() {
    const btnDesktop = document.getElementById('ctaBtnDesktop');
    const btnMobile  = document.getElementById('ctaBtnMobile');
    const labelDesktop = document.getElementById('ctaBtnDesktopLabel');
    const mobileMethod = document.getElementById('mobileCtaMethod');
    const mobilePrice  = document.getElementById('mobileCtaPrice');

    const ready = nights > 0 && selectedMethod !== null;

    btnDesktop.disabled = !ready;
    btnMobile.disabled  = !ready;

    if (!ready) {
        labelDesktop.textContent = 'Pilih Tanggal & Metode Bayar';
        mobileMethod.textContent = selectedMethod
            ? (nights <= 0 ? 'Pilih tanggal menginap' : 'Pilih metode bayar')
            : 'Pilih metode bayar';
        mobilePrice.textContent = nights > 0
            ? formatRp((PRICE_PER_NIGHT * nights * (1 + TAX_RATE)))
            : '—';
        return;
    }

    const total = PRICE_PER_NIGHT * nights * (1 + TAX_RATE);
    const payNow = selectedMethod === 'dp' ? total * DP_RATE : total;

    if (selectedMethod === 'dp') {
        labelDesktop.textContent = 'Bayar DP ' + formatRp(payNow);
        mobileMethod.textContent = 'Down Payment (30%)';
    } else {
        labelDesktop.textContent = 'Bayar Lunas ' + formatRp(payNow);
        mobileMethod.textContent = 'Bayar Lunas (100%)';
    }
    mobilePrice.textContent = formatRp(payNow);
}

/* ── Submit (UI only — akan ditambahkan logika nanti) ── */
function submitBooking() {
    if (!selectedMethod || nights <= 0) return;

    const methodLabel = selectedMethod === 'dp' ? 'Down Payment (DP)' : 'Bayar Lunas';
    const total = PRICE_PER_NIGHT * nights * (1 + TAX_RATE);
    const payNow = selectedMethod === 'dp' ? total * DP_RATE : total;

    Swal.fire({
        icon: 'info',
        title: 'Konfirmasi Booking',
        html: `
            <div style="text-align:left;font-size:.875rem;line-height:1.6;">
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #f1f5f9;">
                    <span style="color:#64748b;">Durasi</span>
                    <strong>${nights} malam</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #f1f5f9;">
                    <span style="color:#64748b;">Metode</span>
                    <strong>${methodLabel}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.4rem 0;margin-top:.25rem;">
                    <span style="color:#64748b;font-weight:700;">Bayar Sekarang</span>
                    <strong style="color:#b45309;font-size:1rem;">${formatRp(payNow)}</strong>
                </div>
            </div>`,
        confirmButtonText: 'Lanjut ke Pembayaran',
        cancelButtonText:  'Kembali',
        showCancelButton: true,
        reverseButtons: true,
        customClass: { confirmButton: 'swal-confirm-btn' }
    });
}

/* ── Set minimum tanggal = hari ini ── */
(function initDates() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('checkIn').min  = today;
    document.getElementById('checkOut').min = today;

    document.getElementById('checkIn').addEventListener('change', function () {
        const co = document.getElementById('checkOut');
        if (co.value && co.value <= this.value) {
            co.value = '';
        }
        co.min = this.value;
        calcDuration();
    });
})();
</script>
@endpush
@endsection
