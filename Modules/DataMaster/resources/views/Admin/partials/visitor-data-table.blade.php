
<style>
/* ── Medal ranks ── */
.rank-badge { display:inline-flex;align-items:center;justify-content:center;
              width:28px;height:28px;border-radius:50%;font-size:.72rem;font-weight:800;flex-shrink:0; }
.rank-1 { background:linear-gradient(135deg,#fde68a,#eab308);color:#713f12;box-shadow:0 2px 8px rgba(234,179,8,.4); }
.rank-2 { background:linear-gradient(135deg,#e2e8f0,#94a3b8);color:#1e293b;box-shadow:0 2px 6px rgba(148,163,184,.35); }
.rank-3 { background:linear-gradient(135deg,#fed7aa,#f97316);color:#7c2d12;box-shadow:0 2px 6px rgba(249,115,22,.35); }
.rank-n { background:#f1f5f9;color:#64748b; }
/* ── Asal pill (clickable) ── */
.asal-pill {
    display:inline-flex;align-items:center;gap:.35rem;
    padding:.2rem .65rem;border-radius:9999px;
    font-size:.72rem;font-weight:600;cursor:pointer;
    background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;
    transition:background .15s,color .15s,border-color .15s;
    white-space:nowrap;
}
.asal-pill:hover { background:#fef9c3;color:#b45309;border-color:#fde68a; }
/* ── Row highlight top 3 ── */
tr.rank-row-1 { background:rgba(254,252,232,.55) !important; }
tr.rank-row-2 { background:rgba(248,250,252,.7) !important; }
tr.rank-row-3 { background:rgba(255,247,237,.45) !important; }
tr.rank-row-1:hover,tr.rank-row-2:hover,tr.rank-row-3:hover { filter:brightness(.97); }
/* ── WA link ── */
.wa-link { display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:600;
           color:#15803d;text-decoration:none;transition:color .12s; }
.wa-link:hover { color:#166534; }
/* ── Modal ── */
.vd-modal-overlay { display:none;position:fixed;inset:0;z-index:80;background:rgba(0,0,0,.45);
                    backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem; }
.vd-modal-overlay.show { display:flex; }
.vd-modal-box { background:#fff;border-radius:1.5rem;width:100%;max-width:400px;
                box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;
                animation:vdIn .2s cubic-bezier(.4,0,.2,1); }
@keyframes vdIn { from{opacity:0;transform:scale(.93)} to{opacity:1;transform:scale(1)} }
.vd-modal-hdr { padding:1.125rem 1.5rem;border-bottom:1px solid #f1f5f9;
                display:flex;align-items:center;justify-content:space-between; }
.vd-modal-body { padding:1.25rem 1.5rem 1.5rem; }
.vd-close { width:30px;height:30px;border-radius:8px;border:none;background:#f1f5f9;
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            color:#64748b;transition:background .12s; }
.vd-close:hover { background:#e2e8f0;color:#0f172a; }
.loc-row { display:flex;align-items:center;gap:.875rem;padding:.75rem 0;border-bottom:1px solid #f8fafc; }
.loc-row:last-child { border-bottom:none; }
.loc-icon { width:34px;height:34px;border-radius:10px;display:flex;align-items:center;
            justify-content:center;flex-shrink:0; }
</style>

{{-- ── Section header ── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fef9c3;">
            <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-[.92rem] font-bold text-slate-900">Peringkat Visitor</h3>
            <p class="text-[.72rem] text-slate-400">Diurutkan berdasarkan frekuensi check-in terbanyak</p>
        </div>
    </div>
    <span class="text-[.75rem] font-bold px-3 py-1 rounded-full" style="background:#fef9c3;color:#92400e;">
        {{ $visitors->count() }} visitor
    </span>
</div>

@if($visitors->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4" style="background:#fef9c3;">
            <svg class="w-7 h-7" style="color:#eab308;opacity:.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-[.92rem] font-bold text-slate-700 mb-1">Belum ada data visitor</p>
        <p class="text-[.8rem] text-slate-400">Tidak ada check-in dalam periode yang dipilih.</p>
    </div>
</div>
@else

{{-- ── Desktop Table ── --}}
<div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-[.82rem]">
        <thead>
            <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide w-12">#</th>
                <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Visitor</th>
                <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Kontak</th>
                <th class="text-left px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Asal</th>
                <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Sering CI</th>
                <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Durasi</th>
                <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Jam CI</th>
                <th class="text-center px-4 py-3 font-bold text-slate-400 uppercase text-[.65rem] tracking-wide">Jam CO</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
        @foreach($visitors as $v)
        @php
            $rankClass = match($v->rank) { 1=>'rank-row-1', 2=>'rank-row-2', 3=>'rank-row-3', default=>'' };
            $badgeClass = match($v->rank) { 1=>'rank-1', 2=>'rank-2', 3=>'rank-3', default=>'rank-n' };
            $waNum = preg_replace('/\D/', '', $v->wa);
            $hasWa = $waNum && $v->wa !== '—';
            $asalData = json_encode(['city'=>$v->city,'province'=>$v->province,'country'=>$v->country,'name'=>$v->name]);
        @endphp
        <tr class="hover:brightness-95 transition-all {{ $rankClass }}" style="transition:background .15s;">
            {{-- Rank --}}
            <td class="px-4 py-3.5 text-center">
                <span class="rank-badge {{ $badgeClass }}">
                    @if($v->rank <= 3)
                        @if($v->rank === 1)🥇@elseif($v->rank === 2)🥈@else🥉@endif
                    @else
                        {{ $v->rank }}
                    @endif
                </span>
            </td>
            {{-- Visitor --}}
            <td class="px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                    @if($v->foto)
                        <img src="{{ $v->foto }}" alt="" class="w-8 h-8 rounded-full object-cover shrink-0"
                             style="border:2px solid #e2e8f0;">
                    @else
                        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center
                                    text-[.75rem] font-bold"
                             style="background:#fef9c3;color:#b45309;border:2px solid #fde68a;">
                            {{ strtoupper(substr($v->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-slate-900 leading-tight">{{ $v->name }}</p>
                        <p class="text-slate-400 text-[.7rem] truncate max-w-[160px]">{{ $v->email }}</p>
                    </div>
                </div>
            </td>
            {{-- Kontak --}}
            <td class="px-4 py-3.5">
                @if($hasWa)
                    <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener" class="wa-link">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.553 4.122 1.522 5.856L.057 23.882l6.204-1.627A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.005-1.37l-.36-.213-3.683.966.983-3.596-.234-.37A9.818 9.818 0 1112 21.818z"/>
                        </svg>
                        {{ $v->wa }}
                    </a>
                @else
                    <span class="text-slate-300 text-[.75rem]">—</span>
                @endif
            </td>
            {{-- Asal --}}
            <td class="px-4 py-3.5">
                @if($v->asal_full !== '—')
                    <button type="button" class="asal-pill" onclick='openAsalModal(@json($asalData))'>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $v->asal_short }}
                    </button>
                @else
                    <span class="text-slate-300 text-[.75rem]">—</span>
                @endif
            </td>
            {{-- Sering CI --}}
            <td class="px-4 py-3.5 text-center">
                <div class="flex flex-col items-center gap-0.5">
                    <span class="text-[1rem] font-extrabold text-slate-900">{{ $v->checkin_count }}×</span>
                    @php $pct = $visitors->max('checkin_count') > 0 ? round($v->checkin_count / $visitors->max('checkin_count') * 100) : 0; @endphp
                    <div class="w-16 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%;background:#eab308;"></div>
                    </div>
                </div>
            </td>
            {{-- Durasi --}}
            <td class="px-4 py-3.5 text-center">
                <span class="inline-block px-2.5 py-1 rounded-full text-[.72rem] font-bold"
                      style="background:#eff6ff;color:#1d4ed8;">
                    {{ $v->avg_nights > 0 ? $v->avg_nights.' mlm' : '—' }}
                </span>
            </td>
            {{-- Jam CI --}}
            <td class="px-4 py-3.5 text-center">
                <span class="text-[.78rem] font-bold text-slate-700 font-mono">{{ $v->ci_label }}</span>
            </td>
            {{-- Jam CO --}}
            <td class="px-4 py-3.5 text-center">
                <span class="text-[.78rem] font-bold text-slate-700 font-mono">{{ $v->co_label }}</span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- ── Mobile Cards ── --}}
<div class="md:hidden space-y-3">
    @foreach($visitors as $v)
    @php
        $badgeClass = match($v->rank) { 1=>'rank-1', 2=>'rank-2', 3=>'rank-3', default=>'rank-n' };
        $rankClass  = match($v->rank) { 1=>'border-yellow-200 bg-yellow-50/50', 2=>'border-slate-200', 3=>'border-orange-100 bg-orange-50/30', default=>'border-slate-200' };
        $waNum = preg_replace('/\D/', '', $v->wa);
        $hasWa = $waNum && $v->wa !== '—';
        $asalData = json_encode(['city'=>$v->city,'province'=>$v->province,'country'=>$v->country,'name'=>$v->name]);
        $pct = $visitors->max('checkin_count') > 0 ? round($v->checkin_count / $visitors->max('checkin_count') * 100) : 0;
    @endphp
    <div class="bg-white rounded-2xl border {{ $rankClass }} shadow-sm overflow-hidden">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <span class="rank-badge {{ $badgeClass }} shrink-0 mt-0.5">
                    @if($v->rank===1)🥇@elseif($v->rank===2)🥈@elseif($v->rank===3)🥉@else{{ $v->rank }}@endif
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-[.9rem] leading-tight">{{ $v->name }}</p>
                            <p class="text-slate-400 text-[.72rem] truncate">{{ $v->email }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[1.1rem] font-extrabold text-slate-900">{{ $v->checkin_count }}×</p>
                            <p class="text-[.65rem] text-slate-400">check-in</p>
                        </div>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden mt-2">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%;background:#eab308;"></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-3 text-[.75rem]">
                <div>
                    <p class="text-slate-400 font-semibold uppercase text-[.62rem] tracking-wide">Asal</p>
                    @if($v->asal_full !== '—')
                        <button type="button" class="asal-pill mt-0.5" onclick='openAsalModal(@json($asalData))'>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $v->asal_short }}
                        </button>
                    @else
                        <span class="text-slate-300">—</span>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 font-semibold uppercase text-[.62rem] tracking-wide">WA</p>
                    @if($hasWa)
                        <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener" class="wa-link mt-0.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.553 4.122 1.522 5.856L.057 23.882l6.204-1.627A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.005-1.37l-.36-.213-3.683.966.983-3.596-.234-.37A9.818 9.818 0 1112 21.818z"/>
                            </svg>
                            {{ $v->wa }}
                        </a>
                    @else
                        <span class="text-slate-300">—</span>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 font-semibold uppercase text-[.62rem] tracking-wide">Durasi Rata²</p>
                    <p class="font-bold text-blue-600 mt-0.5">{{ $v->avg_nights > 0 ? $v->avg_nights.' malam' : '—' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 font-semibold uppercase text-[.62rem] tracking-wide">Jam CI / CO</p>
                    <p class="font-bold text-slate-700 font-mono mt-0.5 text-[.72rem]">
                        {{ $v->ci_label }} / {{ $v->co_label }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endif {{-- end if visitors not empty --}}

{{-- ══ MODAL: Asal Visitor ══ --}}
<div class="vd-modal-overlay" id="asalModal">
    <div class="vd-modal-box">
        <div class="vd-modal-hdr">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fef9c3;">
                    <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900 text-[.9rem]">Asal Visitor</p>
                    <p class="text-[.7rem] text-slate-400" id="asalModalName">—</p>
                </div>
            </div>
            <button class="vd-close" onclick="closeAsalModal()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="vd-modal-body">
            <div class="loc-row">
                <div class="loc-icon" style="background:#fef9c3;">
                    <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[.7rem] text-slate-400 font-semibold uppercase tracking-wide">Kota</p>
                    <p class="font-bold text-slate-900 text-[.88rem]" id="asalCity">—</p>
                </div>
            </div>
            <div class="loc-row">
                <div class="loc-icon" style="background:#eff6ff;">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[.7rem] text-slate-400 font-semibold uppercase tracking-wide">Provinsi</p>
                    <p class="font-bold text-slate-900 text-[.88rem]" id="asalProvince">—</p>
                </div>
            </div>
            <div class="loc-row">
                <div class="loc-icon" style="background:#f0fdf4;">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[.7rem] text-slate-400 font-semibold uppercase tracking-wide">Negara</p>
                    <p class="font-bold text-slate-900 text-[.88rem]" id="asalCountry">—</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAsalModal(data) {
    document.getElementById('asalModalName').textContent = data.name || '—';
    document.getElementById('asalCity').textContent     = data.city     || '—';
    document.getElementById('asalProvince').textContent = data.province || '—';
    document.getElementById('asalCountry').textContent  = data.country  || '—';
    const overlay = document.getElementById('asalModal');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeAsalModal() {
    document.getElementById('asalModal').classList.remove('show');
    document.body.style.overflow = '';
}
document.getElementById('asalModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAsalModal();
});
</script>
@endpush
