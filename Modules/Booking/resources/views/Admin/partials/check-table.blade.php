
<div class="mt-8">

{{-- ── Tab Navigation ── --}}
<div style="display:flex;gap:.375rem;background:#f1f5f9;border-radius:.875rem;padding:.3rem;margin-bottom:1.25rem;">
    <button type="button" id="ctTabCI" onclick="switchCTTab('checkin')"
            style="flex:1;padding:.55rem 1rem;border-radius:.625rem;border:none;cursor:pointer;
                   font-size:.82rem;font-weight:700;display:flex;align-items:center;justify-content:center;
                   gap:.5rem;transition:all .18s;background:#fff;color:#b45309;box-shadow:0 1px 4px rgba(0,0,0,.10);">
        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Check-In
        <span id="ciBadge" style="font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#fde68a;color:#92400e;">{{ $checkedInGuests->count() }}</span>
    </button>
    <button type="button" id="ctTabCO" onclick="switchCTTab('checkout')"
            style="flex:1;padding:.55rem 1rem;border-radius:.625rem;border:none;cursor:pointer;
                   font-size:.82rem;font-weight:700;display:flex;align-items:center;justify-content:center;
                   gap:.5rem;transition:all .18s;background:transparent;color:#64748b;">
        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Check-Out
        <span id="coBadge" style="font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#e2e8f0;color:#64748b;">{{ $checkedOutGuests->count() }}</span>
    </button>
</div>

{{-- ══ PANEL CHECK-IN ══ --}}
<div id="ctPanelCI" style="display:block;">
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3"
         style="background:linear-gradient(135deg,#fefce8,#fef9c3);">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fde68a;">
                <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-[.9rem] font-bold text-slate-900">Tamu Sedang Menginap</h3>
                <p class="text-[.72rem] text-slate-500">Klik "Check-Out" untuk selesaikan kunjungan tamu</p>
            </div>
        </div>
        <span class="text-[.75rem] font-bold px-3 py-1 rounded-full" style="background:#fde68a;color:#92400e;">
            {{ $checkedInGuests->count() }} tamu aktif
        </span>
    </div>

    @if($checkedInGuests->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3" style="background:#fef9c3;">
                <svg class="w-6 h-6" style="color:#eab308;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-[.85rem] font-semibold text-slate-500">Belum ada tamu yang menginap</p>
        </div>
    @else
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-[.82rem]">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-5 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Tamu</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Kamar</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Waktu Check-In</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Rencana Check-Out</th>
                        <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Durasi</th>
                        <th class="text-right px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Total</th>
                        <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($checkedInGuests as $g)
                    <tr class="hover:bg-slate-50 transition-colors" id="ci-row-{{ $g->id }}">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-900">{{ $g->user->name }}</p>
                            <p class="text-[.7rem] text-slate-400">{{ $g->user->email }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700">{{ $g->room->name }}</p>
                            <p class="font-mono text-[.68rem] text-slate-300">{{ $g->booking_code }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 text-[.8rem]">
                            {{ $g->formatted_checked_in_at !== '-' ? $g->formatted_checked_in_at : $g->formatted_check_in }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="{{ \Carbon\Carbon::today()->gte($g->check_out_date) ? 'font-bold text-red-600' : 'text-slate-600' }} text-[.8rem]">
                                {{ $g->formatted_check_out }}
                            </span>
                            @if(\Carbon\Carbon::today()->gte($g->check_out_date))
                                <span class="ml-1 text-[.63rem] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Hari ini!</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[.7rem] font-bold" style="background:#fef9c3;color:#92400e;">
                                {{ $g->nights }} mlm
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold text-slate-700">{{ $g->formatted_total }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <form method="POST" action="{{ route('admin.check.checkout', $g->id) }}"
                                  onsubmit="return confirmCheckout(event, '{{ addslashes($g->user->name) }}', this)">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                       text-[.75rem] font-bold border border-blue-200 bg-blue-50 text-blue-700
                                       hover:bg-blue-100 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Check-Out
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="md:hidden divide-y divide-slate-50">
            @foreach($checkedInGuests as $g)
            <div class="p-4" id="ci-card-{{ $g->id }}">
                <div class="flex items-start justify-between gap-2 mb-1.5">
                    <div>
                        <p class="font-bold text-slate-900 text-[.9rem]">{{ $g->user->name }}</p>
                        <p class="text-slate-400 text-[.72rem]">{{ $g->room->name }} • {{ $g->booking_code }}</p>
                    </div>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[.65rem] font-bold" style="background:#fef9c3;color:#92400e;">{{ $g->nights }} mlm</span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-[.75rem] text-slate-500 mb-3">
                    <span>CI: {{ $g->formatted_checked_in_at !== '-' ? $g->formatted_checked_in_at : $g->formatted_check_in }}</span>
                    <span class="{{ \Carbon\Carbon::today()->gte($g->check_out_date) ? 'font-bold text-red-600':'' }}">CO: {{ $g->formatted_check_out }}</span>
                    <span class="font-bold text-slate-700">{{ $g->formatted_total }}</span>
                </div>
                <form method="POST" action="{{ route('admin.check.checkout', $g->id) }}"
                      onsubmit="return confirmCheckout(event, '{{ addslashes($g->user->name) }}', this)">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
                                   rounded-xl text-[.82rem] font-bold border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Check-Out Tamu Ini
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    @endif
</div>
</div>{{-- /ctPanelCI --}}

{{-- ══ PANEL CHECK-OUT ══ --}}
<div id="ctPanelCO" style="display:none;">
<div class="bg-white rounded-2xl shadow-sm border border-blue-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-blue-100 flex flex-wrap items-center justify-between gap-3"
         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#bfdbfe;">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <div>
                <h3 class="text-[.9rem] font-bold text-slate-900">Riwayat Check-Out</h3>
                <p class="text-[.72rem] text-slate-500">Tamu yang sudah selesai menginap (50 terakhir)</p>
            </div>
        </div>
        <span class="text-[.75rem] font-bold px-3 py-1 rounded-full" style="background:#bfdbfe;color:#1d4ed8;">
            {{ $checkedOutGuests->count() }} tamu
        </span>
    </div>

    @if($checkedOutGuests->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3" style="background:#eff6ff;">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <p class="text-[.85rem] font-semibold text-slate-500">Belum ada riwayat check-out</p>
        </div>
    @else
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-[.82rem]">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-5 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Tamu</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Kamar</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Waktu Check-In</th>
                        <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Waktu Check-Out</th>
                        <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Durasi</th>
                        <th class="text-right px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($checkedOutGuests as $g)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-900">{{ $g->user->name }}</p>
                            <p class="text-[.7rem] text-slate-400">{{ $g->user->email }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700">{{ $g->room->name }}</p>
                            <p class="font-mono text-[.68rem] text-slate-300">{{ $g->booking_code }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 text-[.8rem]">
                            {{ $g->formatted_checked_in_at }}
                        </td>
                        <td class="px-4 py-3.5 text-[.8rem]">
                            <span class="font-bold text-blue-700">{{ $g->formatted_checked_out_at }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[.7rem] font-bold" style="background:#eff6ff;color:#1d4ed8;">
                                {{ $g->nights }} mlm
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold text-slate-700">{{ $g->formatted_total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="md:hidden divide-y divide-slate-50">
            @foreach($checkedOutGuests as $g)
            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-1.5">
                    <div>
                        <p class="font-bold text-slate-900 text-[.9rem]">{{ $g->user->name }}</p>
                        <p class="text-slate-400 text-[.72rem]">{{ $g->room->name }} • {{ $g->booking_code }}</p>
                    </div>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[.65rem] font-bold" style="background:#eff6ff;color:#1d4ed8;">{{ $g->nights }} mlm</span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-[.75rem] text-slate-500">
                    <span>CI: {{ $g->formatted_checked_in_at }}</span>
                    <span class="font-bold text-blue-700">CO: {{ $g->formatted_checked_out_at }}</span>
                    <span class="font-bold text-slate-700">{{ $g->formatted_total }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
</div>{{-- /ctPanelCO --}}

</div>{{-- /mt-8 --}}

@push('scripts')
<script>
function switchCTTab(tab) {
    const isCI = tab === 'checkin';
    document.getElementById('ctPanelCI').style.display = isCI ? 'block' : 'none';
    document.getElementById('ctPanelCO').style.display = isCI ? 'none'  : 'block';
    const tabCI = document.getElementById('ctTabCI');
    const tabCO = document.getElementById('ctTabCO');
    const badge = document.getElementById(isCI ? 'ciBadge' : 'coBadge');
    const otherBadge = document.getElementById(isCI ? 'coBadge' : 'ciBadge');
    if (isCI) {
        tabCI.style.cssText += 'background:#fff!important;color:#b45309!important;box-shadow:0 1px 4px rgba(0,0,0,.10)!important;';
        tabCO.style.cssText += 'background:transparent!important;color:#64748b!important;box-shadow:none!important;';
        document.getElementById('ciBadge').style.cssText = 'font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#fde68a;color:#92400e;';
        document.getElementById('coBadge').style.cssText = 'font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#e2e8f0;color:#64748b;';
    } else {
        tabCO.style.cssText += 'background:#fff!important;color:#1d4ed8!important;box-shadow:0 1px 4px rgba(0,0,0,.10)!important;';
        tabCI.style.cssText += 'background:transparent!important;color:#64748b!important;box-shadow:none!important;';
        document.getElementById('coBadge').style.cssText = 'font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#bfdbfe;color:#1d4ed8;';
        document.getElementById('ciBadge').style.cssText = 'font-size:.62rem;font-weight:800;padding:.1rem .45rem;border-radius:9999px;background:#e2e8f0;color:#64748b;';
    }
}
function confirmCheckout(e, guestName, form) {
    e.preventDefault();
    Swal.fire({
        icon:'question', title:'Konfirmasi Check-Out',
        html:'Tamu <strong>' + guestName + '</strong> akan di-check-out sekarang. Lanjutkan?',
        showCancelButton:true, confirmButtonText:'Ya, Check-Out',
        cancelButtonText:'Batal', reverseButtons:true,
        customClass:{ confirmButton:'btn-yellow' }
    }).then(function(r){ if(r.isConfirmed) form.submit(); });
    return false;
}
</script>
@endpush
