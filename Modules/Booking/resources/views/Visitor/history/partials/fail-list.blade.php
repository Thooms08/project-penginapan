<div class="space-y-3">
    @foreach($bookings as $booking)
    @php
        $cover = $booking->room?->coverPhoto?->path ?? null;
        $failColor = match($booking->payment_status) {
            'failed'    => ['bg'=>'#fef2f2','txt'=>'#dc2626','icon'=>'❌','label'=>'Pembayaran Gagal'],
            'expired'   => ['bg'=>'#fefce8','txt'=>'#b45309','icon'=>'⏰','label'=>'Kedaluwarsa'],
            'cancelled' => ['bg'=>'#f8fafc','txt'=>'#64748b','icon'=>'✕','label' =>'Dibatalkan'],
            default     => ['bg'=>'#fef2f2','txt'=>'#dc2626','icon'=>'❌','label'=>'Gagal'],
        };
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden opacity-90">
        <div class="flex flex-col sm:flex-row">

            {{-- Room photo (grayscale for failed) --}}
            <div class="shrink-0 w-full sm:w-32 h-36 sm:h-auto" style="min-height:100px;">
                @if($cover)
                    <img src="{{ asset($cover) }}" alt="{{ $booking->room?->name }}"
                         class="w-full h-full object-cover" style="filter:grayscale(60%);">
                @else
                    <div class="w-full h-full flex items-center justify-center"
                         style="background:#f1f5f9;min-height:100px;">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 p-4 min-w-0">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <p class="font-extrabold text-slate-700 text-[.95rem] leading-tight truncate">
                            {{ $booking->room?->name ?? 'Kamar' }}
                        </p>
                        <p class="text-[.72rem] text-slate-400 font-mono mt-0.5">
                            {{ $booking->booking_code }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                 text-[.68rem] font-bold whitespace-nowrap"
                          style="background:{{ $failColor['bg'] }};color:{{ $failColor['txt'] }};">
                        {{ $failColor['label'] }}
                    </span>
                </div>

                {{-- Dates row --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-3">
                    <div class="flex items-center gap-1.5 text-[.78rem] text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $booking->formatted_check_in }}</span>
                    </div>
                    <svg class="w-3 h-3 text-slate-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="text-[.78rem] text-slate-400">{{ $booking->formatted_check_out }}</div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                 text-[.68rem] font-bold text-slate-400"
                          style="background:#f1f5f9;">
                        {{ $booking->nights }} malam
                    </span>
                </div>

                {{-- Payment info + total --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                     text-[.7rem] font-semibold text-slate-400"
                              style="background:#f8fafc;border:1px solid #e2e8f0;">
                            {{ $booking->payment_type_label }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                     text-[.7rem] font-semibold"
                              style="background:{{ $failColor['bg'] }};color:{{ $failColor['txt'] }};border:1px solid {{ $failColor['bg'] }};">
                            {{ $booking->payment_status_label }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-[.68rem] text-slate-400">Total</p>
                        <p class="text-[.92rem] font-extrabold text-slate-400 line-through">
                            {{ $booking->formatted_total }}
                        </p>
                    </div>
                </div>

                {{-- Reason note --}}
                @if($booking->payment_status === 'expired')
                    <p class="mt-2 text-[.72rem] text-amber-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu pembayaran habis. Silakan buat reservasi baru.
                    </p>
                @elseif($booking->payment_status === 'failed')
                    <p class="mt-2 text-[.72rem] text-red-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pembayaran gagal diproses. Silakan coba kembali.
                    </p>
                @endif

                {{-- Re-book CTA --}}
                @if($booking->room)
                    <div class="mt-3">
                        <a href="{{ route('booking.create', $booking->room->uuid) }}"
                           class="inline-flex items-center gap-1.5 text-[.75rem] font-bold px-3.5 py-1.5
                                  rounded-lg transition-colors"
                           style="background:var(--y50);color:var(--ytext);border:1px solid var(--y100);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Pesan Ulang Kamar Ini
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($bookings->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $bookings->appends(['tab' => 'failed'])->links() }}
    </div>
@endif
