@extends('Visitors.layouts.app')

@section('title', __('visitor.hist_title'))

@push('head')
<style>
:root { --y:#eab308;--yd:#ca8a04;--yl:#facc15;--y50:#fefce8;--y100:#fef9c3;--ytext:#713f12; }

.hist-wrap { max-width:900px;margin:0 auto;padding:2rem 1.25rem 6rem; }
@media(min-width:768px){ .hist-wrap{padding:2.5rem 2rem 3rem;} }

/* ── Tabs ── */
.hist-tabs { display:flex;gap:.3rem;background:#f1f5f9;border-radius:1rem;
             padding:.3rem;margin-bottom:1.5rem; }
.hist-tab  { flex:1;padding:.5rem .625rem;border-radius:.75rem;border:none;cursor:pointer;
             font-size:.78rem;font-weight:700;transition:all .18s;background:transparent;
             color:#64748b;display:flex;align-items:center;justify-content:center;gap:.375rem;
             white-space:nowrap; }
.hist-tab.active { background:#fff;color:#0f172a;box-shadow:0 1px 4px rgba(0,0,0,.10); }
.hist-tab.active.success-tab { color:var(--ytext); }
.hist-tab.active.pending-tab { color:#b45309; }
.hist-tab.active.fail-tab    { color:#dc2626; }
.hist-tab-badge { font-size:.62rem;font-weight:800;padding:.1rem .4rem;
                  border-radius:9999px;min-width:17px;text-align:center;flex-shrink:0; }
.hist-tab.active.success-tab .hist-tab-badge { background:var(--y);color:var(--ytext); }
.hist-tab.active.pending-tab .hist-tab-badge { background:#fde68a;color:#92400e; }
.hist-tab.active.fail-tab    .hist-tab-badge { background:#fecaca;color:#b91c1c; }
.hist-tab:not(.active) .hist-tab-badge { background:#e2e8f0;color:#64748b; }
@media(max-width:480px){
    .hist-tab { font-size:.7rem; gap:.25rem; padding:.5rem .375rem; }
}

/* ── Tab panels ── */
.tab-panel { display:none; }
.tab-panel.show { display:block; }

/* ── Empty state ── */
.empty-state { text-align:center;padding:4rem 1rem; }
.empty-state-icon { width:72px;height:72px;border-radius:1.5rem;
                    display:flex;align-items:center;justify-content:center;
                    margin:0 auto 1.25rem; }
.empty-state-title { font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:.375rem; }
.empty-state-desc  { font-size:.82rem;color:#94a3b8;line-height:1.6;max-width:280px;margin:0 auto; }
</style>
@endpush

@section('content')
<div class="hist-wrap">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-[1.2rem] font-extrabold text-slate-900 leading-tight">{{ __('visitor.hist_title') }}</h1>
            <p class="text-[.78rem] text-slate-400 mt-0.5">{{ __('visitor.hist_subtitle') }}</p>
        </div>
        <a href="{{ route('index') }}#kamar"
           class="inline-flex items-center gap-1.5 text-[.8rem] font-semibold px-4 py-2
                  rounded-xl text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('visitor.hist_book_again') }}
        </a>
    </div>

    {{-- Session alerts --}}
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl flex items-center gap-3 text-[.82rem] font-semibold text-green-800"
             style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3.5 rounded-xl flex items-center gap-3 text-[.82rem] font-semibold text-amber-800"
             style="background:#fefce8;border:1px solid #fde68a;">
            <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- 3-Tab navigation --}}
    <div class="hist-tabs" role="tablist">

        {{-- Tab: Pending --}}
        <button type="button" id="tabBtnPending"
                class="hist-tab pending-tab {{ $tab === 'pending' ? 'active' : '' }}"
                onclick="switchTab('pending')" role="tab"
                aria-selected="{{ $tab === 'pending' ? 'true' : 'false' }}">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="hidden sm:inline">{{ __('visitor.hist_reservation') }}</span> {{ __('visitor.hist_tab_pending') }}
            <span class="hist-tab-badge">{{ $pendingBookings->total() }}</span>
        </button>

        {{-- Tab: Success --}}
        <button type="button" id="tabBtnSuccess"
                class="hist-tab success-tab {{ $tab === 'success' ? 'active' : '' }}"
                onclick="switchTab('success')" role="tab"
                aria-selected="{{ $tab === 'success' ? 'true' : 'false' }}">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="hidden sm:inline">{{ __('visitor.hist_reservation') }}</span> {{ __('visitor.hist_tab_success') }}
            <span class="hist-tab-badge">{{ $successBookings->total() }}</span>
        </button>

        {{-- Tab: Failed --}}
        <button type="button" id="tabBtnFailed"
                class="hist-tab fail-tab {{ $tab === 'failed' ? 'active' : '' }}"
                onclick="switchTab('failed')" role="tab"
                aria-selected="{{ $tab === 'failed' ? 'true' : 'false' }}">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="hidden sm:inline">{{ __('visitor.hist_reservation') }}</span> {{ __('visitor.hist_tab_failed') }}
            <span class="hist-tab-badge">{{ $failedBookings->total() }}</span>
        </button>
    </div>

    {{-- ── Tab Panel: PENDING ── --}}
    <div id="panelPending" class="tab-panel {{ $tab === 'pending' ? 'show' : '' }}" role="tabpanel">
        @if($pendingBookings->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon" style="background:#fef9c3;">
                    <svg class="w-9 h-9" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="empty-state-title">{{ __('visitor.hist_empty_pending_title') }}</p>
                <p class="empty-state-desc">{{ __('visitor.hist_empty_pending_desc') }}</p>
            </div>
        @else
            @include('booking::Visitor.history.partials.pending-list', ['bookings' => $pendingBookings])
        @endif
    </div>

    {{-- ── Tab Panel: SUCCESS ── --}}
    <div id="panelSuccess" class="tab-panel {{ $tab === 'success' ? 'show' : '' }}" role="tabpanel">
        @if($successBookings->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon" style="background:var(--y50);">
                    <svg class="w-9 h-9" style="color:var(--y);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="empty-state-title">{{ __('visitor.hist_empty_success_title') }}</p>
                <p class="empty-state-desc">{{ __('visitor.hist_empty_success_desc') }}</p>
                <a href="{{ route('index') }}#kamar"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl font-bold text-[.875rem]"
                   style="background:var(--y);color:var(--ytext);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('visitor.hist_book_now_cta') }}
                </a>
            </div>
        @else
            @include('booking::Visitor.history.partials.success-list', ['bookings' => $successBookings])
        @endif
    </div>

    {{-- ── Tab Panel: FAILED ── --}}
    <div id="panelFailed" class="tab-panel {{ $tab === 'failed' ? 'show' : '' }}" role="tabpanel">
        @if($failedBookings->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon" style="background:#f0fdf4;">
                    <svg class="w-9 h-9 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="empty-state-title">{{ __('visitor.hist_empty_fail_title') }}</p>
                <p class="empty-state-desc">{{ __('visitor.hist_empty_fail_desc') }}</p>
            </div>
        @else
            @include('booking::Visitor.history.partials.fail-list', ['bookings' => $failedBookings])
        @endif
    </div>

</div>{{-- /hist-wrap --}}

@push('scripts')
<script>
const TAB_PANELS = { pending:'panelPending', success:'panelSuccess', failed:'panelFailed' };
const TAB_BTNS   = { pending:'tabBtnPending', success:'tabBtnSuccess', failed:'tabBtnFailed' };

function switchTab(tab) {
    // Deactivate all
    Object.values(TAB_PANELS).forEach(id => document.getElementById(id)?.classList.remove('show'));
    Object.values(TAB_BTNS).forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('active'); el.setAttribute('aria-selected','false'); }
    });

    // Activate selected
    document.getElementById(TAB_PANELS[tab])?.classList.add('show');
    const btn = document.getElementById(TAB_BTNS[tab]);
    if (btn) { btn.classList.add('active'); btn.setAttribute('aria-selected','true'); }

    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    history.replaceState(null, '', url.toString());
}
</script>
@endpush
@endsection
