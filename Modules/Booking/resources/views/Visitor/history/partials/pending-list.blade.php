{{-- Penjelasan status pending --}}
<div class="mb-4 p-3.5 rounded-xl flex items-start gap-3 text-[.8rem]"
     style="background:#fefce8;border:1px solid #fde68a;">
    <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="font-bold text-amber-800 mb-0.5">{{ __('visitor.hist_dp_confirmed_title') }}</p>
        <p class="text-amber-700 leading-relaxed">{{ __('visitor.hist_dp_confirmed_desc') }}</p>
    </div>
</div>

<div class="space-y-3">
    @foreach($bookings as $booking)
    @php
        $cover = $booking->room?->coverPhoto?->path ?? null;
        $isCheckedIn = $booking->booking_status === 'checked_in';
    @endphp
    <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-200 hover:shadow-md"
         style="border-color:#fde68a;box-shadow:0 1px 6px rgba(234,179,8,.12);">
        <div class="flex flex-col sm:flex-row">

            {{-- Room photo --}}
            <div class="shrink-0 w-full sm:w-32 h-36 sm:h-auto" style="min-height:100px;">
                @if($cover)
                    <img src="{{ asset($cover) }}" alt="{{ $booking->room?->trans('name') }}"
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

                {{-- Header row --}}
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <p class="font-extrabold text-slate-900 text-[.95rem] leading-tight truncate">
                            {{ $booking->room?->trans('name') ?? __('visitor.breadcrumb_rooms') }}
                        </p>
                        <p class="text-[.72rem] text-slate-400 font-mono mt-0.5">
                            {{ $booking->booking_code }}
                        </p>
                    </div>
                    {{-- Status badge --}}
                    @if($isCheckedIn)
                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-[.68rem] font-bold whitespace-nowrap"
                              style="background:#eff6ff;color:#1e40af;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('visitor.hist_status_checked_in_pending') }}
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-[.68rem] font-bold whitespace-nowrap"
                              style="background:#fef9c3;color:#92400e;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('visitor.hist_status_dp_confirmed') }}
                        </span>
                    @endif
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
                    <div class="text-[.78rem] text-slate-600">{{ $booking->formatted_check_out }}</div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                 text-[.68rem] font-bold"
                          style="background:var(--y50);color:var(--ytext);">
                        {{ __('visitor.hist_pending_nights', ['n' => $booking->nights]) }}
                    </span>
                </div>

                {{-- Payment summary --}}
                <div class="rounded-xl p-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-[.65rem] text-slate-400 mb-0.5">{{ __('visitor.hist_total_cost') }}</p>
                            <p class="text-[.82rem] font-extrabold text-slate-700">
                                {{ $booking->formatted_total }}
                            </p>
                        </div>
                        <div style="border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p class="text-[.65rem] text-slate-400 mb-0.5">{{ __('visitor.hist_dp_paid') }}</p>
                            <p class="text-[.82rem] font-extrabold text-green-700">
                                {{ $booking->formatted_amount_paid }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[.65rem] text-amber-600 mb-0.5 font-semibold">{{ __('visitor.hist_remaining') }}</p>
                            <p class="text-[.82rem] font-extrabold" style="color:#b45309;">
                                {{ $booking->formatted_amount_remaining }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Reminder --}}
                <div class="flex items-center gap-2 text-[.73rem] text-amber-700 font-semibold">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {!! __('visitor.hist_reminder', ['amount' => '<strong>' . $booking->formatted_amount_remaining . '</strong>', 'date' => '<strong>' . $booking->formatted_check_in . '</strong>']) !!}
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($bookings->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $bookings->appends(['tab' => 'pending'])->links() }}
    </div>
@endif
