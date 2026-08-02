@extends('Admin.layouts.app')

@section('title', 'Daftar Booking')
@section('page_title', 'Daftar Booking')
@section('page_subtitle', 'Kelola dan konfirmasi reservasi tamu')

@section('content')
<style>
.btn-yellow {
    background:#eab308;color:#713f12;border:none;cursor:pointer;font-weight:700;
    border-radius:.75rem;padding:.625rem 1.5rem;font-size:.875rem;
    transition:background .15s,color .15s;display:inline-flex;align-items:center;gap:.5rem;
}
.btn-yellow:hover { background:#ca8a04;color:#fff; }
.btn-green-sm {
    display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;
    border-radius:.5rem;font-size:.75rem;font-weight:700;border:1px solid #bbf7d0;
    background:#f0fdf4;color:#15803d;cursor:pointer;white-space:nowrap;transition:background .12s;
}
.btn-green-sm:hover { background:#dcfce7; }
.btn-slate-sm {
    display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;
    border-radius:.5rem;font-size:.75rem;font-weight:700;border:1px solid #e2e8f0;
    background:#f8fafc;color:#475569;cursor:pointer;white-space:nowrap;transition:background .12s;
}
.btn-slate-sm:hover { background:#f1f5f9; }
/* Tabs */
.adm-tabs { display:flex;gap:.375rem;background:#f1f5f9;border-radius:.875rem;padding:.3rem;margin-bottom:1.5rem; }
.adm-tab  { flex:1;padding:.55rem 1rem;border-radius:.625rem;border:none;cursor:pointer;
            font-size:.82rem;font-weight:700;background:transparent;color:#64748b;
            display:flex;align-items:center;justify-content:center;gap:.5rem;transition:all .18s;white-space:nowrap; }
.adm-tab.active { background:#fff;color:#0f172a;box-shadow:0 1px 4px rgba(0,0,0,.10); }
.adm-tab.active.tab-pending { color:#b45309; }
.adm-tab.active.tab-confirm { color:#15803d; }
.adm-tab-badge { font-size:.62rem;font-weight:800;padding:.1rem .4rem;border-radius:9999px;min-width:17px;text-align:center; }
.adm-tab.active.tab-pending .adm-tab-badge { background:#fde68a;color:#92400e; }
.adm-tab.active.tab-confirm .adm-tab-badge { background:#bbf7d0;color:#166534; }
.adm-tab:not(.active) .adm-tab-badge { background:#e2e8f0;color:#64748b; }
.tab-panel { display:none; }
.tab-panel.show { display:block; }
/* Modal */
.modal-overlay { display:none;position:fixed;inset:0;z-index:80;background:rgba(0,0,0,.5);
                 backdrop-filter:blur(3px);align-items:center;justify-content:center;padding:1rem; }
.modal-overlay.show { display:flex; }
.modal-box { background:#fff;border-radius:1.5rem;width:100%;max-width:500px;
             box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden;
             animation:mIn .2s cubic-bezier(.4,0,.2,1); }
.modal-box.wide { max-width:760px; }
.modal-body { max-height:80vh;overflow-y:auto; }
@keyframes mIn { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
.modal-hdr { display:flex;align-items:center;justify-content:space-between;
             padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9; }
.modal-body { padding:1.25rem 1.5rem 1.5rem; }
.modal-close { width:30px;height:30px;border-radius:8px;border:none;background:#f1f5f9;
               cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;transition:background .12s; }
.modal-close:hover { background:#e2e8f0;color:#0f172a; }
/* Detail rows */
.d-row { display:flex;justify-content:space-between;align-items:flex-start;
         padding:.45rem 0;border-bottom:1px solid #f1f5f9;font-size:.82rem; }
.d-row:last-child { border-bottom:none; }
.d-row .lbl { color:#64748b;flex-shrink:0;width:150px; }
.d-row .val { font-weight:600;color:#0f172a;text-align:right;word-break:break-word; }
/* Detail grid in wide modal */
.detail-grid { display:grid;grid-template-columns:1fr 1fr;gap:0 2rem; }
@media(max-width:600px){ .detail-grid { grid-template-columns:1fr; } }
/* Row slide-out animation */
@keyframes slideOut {
    0%   { opacity:1; transform:translateX(0);    max-height:200px; }
    60%  { opacity:0; transform:translateX(60px);  max-height:200px; }
    100% { opacity:0; transform:translateX(60px);  max-height:0; padding:0; margin:0; border:0; }
}
.row-removing {
    overflow:hidden;
    animation:slideOut .45s cubic-bezier(.4,0,.8,1) forwards;
    pointer-events:none;
}
.cash-input { width:100%;padding:.75rem 1rem;border:2px solid #e2e8f0;border-radius:.875rem;
              font-size:1.1rem;font-weight:700;color:#0f172a;outline:none;
              transition:border .15s,box-shadow .15s;font-family:inherit; }
.cash-input:focus { border-color:#eab308;box-shadow:0 0 0 3px rgba(234,179,8,.15); }
</style>

{{-- Flash toasts --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded',function(){
    Swal.fire({icon:'success',title:'Berhasil',text:@json(session('success')),
        timer:3000,timerProgressBar:true,showConfirmButton:false,
        toast:true,position:'top-end'});
});
</script>
@endif

{{-- Page header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fef9c3;">
            <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Booking</h2>
            <p class="text-[0.78rem] text-slate-500">Konfirmasi check-in tamu dan kelola reservasi</p>
        </div>
    </div>
</div>

{{-- 2-Tab navigation --}}
<div class="adm-tabs" role="tablist">
    <button type="button" id="tabBtnPending"
            class="adm-tab tab-pending {{ $tab === 'pending' ? 'active' : '' }}"
            onclick="switchTab('pending')" role="tab">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Menunggu Pelunasan
        <span class="adm-tab-badge">{{ $pendingBookings->total() }}</span>
    </button>
    <button type="button" id="tabBtnConfirm"
            class="adm-tab tab-confirm {{ $tab === 'confirmed' ? 'active' : '' }}"
            onclick="switchTab('confirmed')" role="tab">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Bayar Lunas (Midtrans)
        <span class="adm-tab-badge">{{ $confirmedBookings->total() }}</span>
    </button>
</div>

{{-- Tab Panel: Pending (DP) --}}
<div id="panelPending" class="tab-panel {{ $tab === 'pending' ? 'show' : '' }}">
    @include('booking::Admin.partials.booking-list', [
        'bookings' => $pendingBookings,
        'type'     => 'pending',
        'tab'      => 'pending',
    ])
</div>

{{-- Tab Panel: Confirmed (Full) --}}
<div id="panelConfirm" class="tab-panel {{ $tab === 'confirmed' ? 'show' : '' }}">
    @include('booking::Admin.partials.booking-list', [
        'bookings' => $confirmedBookings,
        'type'     => 'confirmed',
        'tab'      => 'confirmed',
    ])
</div>

{{-- ═══════════════════ MODAL: KONFIRMASI DP ═══════════════════ --}}
<div class="modal-overlay" id="modalDP">
    <div class="modal-box">
        <div class="modal-hdr">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fef9c3;">
                    <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900 text-[.92rem]">Konfirmasi Pelunasan DP</p>
                    <p class="text-[.7rem] text-slate-400" id="dpModalCode">—</p>
                </div>
            </div>
            <button onclick="closeModal('modalDP')" class="modal-close">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="rounded-xl p-4 mb-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="d-row"><span class="lbl">Tamu</span><span class="val" id="dpModalGuest">—</span></div>
                <div class="d-row"><span class="lbl">Kamar</span><span class="val" id="dpModalRoom">—</span></div>
                <div class="d-row"><span class="lbl">Check-In</span><span class="val" id="dpModalCheckIn">—</span></div>
                <div class="d-row"><span class="lbl">Total Biaya</span><span class="val" id="dpModalTotal">—</span></div>
                <div class="d-row"><span class="lbl">DP Sudah Dibayar</span>
                    <span class="val text-green-700" id="dpModalPaid">—</span></div>
                <div class="d-row" style="border-top:2px solid #e2e8f0;margin-top:.25rem;padding-top:.625rem;">
                    <span class="lbl font-bold text-slate-900">Sisa Pelunasan</span>
                    <span class="val text-[1rem]" style="color:#b45309;" id="dpModalRemaining">—</span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                    Uang Diterima (Cash)
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" id="cashInput" class="cash-input pl-10"
                           placeholder="Rp 0" min="0" oninput="calcChange()">
                </div>
            </div>
            {{-- Real-time feedback --}}
            <div id="changeInfo" class="hidden rounded-xl p-3.5 mb-4">
                <div class="flex justify-between text-[.82rem] mb-1.5">
                    <span class="text-slate-500">Uang Diterima</span>
                    <span class="font-bold text-slate-700" id="infoReceived">Rp 0</span>
                </div>
                <div class="flex justify-between text-[.82rem] mb-1.5">
                    <span class="text-slate-500">Sisa Tagihan</span>
                    <span class="font-bold" id="infoRemaining">Rp 0</span>
                </div>
                <div class="flex justify-between text-[.88rem] pt-2" style="border-top:1px solid rgba(0,0,0,.08);">
                    <span class="font-bold" id="changeLabel">Kembalian</span>
                    <span class="font-extrabold text-[1rem]" id="changeValue">Rp 0</span>
                </div>
            </div>
            <button type="button" id="dpConfirmBtn" class="btn-yellow w-full justify-center py-3"
                    onclick="submitDP()" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                BAYARKAN &amp; CHECK-IN TAMU
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════ MODAL: DETAIL ═══════════════════ --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box wide">
        <div class="modal-hdr">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#eff6ff;">
                    <svg class="w-4 h-4" style="color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900 text-[.92rem]">Detail Booking</p>
                    <p class="text-[.7rem] text-slate-400" id="detailCode">—</p>
                </div>
            </div>
            <button onclick="closeModal('modalDetail')" class="modal-close">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="detailBody">
            <div class="text-center py-8 text-slate-400">
                <svg class="w-8 h-8 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Memuat...
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ── Remove row with slide-out animation ── */
function removeRow(bookingId) {
    // Desktop table row
    const tr = document.getElementById('row-' + bookingId);
    // Mobile card
    const card = document.getElementById('card-' + bookingId);

    [tr, card].forEach(el => {
        if (!el) return;
        el.classList.add('row-removing');
        el.addEventListener('animationend', () => {
            el.remove();
            // Update badge counter
            updateTabBadge();
        }, { once: true });
    });
}

function updateTabBadge() {
    // Count remaining rows in each panel
    const pendingRows  = document.querySelectorAll('#panelPending  #row-[id], #panelPending  [id^="row-"], #panelPending  [id^="card-"]').length;
    const confirmedRows= document.querySelectorAll('#panelConfirm  [id^="row-"], #panelConfirm [id^="card-"]').length;

    // Re-count via visible rows in table tbody and mobile cards
    const pRows = document.querySelectorAll('#panelPending  tr[id^="row-"]').length
                + document.querySelectorAll('#panelPending  div[id^="card-"]').length;
    const cRows = document.querySelectorAll('#panelConfirm  tr[id^="row-"]').length
                + document.querySelectorAll('#panelConfirm  div[id^="card-"]').length;

    // Each entry has both a tr and a card, so divide by 2
    const pCount = Math.round(pRows / 2);
    const cCount = Math.round(cRows / 2);

    const pBadge = document.querySelector('#tabBtnPending .adm-tab-badge');
    const cBadge = document.querySelector('#tabBtnConfirm .adm-tab-badge');
    if (pBadge) pBadge.textContent = pCount;
    if (cBadge) cBadge.textContent = cCount;
}
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('show'));
    document.querySelectorAll('.adm-tab').forEach(b => b.classList.remove('active'));
    const panels = { pending:'panelPending', confirmed:'panelConfirm' };
    const btns   = { pending:'tabBtnPending', confirmed:'tabBtnConfirm' };
    document.getElementById(panels[tab])?.classList.add('show');
    document.getElementById(btns[tab])?.classList.add('active');
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    history.replaceState(null,'',url.toString());
}

/* ── Modal helpers ── */
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}
function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e){ if(e.target===this) closeModal(this.id); });
});

/* ── DP modal vars ── */
let dpBookingId   = null;
let dpRemaining   = 0;
let dpConfirmUrl  = '';

function openDPModal(id, code, guest, room, checkIn, total, paid, remainingFmt, remainingNum, confirmUrl) {
    dpBookingId  = id;
    dpRemaining  = remainingNum;
    dpConfirmUrl = confirmUrl;

    document.getElementById('dpModalCode').textContent      = code;
    document.getElementById('dpModalGuest').textContent     = guest;
    document.getElementById('dpModalRoom').textContent      = room;
    document.getElementById('dpModalCheckIn').textContent   = checkIn;
    document.getElementById('dpModalTotal').textContent     = total;
    document.getElementById('dpModalPaid').textContent      = paid;
    document.getElementById('dpModalRemaining').textContent = remainingFmt;

    document.getElementById('cashInput').value = '';
    document.getElementById('changeInfo').classList.add('hidden');
    document.getElementById('dpConfirmBtn').disabled = true;

    openModal('modalDP');
    setTimeout(() => document.getElementById('cashInput').focus(), 200);
}

function fmt(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

function calcChange() {
    const raw       = parseFloat(document.getElementById('cashInput').value) || 0;
    const info      = document.getElementById('changeInfo');
    const btnDP     = document.getElementById('dpConfirmBtn');
    const received  = document.getElementById('infoReceived');
    const remaining = document.getElementById('infoRemaining');
    const label     = document.getElementById('changeLabel');
    const value     = document.getElementById('changeValue');

    if (raw <= 0) { info.classList.add('hidden'); btnDP.disabled=true; return; }

    info.classList.remove('hidden');
    received.textContent  = fmt(raw);
    remaining.textContent = fmt(dpRemaining);

    const diff = raw - dpRemaining;
    if (diff < 0) {
        info.style.background = '#fef2f2'; info.style.border = '1px solid #fecaca';
        remaining.style.color = '#dc2626';
        label.textContent = 'Kurang';
        value.textContent = fmt(Math.abs(diff));
        value.style.color = '#dc2626';
        btnDP.disabled = true;
    } else {
        info.style.background = '#f0fdf4'; info.style.border = '1px solid #bbf7d0';
        remaining.style.color = '#0f172a';
        label.textContent = 'Kembalian';
        value.textContent = fmt(diff);
        value.style.color = '#15803d';
        btnDP.disabled = false;
    }
}

function submitDP() {
    const cash    = parseFloat(document.getElementById('cashInput').value) || 0;
    const btn     = document.getElementById('dpConfirmBtn');
    const origTxt = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Memproses...';

    fetch(dpConfirmUrl, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ cash_received: cash })
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = origTxt;
        closeModal('modalDP');
        if (data.success) {
            // Animasi hilangkan row terlebih dahulu
            removeRow(dpBookingId);

            const changeHtml = data.change > 0
                ? `<br><span style="color:#15803d;font-weight:600;font-size:.85rem;">Kembalian: ${data.change_fmt}</span>`
                : '';

            Swal.fire({
                icon:'success',
                title:'Check-In Berhasil!',
                html:`<div style="font-size:.9rem;line-height:1.8;">
                    Tamu <strong>${data.guest_name}</strong> berhasil check-in.${changeHtml}
                </div>`,
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        } else {
            Swal.fire({icon:'error', title:'Gagal', text: data.message});
            btn.disabled = false;
        }
    })
    .catch(() => {
        btn.innerHTML = origTxt; btn.disabled = false;
        Swal.fire({icon:'error', title:'Error', text:'Koneksi bermasalah.'});
    });
}

/* ── Full-pay confirm ── */
function confirmFull(id, code, guest, confirmUrl) {
    Swal.fire({
        icon:'question', title:'Konfirmasi Check-In',
        html:`Tamu <strong>${guest}</strong> (${code}) akan di-check-in sekarang. Lanjutkan?`,
        showCancelButton:true, confirmButtonText:'Ya, Check-In',
        cancelButtonText:'Batal', reverseButtons:true,
        customClass:{confirmButton:'btn-yellow'}
    }).then(res => {
        if (!res.isConfirmed) return;
        fetch(confirmUrl, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                removeRow(id);
                Swal.fire({
                    icon:'success', title:'Check-In Berhasil!',
                    text:`Tamu ${data.guest_name} berhasil check-in.`,
                    timer:3000, timerProgressBar:true,
                    showConfirmButton:false,
                    toast:true, position:'top-end',
                });
            } else {
                Swal.fire({icon:'error',title:'Gagal',text:data.message});
            }
        })
        .catch(() => Swal.fire({icon:'error',title:'Error',text:'Koneksi bermasalah.'}));
    });
}

/* ── Detail modal ── */
function openDetail(id, detailUrl) {
    document.getElementById('detailCode').textContent = '…';
    document.getElementById('detailBody').innerHTML =
        '<div class="text-center py-8 text-slate-400"><svg class="w-8 h-8 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Memuat...</div>';
    openModal('modalDetail');

    fetch(detailUrl, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} })
    .then(r => r.json())
    .then(d => {
        document.getElementById('detailCode').textContent = d.booking_code;
        document.getElementById('detailBody').innerHTML = `
        <div class="detail-grid">
            ${row('Kode Booking','<span class="font-mono text-[.78rem] bg-slate-100 px-2 py-0.5 rounded">'+d.booking_code+'</span>')}
            ${row('Waktu Booking', d.created_at)}
            ${row('Nama Tamu', d.guest_name)}
            ${row('Email Tamu', d.guest_email)}
            ${row('Kamar', d.room_name)}
            ${row('Durasi', d.nights+' malam')}
            ${row('Check-In', d.check_in)}
            ${row('Check-Out', d.check_out)}
            ${row('Metode Bayar', d.payment_type_label)}
            ${row('Status Bayar', badge(d.payment_status, d.payment_status_label))}
            ${row('Status Booking', badge(d.booking_status, d.booking_status_label))}
            ${row('Total Biaya', '<span style="color:#b45309;font-weight:800;">'+d.total_amount+'</span>')}
            ${row('Sudah Dibayar', '<span class="text-green-700 font-bold">'+d.amount_paid+'</span>')}
            ${row('Sisa Tagihan', d.amount_remaining)}
            ${row('Payment Gateway', d.midtrans_payment_type)}
            ${row('Catatan Tamu', '<span style="white-space:pre-wrap;text-align:left;display:block;max-width:200px;margin-left:auto;">'+d.guest_note+'</span>')}
        </div>`;
    })
    .catch(() => {
        document.getElementById('detailBody').innerHTML =
            '<p class="text-center text-red-500 py-6">Gagal memuat detail.</p>';
    });
}
function row(label, val) {
    return `<div class="d-row"><span class="lbl">${label}</span><span class="val">${val}</span></div>`;
}
function badge(status, label) {
    const colors = {
        paid:'background:#f0fdf4;color:#15803d;', pending:'background:#fef9c3;color:#92400e;',
        failed:'background:#fef2f2;color:#dc2626;', expired:'background:#f8fafc;color:#64748b;',
        cancelled:'background:#f8fafc;color:#64748b;', confirmed:'background:#f0fdf4;color:#15803d;',
        checked_in:'background:#eff6ff;color:#1d4ed8;', checked_out:'background:#f0fdf4;color:#166534;',
        waiting_payment:'background:#fef9c3;color:#92400e;',
    };
    const s = colors[status] || 'background:#f1f5f9;color:#475569;';
    return `<span style="display:inline-block;padding:.15rem .6rem;border-radius:999px;font-size:.72rem;font-weight:700;${s}">${label}</span>`;
}
</script>
@endpush
