{{--
    Partial: room card
    Props: $room (Room model)
--}}
<div class="room-card bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm flex flex-col">

    {{-- Cover Photo --}}
    <div class="room-card__thumb relative">
        @if($room->coverPhoto)
            <img src="{{ asset($room->coverPhoto->path) }}"
                 alt="{{ $room->name }}"
                 class="w-full h-44 object-cover">
        @else
            <div class="w-full h-44 flex flex-col items-center justify-center gap-2"
                 style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
                <svg class="w-10 h-10" style="color:rgba(0,0,0,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="text-[0.72rem] font-semibold" style="color:rgba(0,0,0,0.35);">Belum ada foto</span>
            </div>
        @endif

        {{-- Status badge --}}
        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[0.7rem] font-bold
                     {{ $room->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $room->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
        </span>

        {{-- Photo count badge --}}
        @if($room->photos->count() > 0)
            <span class="absolute top-3 right-3 flex items-center gap-1 px-2 py-1
                         rounded-lg bg-black/50 text-white text-[0.7rem] font-semibold">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $room->photos->count() }}
            </span>
        @endif
    </div>

    {{-- Card body --}}
    <div class="p-5 flex flex-col flex-1">
        <div class="flex-1">
            {{-- Nama & kapasitas --}}
            <h3 class="text-[0.95rem] font-bold text-slate-900 leading-tight mb-1 truncate">
                {{ $room->name }}
            </h3>
            <div class="flex items-center gap-1.5 text-[0.78rem] text-slate-500 mb-3">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $room->capacity }} orang</span>
                @if($room->facilities->count() > 0)
                    <span class="text-slate-300">|</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>{{ $room->facilities->count() }} fasilitas</span>
                @endif
            </div>

            {{-- Harga --}}
            @if($room->has_discount)
                {{-- Harga asli dicoret --}}
                <p class="text-[0.8rem] font-medium text-slate-400 mb-0.5"
                   style="text-decoration:line-through;">
                    {{ $room->formatted_price }}
                </p>
                {{-- Harga setelah diskon (asumsi syarat malam terpenuhi) --}}
                <p class="text-[1.1rem] font-extrabold text-green-600 leading-tight">
                    Rp {{ number_format($room->getPriceAfterDiscount(PHP_INT_MAX), 0, ',', '.') }}
                </p>
                {{-- Badge hemat --}}
                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                    <span class="text-[0.72rem] text-slate-400">/malam</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full
                                 text-[0.62rem] font-bold bg-green-100 text-green-700"
                          style="border:1px solid #bbf7d0;">
                        Hemat {{ $room->formatted_discount }}
                    </span>
                </div>
                @if($room->discount_min_nights > 0)
                    <p class="text-[0.65rem] text-slate-400 mt-0.5">
                        Min. {{ $room->discount_min_nights }} malam
                    </p>
                @endif
            @else
                <p class="text-[1.1rem] font-extrabold leading-tight" style="color:#eab308;">
                    {{ $room->formatted_price }}
                </p>
                <p class="text-[0.72rem] text-slate-400 mt-0.5">/malam</p>
            @endif
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-100 mt-4 pt-4 flex items-center gap-2">
            {{-- Edit --}}
            <a href="{{ route('admin.rooms.edit', $room->uuid) }}"
               class="flex-1 flex items-center justify-center gap-1.5
                      px-3 py-2 rounded-xl border border-slate-200 bg-slate-50
                      text-[0.8rem] font-semibold text-slate-600
                      hover:bg-white hover:border-slate-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.rooms.destroy', $room->uuid) }}"
                  class="flex-1" id="deleteForm_{{ $room->uuid }}">
                @csrf
                @method('DELETE')
                <button type="button"
                    onclick="confirmDelete(document.getElementById('deleteForm_{{ $room->uuid }}'), '{{ addslashes($room->name) }}')"
                    class="w-full flex items-center justify-center gap-1.5
                           px-3 py-2 rounded-xl border border-red-200 bg-red-50
                           text-[0.8rem] font-semibold text-red-600
                           hover:bg-red-100 hover:border-red-300 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
