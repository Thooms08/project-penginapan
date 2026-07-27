{{--
    Partial: check-table.blade.php
    Berisi tabel data tamu yang check-in dan check-out.
    Akan diisi dengan data booking pada pengembangan selanjutnya.
--}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-8">
    <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-[0.9rem] font-bold text-slate-900">Data Tamu Check-In &amp; Check-Out</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar tamu yang melakukan check-in dan check-out hari ini</p>
        </div>
        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
            Hari ini — {{ \Carbon\Carbon::today()->locale('id')->isoFormat('D MMMM YYYY') }}
        </span>
    </div>

    {{-- Placeholder — akan diganti dengan data tamu nyata --}}
    <div class="flex flex-col items-center justify-center py-16 px-8 text-center">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
             style="background:#fefce8;">
            <svg class="w-7 h-7" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                       M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                       m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-[0.9rem] font-bold text-slate-900 mb-1.5">Belum ada data tamu</h3>
        <p class="text-[0.82rem] text-slate-400 max-w-xs">
            Data tamu yang check-in dan check-out akan ditampilkan di sini setelah fitur booking aktif.
        </p>
    </div>
</div>
