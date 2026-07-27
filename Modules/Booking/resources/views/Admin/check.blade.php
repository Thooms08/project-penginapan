@extends('Admin.layouts.app')

@section('title', 'Check In & Out')
@section('page_title', 'Check In & Out')
@section('page_subtitle', 'Atur jam check-in dan check-out harian')

@section('content')
<style>
.btn-yellow {
    background:#eab308; color:#713f12; border:none; cursor:pointer;
    font-weight:700; border-radius:.75rem; padding:.625rem 1.5rem;
    font-size:.875rem; transition:background .15s,color .15s;
    display:inline-flex; align-items:center; gap:.5rem;
}
.btn-yellow:hover { background:#ca8a04; color:#fff; }
.btn-red-sm {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.3rem .65rem; border-radius:.5rem; font-size:.72rem; font-weight:600;
    border:1px solid #fecaca; background:#fff5f5; color:#dc2626; cursor:pointer;
    white-space:nowrap; transition:background .12s;
}
.btn-red-sm:hover { background:#fee2e2; }
.picker-wrap { position:relative; }
.picker-display {
    display:flex; align-items:center; gap:.4rem;
    padding:.45rem .75rem; background:#f8fafc; border:1px solid #e2e8f0;
    border-radius:.625rem; cursor:pointer; user-select:none;
    font-size:.8rem; color:#1e293b; min-height:38px; transition:all .15s;
}
.picker-display:hover,.picker-display.open {
    border-color:#eab308; background:#fff; box-shadow:0 0 0 3px rgba(234,179,8,.15);
}
.p-hdr {
    display:flex; align-items:center; justify-content:space-between;
    padding:.55rem .875rem; background:#fefce8; border-bottom:1px solid #fef9c3;
}
.p-hdr span { font-size:.82rem; font-weight:700; color:#713f12; }
.p-nav {
    width:24px; height:24px; border-radius:5px; border:none; background:#fde68a;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    color:#713f12; transition:background .15s; flex-shrink:0;
}
.p-nav:hover { background:#eab308; }
.cal-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:.75rem; border-radius:6px; cursor:pointer; font-weight:500;
    border:none; background:transparent; color:#334155; transition:all .1s;
}
.cal-day:hover:not(.disabled):not(.other)  { background:#fef9c3; color:#713f12; }
.cal-day.today  { background:#fef3c7; color:#b45309; font-weight:700; }
.cal-day.sel    { background:#eab308 !important; color:#713f12 !important; font-weight:700; }
.cal-day.other  { color:#cbd5e1; pointer-events:none; }
/* Style baru untuk disable tanggal */
.cal-day.disabled { 
    background:#f1f5f9 !important; 
    color:#94a3b8 !important; 
    pointer-events:none; 
    text-decoration:line-through; 
    opacity: 0.6;
}
.time-col {
    width: 100%; /* Bikin kolom memenuhi lebar wrapper-nya */
    max-height: 170px; 
    overflow-y: auto;
    scroll-snap-type: y mandatory;
    scrollbar-width: thin; 
    scrollbar-color: #fde68a transparent;
    padding-right: 4px; /* Kasih jarak biar scrollbar gak nempel sama angka */
}
.time-col::-webkit-scrollbar { width: 4px; } /* Agak ditebelin dikit */
.time-col::-webkit-scrollbar-thumb { background: #fde68a; border-radius: 4px; }

.t-item {
    text-align: center; 
    padding: .55rem 1rem; /* Padding horizontal ditambah biar kotak kuningnya lebar */
    margin: 0.15rem 0; /* Kasih jarak atas-bawah dikit antar angka */
    font-size: 1.05rem; /* Angkanya digedein dikit */
    font-weight: 500;
    cursor: pointer; 
    border-radius: 8px; /* Sudut lebih membulat */
    color: #475569; 
    scroll-snap-align: start;
    transition: all .1s;
}
.t-item:hover { background: #fefce8; color: #713f12; }
.t-item.active { background: #eab308; color: #fff; font-weight: 700; } /* Text dibikin putih saat aktif biar lebih kontras */
.row-card {
    display:grid; grid-template-columns:1.5rem 1fr auto auto 1.75rem;
    align-items:center; gap:.5rem;
    background:#f8fafc; border:1px solid #e2e8f0;
    border-radius:.75rem; padding:.6rem .75rem;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(-8px) scale(.98); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
.modal-box { animation:fadeUp .2s ease; }
</style>

{{-- Flash --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded',function(){
    Swal.fire({icon:'success',title:'Berhasil',text:@json(session('success')),
        timer:3000,timerProgressBar:true,showConfirmButton:false,
        toast:true,position:'top-end',customClass:{popup:'swal-toast-popup'}});
});
</script>
@endif
@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded',function(){
    Swal.fire({icon:'error',title:'Gagal',text:@json(session('error')),
        confirmButtonText:'Tutup',customClass:{confirmButton:'swal-confirm-btn'}});
});
</script>
@endif

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Check In &amp; Out</h2>
        <p class="text-[0.82rem] text-slate-500 mt-0.5">Atur jam check-in dan check-out untuk tanggal tertentu</p>
    </div>
    <button type="button" onclick="openSettingModal()" class="btn-yellow">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Atur Jam Check-In &amp; Out
    </button>
</div>

{{-- 2 Cards hari ini --}}
<div class="flex flex-col sm:flex-row gap-5 mb-8">

    {{-- Card Check-In --}}
    <div class="flex-1 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 min-h-[164px]
                flex flex-col items-center justify-center text-center gap-2">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:#fefce8;">
            <svg class="w-5 h-5" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
        </div>
        <p class="text-[0.72rem] font-semibold text-slate-500 uppercase tracking-wide">Check-In Hari Ini</p>
        @if($todayCheckIns->count() > 0)
            <p class="text-[2.1rem] font-extrabold text-slate-900 leading-none">
                {{ $todayCheckIns->first()->formatted_time }}
            </p>
            <p class="text-xs text-slate-400">
                @if($todayCheckIns->count() > 1)
                    +{{ $todayCheckIns->count() - 1 }} jam lainnya
                @else
                    {{ $todayCheckIns->first()->formatted_date }}
                @endif
            </p>
            <button type="button" onclick="openDetailModal('check_in')"
                    class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                           text-[0.72rem] font-semibold border border-yellow-200 bg-yellow-50
                           text-yellow-800 hover:bg-yellow-100 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5
                           c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
            </button>
        @else
            <p class="text-[1.9rem] font-extrabold text-slate-300 leading-none">--:--</p>
            <p class="text-xs text-slate-400">Belum diatur hari ini</p>
        @endif
    </div>

    {{-- Card Check-Out --}}
    <div class="flex-1 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 min-h-[164px]
                flex flex-col items-center justify-center text-center gap-2">
        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </div>
        <p class="text-[0.72rem] font-semibold text-slate-500 uppercase tracking-wide">Check-Out Hari Ini</p>
        @if($todayCheckOuts->count() > 0)
            <p class="text-[2.1rem] font-extrabold text-blue-600 leading-none">
                {{ $todayCheckOuts->first()->formatted_time }}
            </p>
            <p class="text-xs text-slate-400">
                @if($todayCheckOuts->count() > 1)
                    +{{ $todayCheckOuts->count() - 1 }} jam lainnya
                @else
                    {{ $todayCheckOuts->first()->formatted_date }}
                @endif
            </p>
            <button type="button" onclick="openDetailModal('check_out')"
                    class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                           text-[0.72rem] font-semibold border border-blue-200 bg-blue-50
                           text-blue-700 hover:bg-blue-100 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5
                           c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
            </button>
        @else
            <p class="text-[1.9rem] font-extrabold text-slate-300 leading-none">--:--</p>
            <p class="text-xs text-slate-400">Belum diatur hari ini</p>
        @endif
    </div>
</div>

{{-- Tabel tamu --}}
@include('booking::Admin.partials.check-table')

{{-- Hidden delete forms --}}
@foreach($allSettings as $s)
<form id="delForm_{{ $s->id }}" method="POST"
      action="{{ route('admin.check.destroy', $s->id) }}" class="hidden">
    @csrf @method('DELETE')
</form>
@endforeach

{{-- ══════════════════════════════════════════════════
     MODAL UTAMA — Atur Jam Check-In & Out
══════════════════════════════════════════════════ --}}
<div id="settingModal" class="fixed inset-0 z-40 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSettingModal()"></div>
    <div class="modal-box relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl
                max-h-[90vh] flex flex-col z-10">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0"
             style="background:linear-gradient(135deg,#fefce8,#fef9c3);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:#fde68a;">
                    <svg class="w-4 h-4" style="color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[0.9rem] font-bold text-slate-900">Atur Jam Check-In &amp; Out</p>
                    <p class="text-[0.7rem] text-slate-500">Tambah banyak tanggal &amp; jam sekaligus</p>
                </div>
            </div>
            <button type="button" onclick="closeSettingModal()"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white flex items-center justify-center
                           text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body: 2 kolom --}}
        <div class="flex flex-col md:flex-row flex-1 min-h-0">

            {{-- ── KOLOM KIRI: Tambah Jam + Tombol ── --}}
            <div class="md:w-[52%] flex flex-col border-b md:border-b-0 md:border-r border-slate-100"
                 style="min-height:0; max-height:calc(90vh - 64px);">

                <div class="flex-1 overflow-y-auto px-5 pt-5 pb-3 min-h-0"
                     style="scrollbar-width:thin; scrollbar-color:#e2e8f0 transparent;">
                    <p class="text-[0.78rem] font-bold text-slate-600 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Jam Baru
                    </p>

                    <form id="settingForm" method="POST" action="{{ route('admin.check.store') }}">
                        @csrf
                        <div id="rowsWrap" class="space-y-2"></div>

                        <button type="button" onclick="addRow()"
                                class="mt-3 w-full flex items-center justify-center gap-2 py-2.5
                                       border-2 border-dashed border-slate-200 rounded-xl
                                       text-[0.78rem] font-semibold text-slate-400
                                       hover:border-yellow-300 hover:text-yellow-600 hover:bg-yellow-50
                                       transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Baris
                        </button>
                    </form>
                </div>

                <div class="shrink-0 px-5 py-4 border-t border-slate-100 bg-slate-50/80">
                    <p class="text-[0.68rem] text-slate-400 mb-2.5">
                        Klik <strong class="text-slate-600">Simpan Semua</strong> untuk menyimpan baris yang sudah diisi di atas.
                    </p>
                    <div class="flex gap-2.5">
                        <button type="button" onclick="closeSettingModal()"
                                class="flex-1 inline-flex justify-center items-center
                                       px-4 py-2.5 bg-white border border-slate-300 text-slate-700
                                       text-[0.8rem] font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                            Tutup
                        </button>
                        <button type="submit" form="settingForm"
                                class="flex-1 btn-yellow justify-center py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Semua
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── KOLOM KANAN: Data Tersimpan ── --}}
            <div class="md:w-[48%] flex flex-col" style="min-height:0; max-height:calc(90vh - 64px);">
                <div class="px-5 pt-4 pb-3 shrink-0 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <p class="text-[0.78rem] font-bold text-slate-600">Data Tersimpan</p>
                        <span id="savedCount"
                              class="text-[0.65rem] font-bold text-slate-400 bg-slate-100
                                     px-2 py-0.5 rounded-full">0</span>
                    </div>

                    <div class="flex gap-1 mt-3">
                        <button type="button" id="tabCI"
                                onclick="switchTab('check_in')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg
                                       text-[0.72rem] font-semibold transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                            </svg>
                            Check-In
                            <span id="cntCI"
                                  class="text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full bg-yellow-200 text-yellow-800">0</span>
                        </button>
                        <button type="button" id="tabCO"
                                onclick="switchTab('check_out')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg
                                       text-[0.72rem] font-semibold transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                            </svg>
                            Check-Out
                            <span id="cntCO"
                                  class="text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700">0</span>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-3 min-h-0"
                     style="scrollbar-width:thin; scrollbar-color:#e2e8f0 transparent;">
                    <div id="savedListCI" class="space-y-1.5"></div>
                    <div id="savedListCO" class="space-y-1.5 hidden"></div>
                    <p id="savedEmpty"
                       class="hidden text-center text-[0.75rem] text-slate-400 py-8">
                        Belum ada data tersimpan
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL DETAIL JAM (HARI INI)
══════════════════════════════════════════════════ --}}
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDetailModal()"></div>
    <div class="modal-box relative bg-white rounded-2xl shadow-2xl w-full max-w-xs z-10">
        <div id="detailHdr" class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100"
             style="background:linear-gradient(135deg,#fefce8,#fef9c3);">
            <p id="detailTitle" class="text-[0.88rem] font-bold text-slate-900"></p>
            <button type="button" onclick="closeDetailModal()"
                    class="w-6 h-6 rounded-lg border border-slate-200 bg-white flex items-center justify-center
                           text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-4 max-h-72 overflow-y-auto">
            <div id="detailList" class="space-y-2"></div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL PICKER (DATE & TIME) - BARU
══════════════════════════════════════════════════ --}}
{{-- MODAL CALENDAR --}}
<div id="globalDateModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeGlobalDateModal()"></div>
    <div class="modal-box relative bg-white rounded-2xl shadow-2xl w-full max-w-[280px] overflow-hidden z-10">
        <div id="globalDateContent"></div>
    </div>
</div>

{{-- MODAL TIME --}}
<div id="globalTimeModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeGlobalTimeModal()"></div>
    <div class="modal-box relative bg-white rounded-2xl shadow-2xl w-full max-w-[260px] overflow-hidden z-10">
        <div id="globalTimeContent"></div>
        <div class="px-4 pb-4 pt-2">
            <button type="button" onclick="closeGlobalTimeModal()" 
                class="w-full btn-yellow justify-center py-2 text-[0.8rem]">Terapkan Jam</button>
        </div>
    </div>
</div>

<script>
/* ── Data dari server ── */
const ALL_SETTINGS   = @json($jsAllSettings);
const CI_TODAY       = @json($jsTodayCheckIns);
const CO_TODAY       = @json($jsTodayCheckOuts);

/* ── State ── */
const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'];
const DSHORT = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
let   rowIdx = 0;
let   activePickerIdx = null; // Melacak row mana yg sedang buka modal picker
const calState  = {};   
const timeState = {};   

/* ════════════════════════════════════════
   MODAL UTAMA & DETAIL
════════════════════════════════════════ */
function openSettingModal() {
    document.getElementById('settingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    rowIdx = 0;
    activeTab = 'check_in';
    document.getElementById('rowsWrap').innerHTML = '';
    addRow();
    renderSaved();
}
function closeSettingModal() {
    document.getElementById('settingModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openDetailModal(type) {
    const isCI  = type === 'check_in';
    const items = isCI ? CI_TODAY : CO_TODAY;
    const title = isCI ? 'Detail Jam Check-In Hari Ini' : 'Detail Jam Check-Out Hari Ini';
    const bg    = isCI ? 'linear-gradient(135deg,#fefce8,#fef9c3)' : 'linear-gradient(135deg,#eff6ff,#dbeafe)';
    document.getElementById('detailHdr').style.background = bg;
    document.getElementById('detailTitle').textContent = title;
    const list = document.getElementById('detailList');
    list.innerHTML = '';
    if (!items.length) {
        list.innerHTML = '<p class="text-center text-sm text-slate-400 py-4">Belum ada jam yang diatur hari ini</p>';
    } else {
        items.forEach(function(it, i) {
            const d = document.createElement('div');
            d.className = 'flex items-center gap-3 px-3 py-2.5 rounded-xl ' +
                (isCI ? 'bg-yellow-50 border border-yellow-100' : 'bg-blue-50 border border-blue-100');
            d.innerHTML = '<span class="text-[0.65rem] font-bold text-slate-400 w-4 text-right shrink-0">' + (i+1) + '</span>' +
                '<span class="text-[1.3rem] font-extrabold ' + (isCI ? 'text-yellow-700' : 'text-blue-700') + ' leading-none">' + it.time + '</span>' +
                (it.notes ? '<span class="text-[0.7rem] text-slate-400 truncate">' + it.notes + '</span>' : '');
            list.appendChild(d);
        });
    }
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    if(document.getElementById('settingModal').classList.contains('hidden')){
        document.body.style.overflow = '';
    }
}

/* ════════════════════════════════════════
   RENDER SAVED PREVIEW
════════════════════════════════════════ */
let activeTab = 'check_in';

function renderSaved() {
    const listCI = document.getElementById('savedListCI');
    const listCO = document.getElementById('savedListCO');
    const empty  = document.getElementById('savedEmpty');
    const cnt    = document.getElementById('savedCount');
    const cntCI  = document.getElementById('cntCI');
    const cntCO  = document.getElementById('cntCO');

    listCI.innerHTML = '';
    listCO.innerHTML = '';

    const ciItems = ALL_SETTINGS.filter(function(s){ return s.type === 'check_in'; });
    const coItems = ALL_SETTINGS.filter(function(s){ return s.type === 'check_out'; });

    cnt.textContent  = ALL_SETTINGS.length;
    cntCI.textContent = ciItems.length;
    cntCO.textContent = coItems.length;

    if (ciItems.length) {
        ciItems.forEach(function(s) { listCI.appendChild(buildSavedRow(s)); });
    } else {
        const p = document.createElement('p');
        p.className = 'text-center text-[0.73rem] text-slate-400 py-6';
        p.textContent = 'Belum ada data check-in';
        listCI.appendChild(p);
    }

    if (coItems.length) {
        coItems.forEach(function(s) { listCO.appendChild(buildSavedRow(s)); });
    } else {
        const p = document.createElement('p');
        p.className = 'text-center text-[0.73rem] text-slate-400 py-6';
        p.textContent = 'Belum ada data check-out';
        listCO.appendChild(p);
    }

    if (empty) empty.classList.add('hidden');
    switchTab(activeTab);
}

function buildSavedRow(s) {
    const isCI = s.type === 'check_in';
    const d = document.createElement('div');
    d.style.cssText =
        'display:flex;align-items:center;gap:.5rem;' +
        'background:' + (isCI ? '#fefce8' : '#eff6ff') + ';' +
        'border:1px solid ' + (isCI ? '#fde68a' : '#bfdbfe') + ';' +
        'border-radius:.625rem;padding:.45rem .65rem;';
    d.innerHTML =
        '<span style="font-size:.75rem;font-weight:700;' +
            (isCI ? 'color:#b45309;' : 'color:#1d4ed8;') +
            'white-space:nowrap;min-width:36px;">' + s.time + '</span>' +
        '<span style="font-size:.7rem;color:#64748b;white-space:nowrap;flex:1;">' + s.short + '</span>' +
        (s.notes
            ? '<span style="font-size:.65rem;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:80px;" title="' + escJ(s.notes) + '">' + s.notes + '</span>'
            : '') +
        '<button type="button" onclick="confirmDel(' + s.id + ',\'' + escJ(s.short) + '\',\'' + escJ(s.time) + '\')" ' +
            'class="btn-red-sm shrink-0" style="padding:.2rem .5rem;">' +
            '<svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" ' +
                'd="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7' +
                'm5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' +
            '</svg>' +
        '</button>';
    return d;
}

function switchTab(type) {
    activeTab = type;
    const isCI = type === 'check_in';
    const listCI = document.getElementById('savedListCI');
    const listCO = document.getElementById('savedListCO');
    const tabCI  = document.getElementById('tabCI');
    const tabCO  = document.getElementById('tabCO');

    listCI.classList.toggle('hidden', !isCI);
    listCO.classList.toggle('hidden',  isCI);

    if (isCI) {
        tabCI.style.cssText = 'flex:1;display:flex;align-items:center;justify-content:center;gap:.375rem;padding:.375rem .5rem;border-radius:.5rem;font-size:.72rem;font-weight:700;background:#fef9c3;color:#713f12;border:1.5px solid #fde68a;cursor:pointer;transition:all .15s;';
        tabCO.style.cssText = 'flex:1;display:flex;align-items:center;justify-content:center;gap:.375rem;padding:.375rem .5rem;border-radius:.5rem;font-size:.72rem;font-weight:600;background:transparent;color:#94a3b8;border:1.5px solid transparent;cursor:pointer;transition:all .15s;';
    } else {
        tabCO.style.cssText = 'flex:1;display:flex;align-items:center;justify-content:center;gap:.375rem;padding:.375rem .5rem;border-radius:.5rem;font-size:.72rem;font-weight:700;background:#dbeafe;color:#1d4ed8;border:1.5px solid #bfdbfe;cursor:pointer;transition:all .15s;';
        tabCI.style.cssText = 'flex:1;display:flex;align-items:center;justify-content:center;gap:.375rem;padding:.375rem .5rem;border-radius:.5rem;font-size:.72rem;font-weight:600;background:transparent;color:#94a3b8;border:1.5px solid transparent;cursor:pointer;transition:all .15s;';
    }
}
function escJ(s) { return String(s).replace(/'/g,"\\'"); }

/* ════════════════════════════════════════
   ADD / REMOVE ROW
════════════════════════════════════════ */
function addRow() {
    const idx  = rowIdx++;
    const wrap = document.getElementById('rowsWrap');
    const today = fmtDate(new Date());

    calState[idx]  = { y: new Date().getFullYear(), m: new Date().getMonth() };
    timeState[idx] = { h: 14, m: 0 };

    const row = document.createElement('div');
    row.className = 'row-card';
    row.id = 'row_' + idx;
    row.innerHTML =
        '<span style="font-size:.68rem;font-weight:700;color:#94a3b8;text-align:center;">' + (idx+1) + '</span>' +
        '<div class="picker-wrap" id="dw_' + idx + '">' +
            '<div class="picker-display" id="dd_' + idx + '" onclick="openGlobalDateModal(' + idx + ')">' +
                '<svg style="width:13px;height:13px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' +
                '</svg>' +
                '<span id="dt_' + idx + '" style="flex:1;font-size:.75rem;">Pilih tgl</span>' +
            '</div>' +
            '<input type="hidden" name="rows[' + idx + '][date]" id="dv_' + idx + '">' +
        '</div>' +
        '<select name="rows[' + idx + '][type]" id="ty_' + idx + '" ' +
            'style="padding:.4rem .5rem;background:#f8fafc;border:1px solid #e2e8f0;' +
            'border-radius:.625rem;font-size:.75rem;font-weight:600;color:#334155;' +
            'outline:none;cursor:pointer;min-width:100px;">' +
            '<option value="check_in">Check-In</option>' +
            '<option value="check_out">Check-Out</option>' +
        '</select>' +
        '<div class="picker-wrap" id="tw_' + idx + '">' +
            '<div class="picker-display" id="td_' + idx + '" onclick="openGlobalTimeModal(' + idx + ')">' +
                '<svg style="width:13px;height:13px;color:#eab308;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>' +
                '</svg>' +
                '<span id="tt_' + idx + '" style="flex:1;font-size:.75rem;">Pilih jam</span>' +
            '</div>' +
            '<input type="hidden" name="rows[' + idx + '][time]" id="tv_' + idx + '">' +
        '</div>' +
        '<button type="button" onclick="removeRow(' + idx + ')" ' +
            'style="width:26px;height:26px;border-radius:6px;border:1px solid #fecaca;' +
            'background:#fff5f5;color:#dc2626;display:flex;align-items:center;justify-content:center;' +
            'cursor:pointer;flex-shrink:0;transition:background .12s;" ' +
            'onmouseover="this.style.background=\'#fee2e2\'" onmouseout="this.style.background=\'#fff5f5\'">' +
            '<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>' +
            '</svg>' +
        '</button>';

    wrap.appendChild(row);

    commitDate(idx, today);
    commitTime(idx);
}

function removeRow(idx) {
    const el = document.getElementById('row_' + idx);
    if (el) el.remove();
}

/* ════════════════════════════════════════
   MODAL DATE PICKER LOGIC
════════════════════════════════════════ */
function openGlobalDateModal(idx) {
    activePickerIdx = idx;
    document.getElementById('globalDateModal').classList.remove('hidden');
    renderGlobalDate();
}
function closeGlobalDateModal() {
    document.getElementById('globalDateModal').classList.add('hidden');
    activePickerIdx = null;
}

function renderGlobalDate() {
    const idx  = activePickerIdx;
    if (idx === null) return;

    const s    = calState[idx];
    const sel  = document.getElementById('dv_' + idx).value;
    const now  = new Date();
    const p    = document.getElementById('globalDateContent');

    // Cek tanggal mana saja yang disable (berdasarkan tipe saat ini)
    const currentType = document.getElementById('ty_' + idx).value;
    const disabledDates = ALL_SETTINGS.filter(setting => setting.type === currentType).map(setting => setting.date);

    let html =
        '<div class="p-hdr">' +
            '<button type="button" class="p-nav" onclick="navGlobalCal(-1)">' +
                '<svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>' +
            '</button>' +
            '<span>' + MONTHS[s.m] + ' ' + s.y + '</span>' +
            '<button type="button" class="p-nav" onclick="navGlobalCal(1)">' +
                '<svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>' +
            '</button>' +
        '</div>' +
        '<div style="padding:.75rem;">' +
            '<div style="display:grid;grid-template-columns:repeat(7,1fr);margin-bottom:.25rem;">';
    DSHORT.forEach(function(d) {
        html += '<div style="text-align:center;font-size:.6rem;font-weight:700;color:#94a3b8;padding:.2rem 0;">' + d + '</div>';
    });
   html += '</div><div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:1rem;" id="globalCalGrid"></div></div>';
    html += '<div style="padding: 0 1rem 1rem 1rem;">' +
                '<button type="button" onclick="terapkanTanggal()" style="width: 100%; background-color: #eab308; color: white; padding: 0.75rem; border-radius: 0.5rem; font-weight: bold; border: none; cursor: pointer; transition: background-color 0.2s;">' +
                    'Simpan Tanggal' +
                '</button>' +
            '</div>';

    p.innerHTML = html;

    const grid     = document.getElementById('globalCalGrid');
    const firstDay = new Date(s.y, s.m, 1).getDay();
    const days     = new Date(s.y, s.m + 1, 0).getDate();
    const prevDays = new Date(s.y, s.m, 0).getDate();

    let cells = [];
    for (let i = firstDay - 1; i >= 0; i--) cells.push({ d: prevDays - i, other: true });
    for (let d = 1; d <= days; d++) {
        const dt = new Date(s.y, s.m, d);
        const dateString = fmtDate(dt);
        const isDisabled = disabledDates.includes(dateString);
        cells.push({ d, other: false, isToday: sameDay(dt, now), isSel: sel === dateString, dt, isDisabled });
    }
    while (cells.length < 42) cells.push({ d: cells.length - days - firstDay + 2, other: true });

    cells.forEach(function(c) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = c.d;
        btn.className = 'cal-day' +
            (c.other ? ' other' : '') +
            (c.isToday ? ' today' : '') +
            (c.isSel ? ' sel' : '') +
            (c.isDisabled ? ' disabled' : '');
            
        if (!c.other && !c.isDisabled) {
            const ds = fmtDate(c.dt);
            btn.onclick = function() { 
                commitDate(activePickerIdx, ds);
                renderGlobalDate(); 
            };
        }
        grid.appendChild(btn);
    });
}

function navGlobalCal(dir) {
    const idx = activePickerIdx;
    if (idx === null) return;
    let m = calState[idx].m + dir, y = calState[idx].y;
    if (m < 0)  { m = 11; y--; }
    if (m > 11) { m = 0;  y++; }
    calState[idx] = { y, m };
    renderGlobalDate();
}

function terapkanTanggal() {
    closeGlobalDateModal();
}

function commitDate(idx, ds) {
    document.getElementById('dv_' + idx).value = ds;
    const dt = new Date(ds + 'T00:00:00');
    const lbl = dt.getDate() + ' ' + MONTHS[dt.getMonth()].substring(0,3) + ' ' + dt.getFullYear();
    const span = document.getElementById('dt_' + idx);
    if(span) {
        span.textContent = lbl;
        span.style.color = '#1e293b';
        span.style.fontWeight = '600';
    }
    calState[idx] = { y: dt.getFullYear(), m: dt.getMonth() };
}

/* ════════════════════════════════════════
   MODAL TIME PICKER LOGIC
════════════════════════════════════════ */
function openGlobalTimeModal(idx) {
    activePickerIdx = idx;
    document.getElementById('globalTimeModal').classList.remove('hidden');
    renderGlobalTime();
}

function closeGlobalTimeModal() {
    document.getElementById('globalTimeModal').classList.add('hidden');
    activePickerIdx = null;
}

function renderGlobalTime() {
    const idx = activePickerIdx;
    if (idx === null) return;

    const s = timeState[idx] || { h: 14, m: 0 };
    const hours   = Array.from({ length: 24 }, function(_, i) { return i; });
    const minutes = Array.from({ length: 12 }, function(_, i) { return i * 5; });
    const p = document.getElementById('globalTimeContent');

    let hItems = '', mItems = '';
    hours.forEach(function(h) {
        hItems += '<div class="t-item' + (h === s.h ? ' active' : '') + '" onclick="selGlobalH(' + h + ')">' +
            String(h).padStart(2,'0') + '</div>';
    });
    minutes.forEach(function(m) {
        mItems += '<div class="t-item' + (m === s.m ? ' active' : '') + '" onclick="selGlobalM(' + m + ')">' +
            String(m).padStart(2,'0') + '</div>';
    });

    p.innerHTML =
        '<div class="p-hdr"><span>Pilih Jam</span></div>' +
        /* Container utama dibikin lebih lebar gap dan padding-nya */
        '<div style="display:flex;gap:1.5rem;justify-content:center;padding:1.25rem 2rem;">' +
            
            /* Kolom Jam */
            '<div style="display:flex;flex-direction:column;align-items:center;flex:1;max-width:80px;">' +
                '<span style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:.6rem;">Jam</span>' +
                '<div class="time-col" id="global_hcol">' + hItems + '</div>' +
            '</div>' +
            
            /* Titik Dua */
            '<div style="font-size:1.5rem;font-weight:700;color:#cbd5e1;margin-top:1.5rem;flex-shrink:0;">:</div>' +
            
            /* Kolom Menit */
            '<div style="display:flex;flex-direction:column;align-items:center;flex:1;max-width:80px;">' +
                '<span style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:.6rem;">Menit</span>' +
                '<div class="time-col" id="global_mcol">' + mItems + '</div>' +
            '</div>' +

        '</div>';

    requestAnimationFrame(function() {
        var hc = document.getElementById('global_hcol');
        var mc = document.getElementById('global_mcol');
        var hs = hc ? hc.querySelector('.active') : null;
        var ms = mc ? mc.querySelector('.active') : null;
        if (hs && hc) hc.scrollTop = hs.offsetTop - hc.clientHeight / 2 + hs.clientHeight / 2;
        if (ms && mc) mc.scrollTop = ms.offsetTop - mc.clientHeight / 2 + ms.clientHeight / 2;
    });
}

function selGlobalH(h) {
    const idx = activePickerIdx;
    if (!timeState[idx]) timeState[idx] = { h: 14, m: 0 };
    timeState[idx].h = h;
    renderGlobalTime(); // re-render untuk animasinya
    commitTime(idx);
}

function selGlobalM(m) {
    const idx = activePickerIdx;
    if (!timeState[idx]) timeState[idx] = { h: 14, m: 0 };
    timeState[idx].m = m;
    renderGlobalTime();
    commitTime(idx);
}

function commitTime(idx) {
    var s   = timeState[idx] || { h: 14, m: 0 };
    var val = String(s.h).padStart(2,'0') + ':' + String(s.m).padStart(2,'0');
    var inp = document.getElementById('tv_' + idx);
    var lbl = document.getElementById('tt_' + idx);
    if (inp) inp.value = val;
    if (lbl) {
        lbl.textContent = val;
        lbl.style.color = '#1e293b';
        lbl.style.fontWeight = '600';
    }
}

/* ════════════════════════════════════════
   HELPERS
════════════════════════════════════════ */
function fmtDate(d) {
    return d.getFullYear() + '-' +
        String(d.getMonth()+1).padStart(2,'0') + '-' +
        String(d.getDate()).padStart(2,'0');
}
function sameDay(a, b) {
    return a.getFullYear()===b.getFullYear() &&
           a.getMonth()===b.getMonth() &&
           a.getDate()===b.getDate();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { 
        closeSettingModal(); 
        closeDetailModal(); 
        closeGlobalDateModal(); 
        closeGlobalTimeModal(); 
    }
});

/* ════════════════════════════════════════
   HAPUS DATA TERSIMPAN
════════════════════════════════════════ */
function confirmDel(id, dateLabel, timeLabel) {
    Swal.fire({
        title: 'Hapus Pengaturan?',
        html: '<span style="font-size:.88rem;color:#475569;">Jam <strong>' + timeLabel +
              '</strong> pada <strong>' + dateLabel + '</strong> akan dihapus.</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: { confirmButton: 'swal-delete-btn' },
        buttonsStyling: true,
    }).then(function(r) {
        if (r.isConfirmed) {
            var f = document.getElementById('delForm_' + id);
            if (f) f.submit();
        }
    });
}
</script>

@endsection