@extends('Admin.layouts.app')

@section('title', 'Manajemen Kamar')
@section('page_title', 'Kamar')
@section('page_subtitle', 'Kelola data kamar penginapan')

@section('content')

<style>
    .room-card { transition: box-shadow 0.2s, transform 0.2s; }
    .room-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
    .room-card__thumb { overflow: hidden; }
    .room-card__thumb img { transition: transform 0.3s ease; }
    .room-card:hover .room-card__thumb img { transform: scale(1.04); }

    .btn-primary {
        background: #eab308; color: #713f12;
        border: none; cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }
    .btn-primary:hover { background: #ca8a04; color: #fff; }

    /* SweetAlert custom buttons */
    .swal-delete-btn { background: #ef4444 !important; color: #fff !important; font-weight: 600 !important; }
    .swal-delete-btn:hover { background: #dc2626 !important; }
    .swal-toast-popup { font-family: 'Inter', sans-serif !important; font-size: 0.875rem !important; }
</style>

{{-- ── Flash SweetAlert (success/error dari controller) ── --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'swal-toast-popup' }
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonText: 'Tutup',
            customClass: { confirmButton: 'swal-confirm-btn' },
        });
    });
</script>
@endif

{{-- ── Header bar ── --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Daftar Kamar</h2>
        <p class="text-[0.82rem] text-slate-500 mt-0.5">
            Total {{ $rooms->total() }} kamar terdaftar
        </p>
    </div>
    <a href="{{ route('admin.rooms.create') }}"
       class="btn-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-[0.875rem] font-semibold no-underline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kamar
    </a>
</div>

{{-- ── Grid kartu kamar ── --}}
@if($rooms->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
        @foreach($rooms as $room)
            @include('room::Admin.partials.card', ['room' => $room])
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($rooms->hasPages())
        <div class="flex justify-center">
            {{ $rooms->links() }}
        </div>
    @endif

@else
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm
                flex flex-col items-center justify-center py-20 px-8 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5"
             style="background:#fefce8;">
            <svg class="w-8 h-8" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-2">Belum ada kamar</h3>
        <p class="text-[0.85rem] text-slate-500 mb-6 max-w-xs">
            Tambahkan kamar pertama Anda untuk mulai menerima tamu.
        </p>
        <a href="{{ route('admin.rooms.create') }}"
           class="btn-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-[0.875rem] font-semibold no-underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kamar Pertama
        </a>
    </div>
@endif

<script>
{{-- confirmDelete dipanggil dari card.blade.php --}}
function confirmDelete(formEl, roomName) {
    Swal.fire({
        title: 'Hapus Kamar?',
        html:  '<span style="font-size:0.9rem;color:#475569;">Kamar <strong>' + roomName + '</strong> beserta seluruh fasilitas dan foto akan dihapus permanen.</span>',
        icon:  'warning',
        showCancelButton:  true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText:  'Batal',
        reverseButtons:    true,
        focusCancel:       true,
        customClass: {
            confirmButton: 'swal-delete-btn',
        },
        buttonsStyling: true,
    }).then(function(result) {
        if (result.isConfirmed) {
            formEl.submit();
        }
    });
}
</script>

@endsection
