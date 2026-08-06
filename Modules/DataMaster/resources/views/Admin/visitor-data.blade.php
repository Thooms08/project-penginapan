@extends('Admin.layouts.app')

@section('title', 'Data Visitor')
@section('page_title', 'Data Visitor')
@section('page_subtitle', 'Analitik dan perilaku check-in tamu penginapan')

@section('content')
<style>
.btn-yellow {
    background:#eab308;color:#713f12;border:none;cursor:pointer;font-weight:700;
    border-radius:.75rem;padding:.625rem 1.25rem;font-size:.875rem;
    transition:background .15s,color .15s;display:inline-flex;align-items:center;gap:.5rem;
}
.btn-yellow:hover { background:#ca8a04;color:#fff; }
.stat-card {
    background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;
    padding:1.125rem 1.25rem;display:flex;align-items:center;gap:1rem;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.stat-icon {
    width:44px;height:44px;border-radius:13px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.filter-input {
    padding:.5rem .875rem;border:1.5px solid #e2e8f0;border-radius:.75rem;
    font-size:.82rem;color:#1e293b;outline:none;background:#f8fafc;
    transition:border .15s,box-shadow .15s;font-family:inherit;
}
.filter-input:focus { border-color:#eab308;box-shadow:0 0 0 3px rgba(234,179,8,.15); }
</style>

{{-- ── Page Header ── --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fef9c3;">
            <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                       M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                       m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Data Visitor</h2>
            <p class="text-[.78rem] text-slate-500">
                Menampilkan data periode
                <span class="font-semibold text-slate-700">{{ $fromLabel }}</span>
                — <span class="font-semibold text-slate-700">{{ $toLabel }}</span>
            </p>
        </div>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<form method="GET" action="{{ route('admin.visitor-data.index') }}"
      class="flex flex-wrap items-end gap-3 mb-6 p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div>
        <label class="block text-[.72rem] font-bold text-slate-500 uppercase tracking-wide mb-1">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $from }}" class="filter-input">
    </div>
    <div>
        <label class="block text-[.72rem] font-bold text-slate-500 uppercase tracking-wide mb-1">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $to }}" class="filter-input">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn-yellow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>
        <a href="{{ route('admin.visitor-data.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-[.82rem] font-semibold
                  border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reset
        </a>
    </div>
    {{-- Quick range shortcuts --}}
    <div class="flex flex-wrap gap-2 ml-auto">
        @php
            $defaultFrom = now()->subDays(29)->format('Y-m-d');
            $defaultTo   = now()->addDays(30)->format('Y-m-d');
            $shortcuts = [
                ['label'=>'Bulan Ini', 'from'=>now()->startOfMonth()->format('Y-m-d'), 'to'=>now()->endOfMonth()->format('Y-m-d')],
                ['label'=>'Default (60hr)', 'from'=>$defaultFrom, 'to'=>$defaultTo],
                ['label'=>'90 Hari', 'from'=>now()->subDays(29)->format('Y-m-d'), 'to'=>now()->addDays(60)->format('Y-m-d')],
            ];
        @endphp
        @foreach($shortcuts as $sc)
            @php
                $scFrom = $sc['from'];
                $scTo   = $sc['to'];
                $active = $from === $scFrom && $to === $scTo;
            @endphp
            <a href="{{ route('admin.visitor-data.index', ['from'=>$scFrom,'to'=>$scTo]) }}"
               class="px-3 py-1.5 rounded-lg text-[.75rem] font-bold border transition-colors
                      {{ $active ? 'border-yellow-400 bg-yellow-50 text-amber-700' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50' }}">
                {{ $sc['label'] }}
            </a>
        @endforeach
    </div>
</form>

{{-- ── Summary Stats ── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;">
            <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <p class="text-[.72rem] font-semibold text-slate-400 uppercase tracking-wide">Total Check-In</p>
            <p class="text-[1.5rem] font-extrabold text-slate-900 leading-tight">{{ $totalCheckins }}</p>
            <p class="text-[.72rem] text-slate-400">dalam periode ini</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-[.72rem] font-semibold text-slate-400 uppercase tracking-wide">Visitor Unik</p>
            <p class="text-[1.5rem] font-extrabold text-slate-900 leading-tight">{{ $uniqueVisitors }}</p>
            <p class="text-[.72rem] text-slate-400">tamu berbeda</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-[.72rem] font-semibold text-slate-400 uppercase tracking-wide">Rata-rata Durasi</p>
            <p class="text-[1.5rem] font-extrabold text-slate-900 leading-tight">{{ $avgNights > 0 ? $avgNights : '—' }}</p>
            <p class="text-[.72rem] text-slate-400">malam per kunjungan</p>
        </div>
    </div>
</div>

{{-- ── Chart ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-[.92rem] font-bold text-slate-900">Grafik Check-In Harian</h3>
            <p class="text-[.75rem] text-slate-400 mt-0.5">Jumlah check-in per hari dalam rentang waktu yang dipilih</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full inline-block" style="background:#eab308;"></span>
            <span class="text-[.75rem] font-semibold text-slate-500">Check-In</span>
        </div>
    </div>
    <div style="position:relative;height:220px;">
        <canvas id="checkinChart"></canvas>
    </div>
</div>

{{-- ── Visitor Table ── --}}
@include('datamaster::Admin.partials.visitor-data-table', ['visitors' => $visitors, 'from' => $from, 'to' => $to])

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($chartLabels);
    const data   = @json($chartData);
    const maxVal = Math.max(...data, 1);

    const ctx = document.getElementById('checkinChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Check-In',
                data,
                backgroundColor: data.map(v =>
                    v === Math.max(...data) ? 'rgba(234,179,8,0.9)' : 'rgba(234,179,8,0.45)'
                ),
                borderColor: 'rgba(202,138,4,0.8)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(202,138,4,0.85)',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f8fafc',
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} check-in`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, family: 'Inter, sans-serif' },
                        color: '#94a3b8',
                        maxRotation: 45,
                        autoSkip: true,
                        maxTicksLimit: 20,
                    },
                },
                y: {
                    beginAtZero: true,
                    max: maxVal + Math.ceil(maxVal * 0.2) || 5,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11, family: 'Inter, sans-serif' },
                        color: '#94a3b8',
                    },
                    grid: { color: 'rgba(0,0,0,.05)' },
                },
            },
        },
    });
})();
</script>
@endpush
