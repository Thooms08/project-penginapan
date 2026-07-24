@extends('Visitors.layouts.app')

@section('title', 'Dashboard Visitor')

@section('content')

    {{-- ── Welcome Banner ── --}}
    <div class="rounded-2xl p-7 mb-8 flex items-center justify-between flex-wrap gap-4"
         style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
        <div>
            <p class="text-sm mb-1" style="color:rgba(0,0,0,0.45);">Selamat datang 👋</p>
            <h1 class="text-2xl font-extrabold tracking-tight" style="color:#713f12;">
                {{ Auth::user()->name }}
            </h1>
            <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[0.75rem] font-semibold"
                  style="background:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.5);color:#713f12;">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="avatar"
                class="w-[60px] h-[60px] rounded-full object-cover"
                style="border:3px solid rgba(255,255,255,0.5);">
        @else
            <div class="w-[60px] h-[60px] rounded-full flex items-center justify-center text-[1.3rem] font-extrabold"
                 style="background:rgba(255,255,255,0.3);border:3px solid rgba(255,255,255,0.5);color:#713f12;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        @endif
    </div>

    {{-- ── Section title ── --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Kamar Tersedia</h2>
            <p class="text-[0.82rem] text-slate-500 mt-0.5">Temukan kamar yang sesuai kebutuhan Anda.</p>
        </div>
        <span class="text-[0.78rem] font-semibold px-3 py-1 rounded-full"
              style="background:#fefce8;color:#713f12;border:1px solid #fef9c3;">
            3 kamar
        </span>
    </div>

    {{-- ── Room cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach([
            ['name' => 'Kamar Deluxe',   'price' => '350.000', 'bed' => 'King Bed',   'available' => true,  'tag' => 'Populer'],
            ['name' => 'Kamar Superior', 'price' => '250.000', 'bed' => 'Queen Bed',  'available' => true,  'tag' => ''],
            ['name' => 'Kamar Standar',  'price' => '150.000', 'bed' => 'Single Bed', 'available' => false, 'tag' => ''],
        ] as $room)

        <div class="room-card bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">

            {{-- Gambar / placeholder --}}
            <div class="relative h-40 flex items-center justify-center"
                 style="background:linear-gradient(135deg,#eab308 0%,#facc15 100%);">
                <svg class="w-12 h-12" style="color:rgba(0,0,0,0.18);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                @if($room['tag'])
                    <div class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full
                                text-[0.7rem] font-bold"
                         style="background:rgba(255,255,255,0.28);border:1px solid rgba(255,255,255,0.45);color:#713f12;">
                        ⭐ {{ $room['tag'] }}
                    </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="p-5">
                <div class="flex items-start justify-between mb-1.5">
                    <h3 class="text-[0.95rem] font-bold text-slate-900">{{ $room['name'] }}</h3>
                    <span class="shrink-0 ml-2 px-2 py-0.5 rounded-full text-[0.7rem] font-bold
                                 {{ $room['available'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $room['available'] ? 'Tersedia' : 'Penuh' }}
                    </span>
                </div>

                <p class="text-[0.8rem] text-slate-400 mb-4">{{ $room['bed'] }}</p>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[1.15rem] font-extrabold" style="color:#eab308;">
                            Rp {{ $room['price'] }}
                        </span>
                        <span class="text-[0.75rem] text-slate-400">/malam</span>
                    </div>
                    @if($room['available'])
                        <button class="btn-pesan px-4 py-2 rounded-lg text-[0.82rem] font-semibold border-none cursor-pointer transition-all">
                            Pesan
                        </button>
                    @else
                        <button disabled
                            class="px-4 py-2 rounded-lg text-[0.82rem] font-semibold
                                   bg-slate-100 text-slate-400 cursor-not-allowed border-none">
                            Penuh
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <style>
        /* Card hover — tidak bisa di Tailwind tanpa JIT/build */
        .room-card { transition: box-shadow 0.2s, transform 0.2s; }
        .room-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }

        /* Tombol pesan */
        .btn-pesan { background: #eab308; color: #713f12; }
        .btn-pesan:hover { background: #ca8a04; color: #fff; }
    </style>

@endsection
