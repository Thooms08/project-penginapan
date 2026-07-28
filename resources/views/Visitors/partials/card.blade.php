{{--
    Partial: Visitor Room Card
    Props (di-precompute oleh PublicController):
        $room              — Room model, dengan properti tambahan:
                              $room->_isAvailable  (bool)
                              $room->_hasDiscount  (bool)
                              $room->_priceDisplay (string)
                              $room->_nights       (int)
        $checkIn           — string|null  (dari request, untuk tombol modal)
        $checkOut          — string|null
--}}
@php
    $isAvailable  = $room->_isAvailable;
    $hasDiscount  = $room->_hasDiscount;
    $priceDisplay = $room->_priceDisplay;
@endphp

<div class="vis-room-card group relative bg-white rounded-2xl overflow-hidden border
            {{ $isAvailable ? 'border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1' : 'border-slate-100 shadow-sm opacity-75' }}
            transition-all duration-200 flex flex-col"
     data-available="{{ $isAvailable ? '1' : '0' }}">

    {{-- ── Photo / Thumbnail ── --}}
    <div class="card-photo relative overflow-hidden flex-shrink-0 h-48">
        @if($room->coverPhoto)
            <img src="{{ asset($room->coverPhoto->path) }}"
                 alt="{{ $room->name }}"
                 class="w-full h-full object-cover transition-transform duration-500
                        {{ $isAvailable ? 'group-hover:scale-105' : '' }}">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center gap-2
                        {{ $isAvailable ? '' : 'grayscale' }}"
                 style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
                <svg class="w-10 h-10" style="color:rgba(0,0,0,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="text-[0.72rem] font-semibold" style="color:rgba(0,0,0,0.3);">Belum ada foto</span>
            </div>
        @endif

        {{-- Overlay kamar penuh --}}
        @if(!$isAvailable)
            <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center backdrop-blur-[2px]">
                <div class="px-4 py-2 rounded-full text-white font-bold text-[0.82rem]
                            border border-white/30"
                     style="background:rgba(0,0,0,0.55);">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Tidak Tersedia
                </div>
            </div>
        @endif

        {{-- Status badge --}}
        <span class="card-status-badge absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[0.7rem] font-bold
                     {{ $isAvailable ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
            {{ $isAvailable ? '● Tersedia' : '✕ Penuh' }}
        </span>

        {{-- Diskon / foto count badge --}}
        @if($hasDiscount && $isAvailable)
            <span class="card-badge-right absolute top-3 right-3 px-2 py-1 rounded-lg text-[0.68rem] font-bold
                         bg-red-500 text-white">
                HEMAT {{ $room->formatted_discount }}
            </span>
        @elseif($room->photos->count() > 0 && $isAvailable)
            <span class="card-badge-right absolute top-3 right-3 flex items-center gap-1 px-2 py-1
                         rounded-lg bg-black/45 text-white text-[0.68rem] font-semibold">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $room->photos->count() }}
            </span>
        @endif
    </div>

    {{-- ── Card Body ── --}}
    <div class="card-body p-5 flex flex-col flex-1">
        <div class="flex-1">
            {{-- Nama kamar --}}
            <h3 class="card-name text-[0.98rem] font-bold text-slate-900 leading-tight mb-1.5 truncate">
                {{ $room->name }}
            </h3>

            {{-- Meta info --}}
            <div class="card-meta flex items-center gap-3 text-[0.78rem] text-slate-500 mb-3 flex-wrap">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $room->capacity }} org
                </span>
                @if($room->facilities->count() > 0)
                    <span class="card-meta-sep text-slate-300">·</span>
                    <span class="card-meta-facility flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ $room->facilities->count() }} fasilitas
                    </span>
                @endif
            </div>

            {{-- Harga --}}
            @if($hasDiscount && $isAvailable)
                <p class="card-price-strike text-[0.78rem] text-slate-400 line-through mb-0.5">
                    {{ $room->formatted_price }}
                </p>
                <p class="card-price text-[1.15rem] font-extrabold text-emerald-600 leading-none">
                    {{ $priceDisplay }}
                </p>
                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                    <span class="card-price-unit text-[0.72rem] text-slate-400">/malam</span>
                    @if($room->discount_min_nights > 0)
                        <span class="card-price-unit text-[0.65rem] text-slate-400">· Min. {{ $room->discount_min_nights }} malam</span>
                    @endif
                </div>
            @else
                <p class="card-price text-[1.15rem] font-extrabold leading-none {{ $isAvailable ? '' : 'text-slate-400' }}"
                   style="{{ $isAvailable ? 'color:#eab308;' : '' }}">
                    {{ $priceDisplay }}
                </p>
                <p class="card-price-unit text-[0.72rem] text-slate-400 mt-0.5">/malam</p>
            @endif
        </div>

        {{-- Divider + Action --}}
        <div class="card-actions border-t border-slate-100 mt-4 pt-4 flex flex-col gap-2">
            {{-- Tombol Lihat Detail — selalu tampil --}}
            <a href="{{ route('room.show', $room->uuid) }}@if(!empty($checkIn) || !empty($checkOut))?{{ http_build_query(array_filter(['check_in'=>$checkIn??'','check_out'=>$checkOut??''])) }}@endif"
               class="card-btn w-full flex items-center justify-center gap-2 px-4 py-2.5
                      rounded-xl font-semibold text-[0.875rem] text-slate-700
                      border border-slate-200 bg-slate-50 hover:bg-white
                      hover:border-slate-300 transition-colors no-underline">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
            </a>

            {{-- Tombol Pesan / Login --}}
            @if($isAvailable)
                @auth
                    <button type="button"
                            onclick="openBookingModal('{{ $room->uuid }}', '{{ addslashes($room->name) }}', '{{ $priceDisplay }}')"
                            class="card-btn w-full flex items-center justify-center gap-2 px-4 py-2.5
                                   rounded-xl font-semibold text-[0.875rem] text-[#713f12]
                                   transition-all duration-150 active:scale-95 border-none cursor-pointer"
                            style="background:#eab308;"
                            onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                            onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Pesan Sekarang
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="card-btn w-full flex items-center justify-center gap-2 px-4 py-2.5
                              rounded-xl font-semibold text-[0.875rem] text-[#713f12]
                              transition-all duration-150 no-underline"
                       style="background:#eab308;"
                       onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                       onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </a>
                @endauth
            @else
                <div class="card-btn-unavail w-full flex items-center justify-center gap-2 px-4 py-2.5
                            rounded-xl font-semibold text-[0.875rem]
                            bg-slate-100 text-slate-400 cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Penuh
                </div>
                <p class="card-hint text-center text-[0.72rem] text-slate-400 mt-0.5">
                    Coba tanggal lain
                </p>
            @endif
        </div>
    </div>
</div>
