@php
    $isPending = $type === 'pending';
@endphp

@if($bookings->isEmpty())
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:{{ $isPending ? '#fef9c3' : '#f0fdf4' }};">
            <svg class="w-8 h-8" style="color:{{ $isPending ? '#b45309' : '#16a34a' }};"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="font-bold text-slate-700 text-[.95rem] mb-1">
            {{ $isPending ? 'Tidak ada booking pending' : 'Tidak ada booking terkonfirmasi' }}
        </p>
        <p class="text-[.8rem] text-slate-400">
            {{ $isPending ? 'Semua DP sudah dilunasi.' : 'Belum ada tamu bayar lunas via Midtrans.' }}
        </p>
    </div>
@else

{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-[.82rem]">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th class="text-left px-5 py-3 font-bold text-slate-500 uppercase text-[.68rem] tracking-wide">Tamu & Kamar</th>
                <th class="text-left px-4 py-3 font-bold text-slate-500 uppercase text-[.68rem] tracking-wide">Tanggal</th>
                <th class="text-right px-4 py-3 font-bold text-slate-500 uppercase text-[.68rem] tracking-wide">Biaya</th>
                <th class="text-center px-4 py-3 font-bold text-slate-500 uppercase text-[.68rem] tracking-wide">Status</th>
                <th class="text-center px-4 py-3 font-bold text-slate-500 uppercase text-[.68rem] tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($bookings as $booking)
            @php
                $cover = $booking->room?->coverPhoto?->path ?? null;
                $confirmUrl = route('admin.bookings.confirm', $booking->id);
                $detailUrl  = route('admin.bookings.detail',  $booking->id);
            @endphp
            <tr class="hover:bg-slate-50 transition-colors" id="row-{{ $booking->id }}">

                {{-- Tamu & Kamar --}}
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0"
                             style="background:#f1f5f9;">
                            @if($cover)
                                <img src="{{ asset($cover) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center"
                                     style="background:linear-gradient(135deg,#fef9c3,#fde68a);">
                                    <svg class="w-5 h-5" style="color:#b45309;opacity:.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 leading-tight">{{ $booking->user->name }}</p>
                            <p class="text-slate-400 text-[.72rem]">{{ $booking->room?->name }}</p>
                            <p class="font-mono text-[.68rem] text-slate-300 mt-0.5">{{ $booking->booking_code }}</p>
                        </div>
                    </div>
                </td>

                {{-- Tanggal --}}
                <td class="px-4 py-3.5">
                    <p class="font-semibold text-slate-700">{{ $booking->formatted_check_in }}</p>
                    <p class="text-slate-400 text-[.72rem]">s.d. {{ $booking->formatted_check_out }}</p>
                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[.65rem] font-bold"
                          style="background:#fef9c3;color:#92400e;">
                        {{ $booking->nights }} malam
                    </span>
                </td>

                {{-- Biaya --}}
                <td class="px-4 py-3.5 text-right">
                    <p class="font-bold text-slate-800">{{ $booking->formatted_total }}</p>
                    @if($isPending)
                        <p class="text-[.72rem] text-green-600">DP: {{ $booking->formatted_amount_paid }}</p>
                        <p class="text-[.72rem] font-bold" style="color:#b45309;">
                            Sisa: {{ $booking->formatted_amount_remaining }}
                        </p>
                    @else
                        <span class="text-[.7rem] font-bold text-green-700">Lunas</span>
                    @endif
                </td>

                {{-- Status --}}
                <td class="px-4 py-3.5 text-center">
                    @if($isPending)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[.68rem] font-bold"
                              style="background:#fef9c3;color:#92400e;">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                            Pending DP
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[.68rem] font-bold"
                              style="background:#f0fdf4;color:#15803d;">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                            Terkonfirmasi
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        @if($isPending)
                            <button type="button" class="btn-yellow py-2 px-3 text-[.75rem]"
                                    onclick="openDPModal(
                                        {{ $booking->id }},
                                        '{{ $booking->booking_code }}',
                                        '{{ addslashes($booking->user->name) }}',
                                        '{{ addslashes($booking->room?->name) }}',
                                        '{{ $booking->formatted_check_in }}',
                                        '{{ $booking->formatted_total }}',
                                        '{{ $booking->formatted_amount_paid }}',
                                        '{{ $booking->formatted_amount_remaining }}',
                                        {{ (float) $booking->amount_remaining }},
                                        '{{ $confirmUrl }}'
                                    )">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Konfirmasi
                            </button>
                        @else
                            <button type="button" class="btn-green-sm"
                                    onclick="confirmFull(
                                        {{ $booking->id }},
                                        '{{ $booking->booking_code }}',
                                        '{{ addslashes($booking->user->name) }}',
                                        '{{ $confirmUrl }}'
                                    )">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Check-In
                            </button>
                        @endif
                        <button type="button" class="btn-slate-sm"
                                onclick="openDetail({{ $booking->id }}, '{{ $detailUrl }}')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detail
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-3">
    @foreach($bookings as $booking)
    @php
        $cover      = $booking->room?->coverPhoto?->path ?? null;
        $confirmUrl = route('admin.bookings.confirm', $booking->id);
        $detailUrl  = route('admin.bookings.detail',  $booking->id);
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="card-{{ $booking->id }}">
        <div class="flex gap-3 p-4">
            <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0" style="background:#f1f5f9;">
                @if($cover)
                    <img src="{{ asset($cover) }}" class="w-full h-full object-cover" alt="">
                @else
                    <div class="w-full h-full flex items-center justify-center"
                         style="background:linear-gradient(135deg,#fef9c3,#fde68a);">
                        <svg class="w-6 h-6" style="color:#b45309;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-bold text-slate-900 text-[.9rem] leading-tight">{{ $booking->user->name }}</p>
                        <p class="text-slate-400 text-[.72rem]">{{ $booking->room?->name }}</p>
                        <p class="font-mono text-[.65rem] text-slate-300 mt-0.5">{{ $booking->booking_code }}</p>
                    </div>
                    @if($isPending)
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[.65rem] font-bold"
                              style="background:#fef9c3;color:#92400e;">Pending DP</span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[.65rem] font-bold"
                              style="background:#f0fdf4;color:#15803d;">Terkonfirmasi</span>
                    @endif
                </div>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-0.5 text-[.75rem]">
                    <span class="text-slate-500">{{ $booking->formatted_check_in }} — {{ $booking->nights }} mlm</span>
                    <span class="font-bold text-slate-700">{{ $booking->formatted_total }}</span>
                    @if($isPending)
                        <span style="color:#b45309;" class="font-bold">Sisa: {{ $booking->formatted_amount_remaining }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-2 px-4 pb-4">
            @if($isPending)
                <button type="button" class="btn-yellow flex-1 justify-center py-2.5 text-[.8rem]"
                        onclick="openDPModal(
                            {{ $booking->id }},
                            '{{ $booking->booking_code }}',
                            '{{ addslashes($booking->user->name) }}',
                            '{{ addslashes($booking->room?->name) }}',
                            '{{ $booking->formatted_check_in }}',
                            '{{ $booking->formatted_total }}',
                            '{{ $booking->formatted_amount_paid }}',
                            '{{ $booking->formatted_amount_remaining }}',
                            {{ (float) $booking->amount_remaining }},
                            '{{ $confirmUrl }}'
                        )">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Konfirmasi Pelunasan
                </button>
            @else
                <button type="button" class="btn-green-sm flex-1 justify-center py-2.5"
                        onclick="confirmFull(
                            {{ $booking->id }},
                            '{{ $booking->booking_code }}',
                            '{{ addslashes($booking->user->name) }}',
                            '{{ $confirmUrl }}'
                        )">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Check-In Tamu
                </button>
            @endif
            <button type="button" class="btn-slate-sm px-4"
                    onclick="openDetail({{ $booking->id }}, '{{ $detailUrl }}')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Detail
            </button>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($bookings->hasPages())
    <div class="mt-5">
        {{ $bookings->appends(['tab' => $tab])->links() }}
    </div>
@endif

@endif
