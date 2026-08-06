<div class="space-y-3">
    @foreach($bookings as $booking)
    @php
        $cover = $booking->room?->coverPhoto?->path ?? null;
        $statusColor = match($booking->booking_status) {
            'confirmed'   => ['bg'=>'#f0fdf4','txt'=>'#166534','label'=> __('visitor.hist_status_confirmed')],
            'checked_in'  => ['bg'=>'#eff6ff','txt'=>'#1e40af','label'=> __('visitor.hist_status_checked_in')],
            'checked_out' => ['bg'=>'#f8fafc','txt'=>'#475569','label'=> __('visitor.hist_status_checked_out')],
            default       => ['bg'=>'#fefce8','txt'=>'#854d0e','label'=>ucfirst($booking->booking_status)],
        };
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden
                hover:shadow-md hover:border-yellow-200 transition-all duration-200">
        <div class="flex flex-col sm:flex-row">

            {{-- Room photo --}}
            <div class="shrink-0 w-full sm:w-32 h-36 sm:h-auto" style="min-height:100px;">
                @if($cover)
                    <img src="{{ asset($cover) }}" alt="{{ $booking->room?->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center"
                         style="background:linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);min-height:100px;">
                        <svg class="w-10 h-10" style="color:#b45309;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <p class="font-extrabold text-slate-900 text-[.95rem] leading-tight truncate">
                            {{ $booking->room?->trans('name') ?? __('visitor.breadcrumb_rooms') }}
                        </p>
                        <p class="text-[.72rem] text-slate-400 font-mono mt-0.5">
                            {{ $booking->booking_code }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                 text-[.68rem] font-bold whitespace-nowrap"
                          style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['txt'] }};">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $statusColor['label'] }}
                    </span>
                </div>

                {{-- Dates row --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-3">
                    <div class="flex items-center gap-1.5 text-[.78rem] text-slate-600">
                        <svg class="w-3.5 h-3.5 text-violet-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $booking->formatted_check_in }}</span>
                    </div>
                    <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="flex items-center gap-1.5 text-[.78rem] text-slate-600">
                        <span>{{ $booking->formatted_check_out }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                 text-[.68rem] font-bold"
                          style="background:var(--y50);color:var(--ytext);">
                        {{ __('visitor.hist_nights', ['n' => $booking->nights]) }}
                    </span>
                </div>

                {{-- Payment info + total --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                     text-[.7rem] font-semibold text-slate-600"
                              style="background:#f8fafc;border:1px solid #e2e8f0;">
                            {{ $booking->payment_type_label }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                     text-[.7rem] font-semibold text-green-700"
                              style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('visitor.hist_paid') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-[.68rem] text-slate-400">{{ __('visitor.hist_total') }}</p>
                        <p class="text-[.92rem] font-extrabold" style="color:var(--ytext);">
                            {{ $booking->formatted_total }}
                        </p>
                        @if($booking->payment_type === 'full')
                            <p class="text-[.68rem] text-green-600 font-semibold">{{ __('visitor.hist_full_paid') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($bookings->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $bookings->appends(['tab' => 'success'])->links() }}
    </div>
@endif
