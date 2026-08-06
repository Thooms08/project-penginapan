@extends('Visitors.layouts.app')

@section('title', __('visitor.bk_breadcrumb_booking') . ' — ' . $room->trans('name'))

@push('head')
{{-- Midtrans Snap.js --}}
@if(config('services.midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<style>
:root { --y:#eab308;--yd:#ca8a04;--yl:#facc15;--y50:#fefce8;--y100:#fef9c3;--ytext:#713f12; }
.booking-wrap { max-width:960px;margin:0 auto;padding:2rem 1.25rem 6rem; }
@media(min-width:768px){ .booking-wrap{padding:2.5rem 2rem 3rem;} }
.bk-card { background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;
           box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;margin-bottom:1rem; }
.bk-card-hdr { display:flex;align-items:center;gap:.75rem;padding:1rem 1.25rem;
               border-bottom:1px solid #f1f5f9; }
.bk-card-hdr-icon { width:34px;height:34px;border-radius:10px;display:flex;
                    align-items:center;justify-content:center;flex-shrink:0; }
.bk-card-title { font-size:.9rem;font-weight:700;color:#0f172a; }
.bk-card-sub   { font-size:.72rem;color:#94a3b8;margin-top:.1rem; }
.bk-card-body  { padding:1.25rem; }
.room-badge { display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;
              border-radius:9999px;font-size:.68rem;font-weight:700; }
.date-grid { display:grid;grid-template-columns:1fr 1fr;gap:.875rem; }
@media(max-width:480px){ .date-grid{grid-template-columns:1fr;} }
.bk-input-label { display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.4rem; }
.bk-input { width:100%;padding:.625rem .875rem;border:1px solid #e2e8f0;border-radius:.75rem;
            font-size:.875rem;color:#1e293b;background:#f8fafc;outline:none;
            transition:border .15s,box-shadow .15s;font-family:'Inter',sans-serif; }
.bk-input:focus { border-color:var(--yl);box-shadow:0 0 0 3px rgba(234,179,8,.15); }
.bk-input.error { border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.12); }
.duration-badge { display:flex;align-items:center;justify-content:center;gap:.5rem;
                  padding:.5rem 1rem;border-radius:.75rem;background:var(--y50);
                  border:1px solid var(--y100);font-size:.8rem;font-weight:700;
                  color:var(--ytext);margin-top:.875rem; }
</style>
@endpush
<style>
.pay-option { border:2px solid #e2e8f0;border-radius:1.125rem;padding:1.125rem 1.25rem;
              cursor:pointer;transition:border-color .18s,background .18s,box-shadow .18s,transform .12s;
              position:relative;user-select:none;-webkit-tap-highlight-color:transparent; }
.pay-option:hover { border-color:var(--yl);background:var(--y50); }
.pay-option.selected { border-color:var(--y);background:var(--y50);box-shadow:0 0 0 3px rgba(234,179,8,.18); }
.pay-option:active { transform:scale(.98); }
.pay-radio { position:absolute;top:1rem;right:1.125rem;width:20px;height:20px;border-radius:50%;
             border:2px solid #cbd5e1;background:#fff;display:flex;align-items:center;
             justify-content:center;transition:border-color .15s,background .15s;flex-shrink:0; }
.pay-option.selected .pay-radio { border-color:var(--y);background:var(--y); }
.pay-radio-dot { width:8px;height:8px;border-radius:50%;background:#fff;opacity:0;transition:opacity .15s; }
.pay-option.selected .pay-radio-dot { opacity:1; }
.pay-icon-wrap { width:44px;height:44px;border-radius:13px;display:flex;align-items:center;
                 justify-content:center;flex-shrink:0;transition:background .15s; }
.pay-label { font-size:.9rem;font-weight:800;color:#0f172a;margin-bottom:.15rem; }
.pay-desc  { font-size:.75rem;color:#64748b;line-height:1.45; }
.pay-tag   { display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .5rem;
             border-radius:.375rem;font-size:.65rem;font-weight:700;margin-top:.5rem; }
.price-row { display:flex;justify-content:space-between;align-items:center;
             padding:.45rem 0;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
.price-row:last-child { border-bottom:none; }
.price-row .lbl { color:#64748b; }
.price-row .val { font-weight:600;color:#0f172a; }
.price-row.total .lbl { font-weight:800;color:#0f172a;font-size:.9rem; }
.price-row.total .val { font-weight:800;color:var(--ytext);font-size:1rem; }
.bk-btn-primary { width:100%;padding:.875rem;border-radius:.875rem;border:none;cursor:pointer;
                  font-size:1rem;font-weight:800;letter-spacing:.01em;background:var(--y);
                  color:var(--ytext);transition:background .15s,color .15s,transform .12s,box-shadow .15s;
                  display:flex;align-items:center;justify-content:center;gap:.5rem; }
.bk-btn-primary:hover  { background:var(--yd);color:#fff;box-shadow:0 4px 16px rgba(234,179,8,.35); }
.bk-btn-primary:active { transform:scale(.97); }
.bk-btn-primary:disabled { background:#e2e8f0;color:#94a3b8;cursor:not-allowed;box-shadow:none;transform:none; }
.bk-btn-outline { width:100%;padding:.75rem;border-radius:.875rem;border:1.5px solid #e2e8f0;
                  cursor:pointer;font-size:.875rem;font-weight:600;color:#64748b;background:#fff;
                  transition:background .15s,color .15s,border-color .15s;
                  display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:.625rem; }
.bk-btn-outline:hover { background:#f8fafc;border-color:#94a3b8;color:#1e293b; }
.bk-mobile-cta { display:none;position:fixed;bottom:68px;left:0;right:0;z-index:55;
                 padding:.875rem 1rem;background:rgba(255,255,255,.97);
                 border-top:1px solid #e2e8f0;box-shadow:0 -4px 20px rgba(0,0,0,.08);
                 padding-bottom:calc(.875rem + env(safe-area-inset-bottom,0px)); }
@media(max-width:767px) {
    .bk-mobile-cta { display:block; }
    .bk-desktop-cta { display:none; }
    .booking-wrap { padding-bottom:9rem; }
}
.pay-divider { display:flex;align-items:center;gap:.75rem;margin:.875rem 0;
               color:#94a3b8;font-size:.75rem;font-weight:600; }
.pay-divider::before,.pay-divider::after { content:'';flex:1;height:1px;background:#e2e8f0; }
.pg-logos { display:flex;align-items:center;gap:.625rem;flex-wrap:wrap;margin-top:.75rem; }
.pg-logo  { padding:.25rem .625rem;border-radius:.5rem;border:1px solid #e2e8f0;
            background:#f8fafc;font-size:.65rem;font-weight:700;color:#475569;letter-spacing:.03em; }
.bk-steps { display:flex;align-items:center;gap:0;margin-bottom:1.5rem; }
.bk-step  { display:flex;align-items:center;gap:.5rem;flex:1; }
.bk-step-circle { width:28px;height:28px;border-radius:50%;display:flex;align-items:center;
                  justify-content:center;font-size:.72rem;font-weight:800;flex-shrink:0; }
.bk-step.done   .bk-step-circle { background:var(--y);color:var(--ytext); }
.bk-step.active .bk-step-circle { background:#0f172a;color:#fff; }
.bk-step.idle   .bk-step-circle { background:#f1f5f9;color:#94a3b8; }
.bk-step-label { font-size:.7rem;font-weight:600; }
.bk-step.done   .bk-step-label { color:var(--ytext); }
.bk-step.active .bk-step-label { color:#0f172a; }
.bk-step.idle   .bk-step-label { color:#94a3b8; }
.bk-step-line { flex:1;height:2px;background:#e2e8f0;margin:0 .25rem; }
.bk-step.done + .bk-step-line,.bk-step-line.done { background:var(--y); }
@media(max-width:480px){ .bk-step-label{display:none;} }
/* DP Modal overlay */
.dp-modal-overlay { display:none;position:fixed;inset:0;z-index:80;
                    background:rgba(0,0,0,.5);backdrop-filter:blur(4px);
                    align-items:center;justify-content:center;padding:1rem; }
.dp-modal-overlay.show { display:flex; }
.dp-modal { background:#fff;border-radius:1.5rem;max-width:420px;width:100%;
            box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden;
            animation:modalIn .22s cubic-bezier(.4,0,.2,1); }
@keyframes modalIn { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
.dp-modal-header { padding:1.25rem 1.5rem 0; }
.dp-modal-body   { padding:1rem 1.5rem 1.5rem; }
</style>

@php
    $coverPhoto = $room->coverPhoto?->path ?? null;
    $facilities = $room->facilities ?? collect();
    $pricePerNight = (float) $room->price;
    $taxRate = 10; // %
    $dpRate  = 50; // %
@endphp

@section('content')
<div class="booking-wrap">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('index') }}"
           class="inline-flex items-center gap-1.5 text-[.8rem] font-semibold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>{{ __('visitor.bk_breadcrumb_home') }}
        </a>
        <span class="text-slate-300 text-xs">/</span>
        <span class="text-[.8rem] font-semibold text-slate-400">{{ __('visitor.bk_breadcrumb_room') }}</span>
        <span class="text-slate-300 text-xs">/</span>
        <span class="text-[.8rem] font-semibold text-slate-900">{{ __('visitor.bk_breadcrumb_booking') }}</span>
    </div>

    {{-- Stepper --}}
    <div class="bk-steps">
        <div class="bk-step done">
            <div class="bk-step-circle">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="bk-step-label">{{ __('visitor.bk_step_choose_room') }}</span>
        </div>
        <div class="bk-step-line done"></div>
        <div class="bk-step active">
            <div class="bk-step-circle">2</div>
            <span class="bk-step-label">{{ __('visitor.bk_step_detail') }}</span>
        </div>
        <div class="bk-step-line"></div>
        <div class="bk-step idle">
            <div class="bk-step-circle">3</div>
            <span class="bk-step-label">{{ __('visitor.bk_step_confirm') }}</span>
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="flex flex-col lg:flex-row gap-5 items-start">

        {{-- LEFT COLUMN --}}
        <div class="flex-1 w-full min-w-0">

            {{-- Card: Room Summary --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#fef9c3;">
                        <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">{{ __('visitor.bk_card_room_title') }}</p>
                        <p class="bk-card-sub">{{ __('visitor.bk_card_room_sub') }}</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    <div class="flex gap-4 flex-col sm:flex-row">
                        <div class="shrink-0 w-full sm:w-36">
                            <div class="rounded-xl overflow-hidden" style="height:120px;background:#f1f5f9;">
                                @if($coverPhoto)
                                    <img src="{{ asset($coverPhoto) }}" alt="{{ $room->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center"
                                         style="background:linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);">
                                        <svg class="w-12 h-12" style="color:#b45309;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[1.05rem] font-extrabold text-slate-900 leading-tight mb-1">
                                {{ $room->trans('name') }}
                            </p>
                            @if($room->description)
                                <p class="text-[.78rem] text-slate-500 mb-2 leading-relaxed line-clamp-2">
                                    {{ $room->trans('description') }}
                                </p>
                            @endif
                            <div class="flex flex-wrap gap-1.5">
                                <span class="room-badge" style="background:#fef9c3;color:#92400e;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                    {{ $room->capacity }} {{ __('visitor.bk_guests') }}
                                </span>
                                <span class="room-badge" style="background:#f0fdf4;color:#166534;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('visitor.bk_available') }}
                                </span>
                                @foreach($facilities->take(4) as $f)
                                    <span class="room-badge" style="background:#eff6ff;color:#1e40af;">
                                        {{ $f->getTransName() }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="mt-3 text-[.85rem] font-bold" style="color:var(--ytext);">
                                Rp {{ number_format($room->price, 0, ',', '.') }}
                                <span class="font-normal text-slate-400 text-[.75rem]">{{ __('visitor.bk_per_night') }}</span>
                                @if($room->has_discount)
                                    <span class="ml-2 text-[.72rem] text-green-600 font-semibold">
                                        {{ __('visitor.bk_discount') }} {{ $room->formatted_discount }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Dates --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#ede9fe;">
                        <svg class="w-4 h-4" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">{{ __('visitor.bk_dates_title') }}</p>
                        <p class="bk-card-sub">{{ __('visitor.bk_dates_sub') }}</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    {{-- Validation errors --}}
                    @if($errors->any())
                        <div class="mb-4 p-3 rounded-xl text-[.8rem] font-semibold text-red-700"
                             style="background:#fef2f2;border:1px solid #fecaca;">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="date-grid">
                        <div>
                            <label class="bk-input-label" for="checkIn">
                                {{ __('visitor.bk_checkin_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="checkIn" name="check_in"
                                   value="{{ old('check_in', $preCheckIn ?? '') }}"
                                   class="bk-input {{ $errors->has('check_in') ? 'error' : '' }}">
                            <p id="checkInNote" class="text-[.72rem] text-slate-400 mt-1 hidden">
                                {{ __('visitor.bk_checkin_note') }}
                            </p>
                        </div>
                        <div>
                            <label class="bk-input-label" for="checkOut">
                                {{ __('visitor.bk_checkout_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="checkOut" name="check_out"
                                   value="{{ old('check_out', $preCheckOut ?? '') }}"
                                   class="bk-input {{ $errors->has('check_out') ? 'error' : '' }}">
                            <p id="checkOutNote" class="text-[.72rem] text-slate-400 mt-1 hidden">
                                <span id="maxCheckOutHint"></span>
                            </p>
                        </div>
                    </div>
                    {{-- Availability status --}}
                    <div id="availStatus" class="hidden mt-3 p-2.5 rounded-xl text-[.78rem] font-semibold flex items-center gap-2"></div>
                    {{-- Duration badge --}}
                    <div id="durationBadge" class="duration-badge hidden">
                        <svg class="w-4 h-4" style="color:var(--ytext);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="durationText">—</span>
                    </div>
                    {{-- Guest note --}}
                    <div class="mt-4">
                        <label class="bk-input-label" for="guestNote">
                            {{ __('visitor.bk_note_label') }}
                            <span class="font-normal text-slate-400">{{ __('visitor.bk_note_optional') }}</span>
                        </label>
                        <textarea id="guestNote" name="note" rows="2"
                                  placeholder="{{ __('visitor.bk_note_placeholder') }}"
                                  class="bk-input resize-none" style="height:auto;">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Card: Payment Method --}}
            <div class="bk-card">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#f0fdf4;">
                        <svg class="w-4 h-4" style="color:#15803d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">{{ __('visitor.bk_pay_title') }}</p>
                        <p class="bk-card-sub">{{ __('visitor.bk_pay_sub') }}</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    {{-- DP Option --}}
                    <div class="pay-option" id="payOptDP" onclick="selectPayment('dp')" role="radio"
                         aria-checked="false" tabindex="0"
                         onkeydown="if(event.key==='Enter'||event.key===' ')selectPayment('dp')">
                        <div class="pay-radio" id="radioDP"><div class="pay-radio-dot"></div></div>
                        <div class="flex items-start gap-3">
                            <div class="pay-icon-wrap" style="background:#fef9c3;">
                                <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pr-7">
                                <p class="pay-label">{{ __('visitor.bk_dp_label') }}</p>
                                <p class="pay-desc">
                                    {!! __('visitor.bk_dp_desc') !!}
                                </p>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <span class="pay-tag" style="background:#fef9c3;color:#92400e;">{{ __('visitor.bk_dp_tag1') }}</span>
                                    <span class="pay-tag" style="background:#f0fdf4;color:#166534;">{{ __('visitor.bk_dp_tag2') }}</span>
                                </div>
                                <div id="dpPreview" class="hidden mt-2 p-2.5 rounded-xl"
                                     style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                    <p class="text-[.73rem] text-slate-500 mb-0.5">{{ __('visitor.bk_dp_now_label') }}</p>
                                    <p class="text-[.95rem] font-extrabold text-green-700" id="dpAmountText">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pay-divider">atau</div>

                    {{-- Full Pay Option --}}
                    <div class="pay-option" id="payOptFull" onclick="selectPayment('full')" role="radio"
                         aria-checked="false" tabindex="0"
                         onkeydown="if(event.key==='Enter'||event.key===' ')selectPayment('full')">
                        <div class="pay-radio" id="radioFull"><div class="pay-radio-dot"></div></div>
                        <div class="flex items-start gap-3">
                            <div class="pay-icon-wrap" style="background:#eff6ff;">
                                <svg class="w-5 h-5" style="color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pr-7">
                                <p class="pay-label">{{ __('visitor.bk_full_label') }}</p>
                                <p class="pay-desc">
                                    {!! __('visitor.bk_full_desc') !!}
                                </p>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <span class="pay-tag" style="background:#eff6ff;color:#1e40af;">{{ __('visitor.bk_full_tag1') }}</span>
                                    <span class="pay-tag" style="background:#fce7f3;color:#9d174d;">{{ __('visitor.bk_full_tag2') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2 flex-wrap">
                        <p class="text-[.68rem] text-slate-400 font-semibold uppercase tracking-wide shrink-0">{{ __('visitor.bk_accepted_via') }}</p>
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

        {{-- RIGHT COLUMN: Price Summary --}}
        <div class="w-full lg:w-80 shrink-0">
            <div class="bk-card" style="position:sticky;top:80px;">
                <div class="bk-card-hdr">
                    <div class="bk-card-hdr-icon" style="background:#f0fdf4;">
                        <svg class="w-4 h-4" style="color:#15803d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bk-card-title">{{ __('visitor.bk_summary_title') }}</p>
                        <p class="bk-card-sub" id="summarySubtitle">{{ __('visitor.bk_summary_sub') }}</p>
                    </div>
                </div>
                <div class="bk-card-body">
                    <div id="priceDetail" class="hidden">
                        <div class="price-row">
                            <span class="lbl">{{ __('visitor.bk_price_night') }}</span>
                            <span class="val" id="sumPricePerNight">Rp 0</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">{{ __('visitor.bk_duration') }}</span>
                            <span class="val" id="sumNight">—</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">{{ __('visitor.bk_subtotal') }}</span>
                            <span class="val" id="sumSubtotal">Rp 0</span>
                        </div>
                        <div class="price-row">
                            <span class="lbl">{{ __('visitor.bk_tax') }}</span>
                            <span class="val" id="sumTax">Rp 0</span>
                        </div>
                        <div class="price-row total mt-1 pt-2" style="border-top:1px solid #e2e8f0;">
                            <span class="lbl">{{ __('visitor.bk_total') }}</span>
                            <span class="val" id="sumTotal">Rp 0</span>
                        </div>
                    </div>
                    <div id="pricePlaceholder" class="py-4 text-center">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-[.78rem] text-slate-400">{{ __('visitor.bk_placeholder_dates') }}</p>
                    </div>
                    {{-- DP breakdown --}}
                    <div id="dpBreakdown" class="hidden mt-3 p-3 rounded-xl"
                         style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <p class="text-[.72rem] font-bold text-green-700 mb-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('visitor.bk_dp_detail') }}
                        </p>
                        <div class="flex justify-between text-[.78rem] mb-1">
                            <span class="text-slate-500">{{ __('visitor.bk_dp_now') }}</span>
                            <span class="font-bold text-green-700" id="sumDP">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-[.78rem]">
                            <span class="text-slate-500">{{ __('visitor.bk_dp_rest') }}</span>
                            <span class="font-bold text-slate-700" id="sumRemaining">Rp 0</span>
                        </div>
                    </div>
                    {{-- Desktop CTA --}}
                    <div class="bk-desktop-cta mt-5">
                        <button type="button" id="ctaBtnDesktop" class="bk-btn-primary" disabled
                                onclick="submitBooking()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="ctaBtnDesktopLabel">{{ __('visitor.bk_select_date_method') }}</span>
                        </button>
                        <a href="{{ route('index') }}" class="bk-btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            {{ __('visitor.bk_cancel') }}
                        </a>
                    </div>
                    <p class="mt-3 text-[.68rem] text-slate-400 flex items-start gap-1.5 leading-relaxed">
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        {{ __('visitor.bk_ssl_note') }}
                    </p>
                </div>
            </div>
        </div>{{-- /right column --}}

    </div>{{-- /flex row --}}
</div>{{-- /booking-wrap --}}

{{-- Mobile Sticky CTA --}}
<div class="bk-mobile-cta" id="mobileCta">
    <div class="flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-[.68rem] text-slate-400 font-semibold uppercase tracking-wide leading-none mb-0.5">
                <span id="mobileCtaMethod">{{ __('visitor.bk_mobile_method') }}</span>
            </p>
            <p class="text-[.95rem] font-extrabold text-slate-900 leading-tight truncate" id="mobileCtaPrice">—</p>
        </div>
        <button type="button" id="ctaBtnMobile"
                class="bk-btn-primary shrink-0 py-2.5 px-5 text-[.875rem]"
                style="width:auto;" disabled onclick="submitBooking()">
            {{ __('visitor.bk_continue') }}
        </button>
    </div>
</div>

{{-- DP Confirmation Modal --}}
<div class="dp-modal-overlay" id="dpModalOverlay">
    <div class="dp-modal">
        <div class="dp-modal-header">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fef9c3;">
                        <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-[.95rem]">{{ __('visitor.bk_dp_modal_title') }}</p>
                        <p class="text-[.72rem] text-slate-400">{{ __('visitor.bk_dp_modal_sub') }}</p>
                    </div>
                </div>
                <button onclick="closeDpModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="dp-modal-body">
            <div class="rounded-xl p-4 mb-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="flex justify-between text-[.82rem] py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">{{ __('visitor.bk_dp_modal_room') }}</span>
                    <span class="font-semibold text-slate-900 text-right ml-4 truncate max-w-[160px]">{{ $room->trans('name') }}</span>
                </div>
                <div class="flex justify-between text-[.82rem] py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Check-In</span>
                    <span class="font-semibold text-slate-900" id="dpModalCheckIn">—</span>
                </div>
                <div class="flex justify-between text-[.82rem] py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Check-Out</span>
                    <span class="font-semibold text-slate-900" id="dpModalCheckOut">—</span>
                </div>
                <div class="flex justify-between text-[.82rem] py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">{{ __('visitor.bk_dp_modal_nights') }}</span>
                    <span class="font-semibold text-slate-900" id="dpModalNights">—</span>
                </div>
                <div class="flex justify-between text-[.82rem] py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">{{ __('visitor.bk_dp_modal_total') }}</span>
                    <span class="font-semibold text-slate-900" id="dpModalTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-[.88rem] py-2 mt-1" style="border-top:2px solid #e2e8f0;">
                    <span class="font-bold text-slate-700">{{ __('visitor.bk_dp_modal_now') }}</span>
                    <span class="font-extrabold text-green-700 text-[1rem]" id="dpModalAmount">Rp 0</span>
                </div>
                <div class="flex justify-between text-[.78rem] pb-1">
                    <span class="text-slate-400">{{ __('visitor.bk_dp_modal_rest') }}</span>
                    <span class="font-semibold text-slate-500" id="dpModalRemaining">Rp 0</span>
                </div>
            </div>
            <div class="rounded-xl p-3 mb-4 flex items-start gap-2.5" style="background:#fef9c3;border:1px solid #fde68a;">
                <svg class="w-4 h-4 shrink-0 mt-0.5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-[.75rem] text-amber-800 leading-relaxed">
                    {!! __('visitor.bk_dp_nonrefund') !!}
                </p>
            </div>
            <button type="button" id="dpPayBtn" onclick="processPayment('dp')"
                    class="bk-btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                {{ __('visitor.bk_dp_pay_now') }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
@php
$bkLang = [
    'checking_avail'  => __('visitor.bk_checking_avail'),
    'room_avail'      => __('visitor.bk_room_avail'),
    'room_not_avail'  => __('visitor.bk_room_not_avail'),
    'max_checkout'    => __('visitor.bk_max_checkout'),
    'invalid_title'   => __('visitor.bk_invalid_date_title'),
    'invalid_text'    => __('visitor.bk_invalid_date_text'),
    'pending_title'   => __('visitor.bk_pay_pending_title'),
    'pending_text'    => __('visitor.bk_pay_pending_text'),
    'see_history'     => __('visitor.bk_see_history'),
    'fail_title'      => __('visitor.bk_pay_fail_title'),
    'fail_text'       => __('visitor.bk_pay_fail_text'),
    'cancel_title'    => __('visitor.bk_pay_cancel_title'),
    'cancel_text'     => __('visitor.bk_pay_cancel_text'),
    'stay_here'       => __('visitor.bk_stay_here'),
    'conn_title'      => __('visitor.bk_conn_error_title'),
    'conn_text'       => __('visitor.bk_conn_error_text'),
    'processing'      => __('visitor.bk_processing'),
    'nights_unit'     => __('visitor.bk_nights_unit'),
    'select_method'   => __('visitor.bk_mobile_method'),
    'no_dates_method' => __('visitor.bk_select_date_method'),
    'unavailable'     => __('visitor.bk_unavailable'),
    'select_pay'      => __('visitor.bk_select_method'),
    'dp_pay'          => __('visitor.bk_dp_pay_label'),
    'full_pay'        => __('visitor.bk_full_pay_label'),
    'summary_sub'     => __('visitor.bk_summary_sub'),
];
@endphp
<script>
window.__bkLang = @json($bkLang);
/* ══════════════════════════════════════════════════════════
   Booking Page — JavaScript
   ══════════════════════════════════════════════════════════ */

const PRICE_PER_NIGHT = {{ (float)$room->price }};
const ROOM_ID         = {{ $room->id }};
const ROOM_UUID       = '{{ $room->uuid }}';
const STORE_URL       = '{{ route('booking.store', $room->uuid) }}';
const CSRF_TOKEN      = '{{ csrf_token() }}';
const AVAIL_URL       = '{{ route('booking.check-availability') }}';
const URL_SUCCESS     = '{{ route('booking.history', ['tab' => 'success']) }}';
const URL_PENDING     = '{{ route('booking.history', ['tab' => 'pending']) }}';
const URL_FAILED      = '{{ route('booking.history', ['tab' => 'failed']) }}';
const TAX_RATE        = 0.10;
const DP_RATE         = 0.50;

let selectedMethod  = null;
let nights          = 0;
let isAvailable     = false;
let maxCheckOutDate = null;
let availCheckTimer = null;

/* ── Format Rupiah ── */
function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}
function fmtDate(str) {
    if (!str) return '—';
    const d = new Date(str + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
}

/* ── Calculate prices ── */
function calcPrices(n, ppn) {
    const sub  = ppn * n;
    const tax  = Math.round(sub * TAX_RATE);
    const tot  = sub + tax;
    const dp   = Math.round(tot * DP_RATE);
    const rem  = tot - dp;
    return { sub, tax, tot, dp, rem, ppn };
}

/* ── Select payment method ── */
function selectPayment(method) {
    selectedMethod = method;
    ['dp','full'].forEach(m => {
        const id = m === 'dp' ? 'DP' : 'Full';
        document.getElementById('payOpt' + id).classList.remove('selected');
        document.getElementById('payOpt' + id).setAttribute('aria-checked','false');
    });
    const id = method === 'dp' ? 'DP' : 'Full';
    document.getElementById('payOpt' + id).classList.add('selected');
    document.getElementById('payOpt' + id).setAttribute('aria-checked','true');
    updateSummary();
    updateCTA();
}

/* ── Check availability via AJAX ── */
function checkAvail() {
    const ci = document.getElementById('checkIn').value;
    const co = document.getElementById('checkOut').value;
    if (!ci || !co) return;

    const status = document.getElementById('availStatus');
    status.className = 'mt-3 p-2.5 rounded-xl text-[.78rem] font-semibold flex items-center gap-2';
    status.innerHTML = '<svg class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> ' + window.__bkLang.checking_avail;
    status.classList.remove('hidden');

    fetch(`${AVAIL_URL}?room_id=${ROOM_ID}&check_in=${ci}&check_out=${co}`)
        .then(r => r.json())
        .then(data => {
            isAvailable     = data.available;
            maxCheckOutDate = data.max_checkout;

            if (isAvailable) {
                status.style.background = '#f0fdf4';
                status.style.border     = '1px solid #bbf7d0';
                status.style.color      = '#166534';
                status.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ' + window.__bkLang.room_avail;
            } else {
                status.style.background = '#fef2f2';
                status.style.border     = '1px solid #fecaca';
                status.style.color      = '#dc2626';
                status.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> ' + window.__bkLang.room_not_avail;
            }

            if (maxCheckOutDate) {
                document.getElementById('checkOut').max = maxCheckOutDate;
                const hint = document.getElementById('maxCheckOutHint');
                hint.textContent = window.__bkLang.max_checkout + ' ' + fmtDate(maxCheckOutDate);
                document.getElementById('checkOutNote').classList.remove('hidden');
            }

            updateSummary();
            updateCTA();
        })
        .catch(() => { status.classList.add('hidden'); });
}

/* ── Calc duration & trigger availability check ── */
function calcDuration() {
    const ci = document.getElementById('checkIn').value;
    const co = document.getElementById('checkOut').value;

    if (!ci || !co) {
        nights = 0;
        isAvailable = false;
        document.getElementById('durationBadge').classList.add('hidden');
        document.getElementById('availStatus').classList.add('hidden');
        updateSummary(); updateCTA(); return;
    }

    const d1 = new Date(ci + 'T00:00:00');
    const d2 = new Date(co + 'T00:00:00');

    if (d2 <= d1) {
        nights = 0; isAvailable = false;
        document.getElementById('durationBadge').classList.add('hidden');
        Swal.fire({ icon:'warning', title: window.__bkLang.invalid_title,
            text: window.__bkLang.invalid_text,
            toast:true, position:'top-end', timer:3000,
            timerProgressBar:true, showConfirmButton:false });
        updateSummary(); updateCTA(); return;
    }

    nights = Math.round((d2 - d1) / 86400000);
    document.getElementById('durationText').textContent = nights + ' ' + window.__bkLang.nights_unit;
    document.getElementById('durationBadge').classList.remove('hidden');

    // Debounce availability check
    clearTimeout(availCheckTimer);
    availCheckTimer = setTimeout(checkAvail, 400);

    updateSummary(); updateCTA();
}

/* ── Update price summary ── */
function updateSummary() {
    const ph = document.getElementById('pricePlaceholder');
    const pd = document.getElementById('priceDetail');
    const dpBD = document.getElementById('dpBreakdown');
    const dpPv = document.getElementById('dpPreview');
    const sub  = document.getElementById('summarySubtitle');

    if (nights <= 0) {
        ph.classList.remove('hidden'); pd.classList.add('hidden');
        dpBD.classList.add('hidden'); dpPv.classList.add('hidden');
        sub.textContent = window.__bkLang.summary_sub;
        return;
    }

    const p = calcPrices(nights, PRICE_PER_NIGHT);
    ph.classList.add('hidden'); pd.classList.remove('hidden');

    document.getElementById('sumPricePerNight').textContent = fmt(p.ppn);
    document.getElementById('sumNight').textContent         = nights + ' ' + window.__bkLang.nights_unit;
    document.getElementById('sumSubtotal').textContent      = fmt(p.sub);
    document.getElementById('sumTax').textContent           = fmt(p.tax);
    document.getElementById('sumTotal').textContent         = fmt(p.tot);
    document.getElementById('sumDP').textContent            = fmt(p.dp);
    document.getElementById('sumRemaining').textContent     = fmt(p.rem);
    document.getElementById('dpAmountText').textContent     = fmt(p.dp);

    sub.textContent = nights + ' ' + window.__bkLang.nights_unit + ' • ' + fmt(p.tot);

    if (selectedMethod === 'dp') {
        dpBD.classList.remove('hidden'); dpPv.classList.remove('hidden');
    } else {
        dpBD.classList.add('hidden'); dpPv.classList.add('hidden');
    }
}

/* ── Update CTA buttons ── */
function updateCTA() {
    const btnD = document.getElementById('ctaBtnDesktop');
    const btnM = document.getElementById('ctaBtnMobile');
    const lbl  = document.getElementById('ctaBtnDesktopLabel');
    const mMet = document.getElementById('mobileCtaMethod');
    const mPrc = document.getElementById('mobileCtaPrice');

    const ready = nights > 0 && selectedMethod !== null && isAvailable;
    btnD.disabled = !ready;
    btnM.disabled = !ready;

    if (!ready) {
        lbl.textContent = nights <= 0 ? window.__bkLang.no_dates_method
                        : !isAvailable ? window.__bkLang.unavailable
                        : window.__bkLang.select_pay;
        mMet.textContent = window.__bkLang.select_method;
        mPrc.textContent = nights > 0 ? fmt(calcPrices(nights, PRICE_PER_NIGHT).tot) : '—';
        return;
    }

    const p = calcPrices(nights, PRICE_PER_NIGHT);
    const payNow = selectedMethod === 'dp' ? p.dp : p.tot;

    if (selectedMethod === 'dp') {
        lbl.textContent  = window.__bkLang.dp_pay + ' ' + fmt(payNow);
        mMet.textContent = window.__bkLang.dp_pay + ' (50%)';
    } else {
        lbl.textContent  = window.__bkLang.full_pay + ' ' + fmt(payNow);
        mMet.textContent = window.__bkLang.full_pay + ' (100%)';
    }
    mPrc.textContent = fmt(payNow);
}

/* ── Open DP modal ── */
function openDpModal() {
    const ci = document.getElementById('checkIn').value;
    const co = document.getElementById('checkOut').value;
    const p  = calcPrices(nights, PRICE_PER_NIGHT);

    document.getElementById('dpModalCheckIn').textContent   = fmtDate(ci);
    document.getElementById('dpModalCheckOut').textContent  = fmtDate(co);
    document.getElementById('dpModalNights').textContent    = nights + ' ' + window.__bkLang.nights_unit;
    document.getElementById('dpModalTotal').textContent     = fmt(p.tot);
    document.getElementById('dpModalAmount').textContent    = fmt(p.dp);
    document.getElementById('dpModalRemaining').textContent = fmt(p.rem);

    document.getElementById('dpModalOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeDpModal() {
    document.getElementById('dpModalOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

/* ── Main submit entry point ── */
function submitBooking() {
    if (!selectedMethod || nights <= 0 || !isAvailable) return;
    if (selectedMethod === 'dp') {
        openDpModal();
    } else {
        processPayment('full');
    }
}

/* ── Send to server & open Midtrans Snap ── */
function processPayment(payType) {
    const ci   = document.getElementById('checkIn').value;
    const co   = document.getElementById('checkOut').value;
    const note = document.getElementById('guestNote').value;

    // Show loading
    const btn = payType === 'dp'
        ? document.getElementById('dpPayBtn')
        : document.getElementById('ctaBtnDesktop');
    const origText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> ' + window.__bkLang.processing;

    fetch(STORE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            check_in: ci, check_out: co,
            payment_type: payType,
            guest_note: note,
        }),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled  = false;
        btn.innerHTML = origText;

        if (data.error) {
            Swal.fire({ icon:'error', title:'Gagal', text: data.error });
            return;
        }

        if (payType === 'dp') closeDpModal();

        // Open Midtrans Snap popup
        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                // Beri waktu server menerima webhook notification sebelum redirect
                // Juga trigger server-side status check sebagai fallback
                const destUrl = data.payment_type === 'dp' ? URL_PENDING : URL_SUCCESS;
                fetch('/booking/verify-payment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ order_id: data.booking_code })
                }).finally(() => {
                    window.location.href = destUrl;
                });
            },
            onPending: function(result) {
                Swal.fire({
                    icon: 'info', title: window.__bkLang.pending_title,
                    text: window.__bkLang.pending_text,
                    confirmButtonText: window.__bkLang.see_history,
                    customClass: { confirmButton: 'swal-confirm-btn' }
                }).then(() => { window.location.href = URL_PENDING; });
            },
            onError: function(result) {
                Swal.fire({ icon:'error', title: window.__bkLang.fail_title,
                    text: window.__bkLang.fail_text });
            },
            onClose: function() {
                Swal.fire({
                    icon: 'warning', title: window.__bkLang.cancel_title,
                    text: window.__bkLang.cancel_text,
                    confirmButtonText: window.__bkLang.see_history,
                    showCancelButton: true, cancelButtonText: window.__bkLang.stay_here,
                    customClass: { confirmButton: 'swal-confirm-btn' }
                }).then(r => { if (r.isConfirmed) window.location.href = URL_PENDING; });
            }
        });
    })
    .catch(err => {
        btn.disabled  = false;
        btn.innerHTML = origText;
        Swal.fire({ icon:'error', title: window.__bkLang.conn_title,
            text: window.__bkLang.conn_text });
    });
}

/* ── Init date inputs ── */
(function init() {
    const today = new Date().toISOString().split('T')[0];
    const ci    = document.getElementById('checkIn');
    const co    = document.getElementById('checkOut');
    ci.min = today;
    co.min = today;

    ci.addEventListener('change', function () {
        if (co.value && co.value <= this.value) co.value = '';
        co.min = this.value || today;
        maxCheckOutDate = null;
        co.removeAttribute('max');
        document.getElementById('checkOutNote').classList.add('hidden');
        document.getElementById('availStatus').classList.add('hidden');
        isAvailable = false;
        calcDuration();
    });
    co.addEventListener('change', calcDuration);

    // Close DP modal on overlay click
    document.getElementById('dpModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeDpModal();
    });

    // Auto-trigger if dates pre-filled from query string
    const preCI = document.getElementById('checkIn').value;
    const preCO = document.getElementById('checkOut').value;
    if (preCI) {
        document.getElementById('checkOut').min = preCI;
        if (preCO) calcDuration();
    }
})();
</script>
@endpush
@endsection
