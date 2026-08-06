@extends('Admin.layouts.app')

@section('title', 'Profil Hotel')
@section('page_title', 'Profil Hotel')
@section('page_subtitle', 'Kelola informasi dan foto hotel')

@section('content')
<style>
    .input-base {
        width: 100%; padding: 0.625rem 1rem;
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 0.75rem; font-size: 0.875rem;
        outline: none; transition: all 0.15s;
    }
    .input-base:focus {
        background: #fff; border-color: #eab308;
        box-shadow: 0 0 0 3px rgba(234,179,8,0.15);
    }
    .input-error { border-color: #ef4444 !important; }
    .input-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.12) !important; }
    .label-base {
        display: block; font-size: 0.8125rem; font-weight: 600;
        color: #334155; margin-bottom: 0.375rem;
    }
    .section-card {
        background: #fff; border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 1.75rem;
    }
    .section-title {
        font-size: 0.9375rem; font-weight: 700; color: #0f172a;
        display: flex; align-items: center; gap: 0.5rem;
        padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .btn-yellow {
        background: #eab308; color: #713f12; border: none; cursor: pointer;
        font-weight: 700; border-radius: 0.75rem; padding: 0.625rem 1.75rem;
        font-size: 0.875rem; transition: background 0.15s, color 0.15s;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-yellow:hover { background: #ca8a04; color: #fff; }
    .btn-outline {
        background: #fff; color: #475569; border: 1px solid #e2e8f0;
        cursor: pointer; font-weight: 600; border-radius: 0.75rem;
        padding: 0.625rem 1.5rem; font-size: 0.875rem;
        transition: background 0.15s, border-color 0.15s;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
    .photo-dropzone {
        border: 2px dashed #e2e8f0; border-radius: 0.875rem;
        padding: 1.5rem; text-align: center; cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
    }
    .photo-dropzone:hover, .photo-dropzone.dragover {
        border-color: #eab308; background: #fefce8;
    }
    .logo-preview { width: 96px; height: 96px; border-radius: 1rem;
        object-fit: contain; border: 2px solid #fef9c3; background: #fff; }
    .logo-placeholder {
        width: 96px; height: 96px; border-radius: 1rem;
        background: #fefce8; border: 2px dashed #fde68a;
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; color: #ca8a04; font-size: 0.65rem;
        font-weight: 600; gap: 0.25rem;
    }
</style>

{{-- Flash SweetAlert --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success', title: 'Berhasil',
            text: @json(session('success')),
            timer: 3500, timerProgressBar: true,
            showConfirmButton: false, toast: true,
            position: 'top-end',
            customClass: { popup: 'swal-toast-popup' }
        });
    });
</script>
@endif

{{-- Validation errors --}}
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var errs = @json($errors->all());
        Swal.fire({
            icon: 'error', title: 'Input Tidak Valid',
            html: '<ul class="text-left text-sm text-slate-600 space-y-1">'
                  + errs.map(e => '<li class="flex gap-2"><span class="text-red-400">•</span>' + e + '</li>').join('')
                  + '</ul>',
            confirmButtonText: 'Perbaiki',
            confirmButtonColor: '#eab308',
        });
    });
</script>
@endif

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Profil Hotel</h2>
        <p class="text-[0.82rem] text-slate-500 mt-0.5">Informasi yang ditampilkan kepada tamu di halaman utama</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.hotel.update') }}" enctype="multipart/form-data" id="hotelForm">
@csrf

<div class="grid grid-cols-1 xl:grid-cols-12 gap-7">

{{-- ══ KOLOM KIRI ══ --}}
<div class="xl:col-span-8 flex flex-col gap-7">

    {{-- Informasi Dasar --}}
    <div class="section-card">
        <h3 class="section-title">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Informasi Hotel
        </h3>

        {{-- Logo Hotel --}}
        <div class="flex items-center gap-5 mb-7 p-4 bg-slate-50 rounded-2xl border border-slate-100">
            <div id="logoDisplay">
                @if($hotel->logo)
                    <img src="{{ asset($hotel->logo) }}" alt="Logo Hotel" class="logo-preview" id="logoImg">
                @else
                    <div class="logo-placeholder" id="logoPlaceholder">
                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Logo</span>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-700 mb-1">Logo Hotel</p>
                <p class="text-xs text-slate-400 mb-3">JPG, PNG, WEBP — Maks. 2MB. Disarankan ukuran persegi.</p>
                <div class="photo-dropzone" id="logoDropzone" onclick="document.getElementById('logoInput').click()">
                    <svg class="w-6 h-6 mx-auto mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-xs font-medium text-slate-500">Klik atau seret logo ke sini</p>
                </div>
                <input type="file" id="logoInput" name="logo" accept="image/jpeg,image/png,image/webp"
                       class="hidden" onchange="previewLogo(event)">
                @error('logo')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Nama Hotel --}}
        <div class="mb-5">
            <label class="label-base" for="name">Nama Hotel</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $hotel->name) }}"
                   placeholder="cth. Hotel Melati Indah"
                   class="input-base {{ $errors->has('name') ? 'input-error' : '' }}"
                   maxlength="255">
            @error('name')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Nama Hotel (English) --}}
        <div class="mb-5">
            <label class="label-base" for="name_en">
                Hotel Name
                <span class="ml-1.5 text-[0.7rem] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
            </label>
            <input id="name_en" name="name_en" type="text"
                   value="{{ old('name_en', $hotel->name_en) }}"
                   placeholder="e.g. Melati Indah Hotel"
                   class="input-base {{ $errors->has('name_en') ? 'input-error' : '' }}"
                   maxlength="255">
            @error('name_en')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-5">
            <div class="flex justify-between items-end mb-1.5">
                <label class="label-base mb-0" for="description">Deskripsi Hotel</label>
                <span class="text-xs text-slate-400"><span id="descCount">{{ strlen(old('description', $hotel->description ?? '')) }}</span>/5000</span>
            </div>
            <textarea id="description" name="description" rows="5" maxlength="5000"
            placeholder="Ceritakan keunggulan dan keistimewaan hotel Anda..."
            class="input-base resize-none {{ $errors->has('description') ? 'input-error' : '' }}"
            oninput="document.getElementById('descCount').textContent=this.value.length">{{ old('description', $hotel->description) }}</textarea>
            @error('description')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi (English) --}}
        <div class="mb-5">
            <div class="flex justify-between items-end mb-1.5">
                <label class="label-base mb-0" for="description_en">
                    Hotel Description
                    <span class="ml-1.5 text-[0.7rem] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                </label>
                <span class="text-xs text-slate-400"><span id="descEnCount">{{ strlen(old('description_en', $hotel->description_en ?? '')) }}</span>/5000</span>
            </div>
            <textarea id="description_en" name="description_en" rows="5" maxlength="5000"
            placeholder="Tell your hotel's strengths and uniqueness..."
            class="input-base resize-none {{ $errors->has('description_en') ? 'input-error' : '' }}"
            oninput="document.getElementById('descEnCount').textContent=this.value.length">{{ old('description_en', $hotel->description_en) }}</textarea>
            @error('description_en')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Alamat --}}
        <div class="mb-5">
            <label class="label-base" for="address">Alamat</label>
            <textarea id="address" name="address" rows="2" maxlength="500"
                      placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota"
                      class="input-base resize-none {{ $errors->has('address') ? 'input-error' : '' }}">{{ old('address', $hotel->address) }}</textarea>
            @error('address')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Alamat (English) --}}
        <div class="mb-5">
            <label class="label-base" for="address_en">
                Address
                <span class="ml-1.5 text-[0.7rem] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
            </label>
            <textarea id="address_en" name="address_en" rows="2" maxlength="500"
                      placeholder="e.g. Jl. Example No. 1, Village, District, City"
                      class="input-base resize-none {{ $errors->has('address_en') ? 'input-error' : '' }}">{{ old('address_en', $hotel->address_en) }}</textarea>
            @error('address_en')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Kontak & Maps --}}
    <div class="section-card">
        <h3 class="section-title">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Kontak & Lokasi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            {{-- Nomor WA --}}
            <div>
                <label class="label-base" for="wa">Nomor WhatsApp</label>
                <div class="relative">
                    <input id="wa" name="wa" type="number"
                           value="{{ old('wa', $hotel->wa) }}"
                           placeholder="08xxxxxxxxxx"
                           class="input-base pl-10 {{ $errors->has('wa') ? 'input-error' : '' }}"
                           maxlength="20">
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Format: 08xx... atau +628xx...</p>
                @error('wa')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="label-base" for="email">Email Hotel</label>
                <div class="relative">
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $hotel->email) }}"
                           placeholder="info@hotel.com"
                           class="input-base pl-10 {{ $errors->has('email') ? 'input-error' : '' }}"
                           maxlength="255">
                </div>
                @error('email')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Link Google Maps --}}
        <div>
            <label class="label-base" for="maps">
                Link Google Maps (Embed)
            </label>
            <div class="relative">
                <textarea id="maps" name="maps" rows="3"
                          placeholder='Paste tag &lt;iframe ...&gt; dari Google Maps "Share → Embed a map"'
                          class="input-base pl-10 resize-none font-mono text-xs {{ $errors->has('maps') ? 'input-error' : '' }}">{{ old('maps', $hotel->maps) }}</textarea>
            </div>
            <p class="text-xs text-slate-400 mt-1.5">
                Buka Google Maps → Cari lokasi hotel → Share → Embed a map → salin kode
                <code class="bg-slate-100 px-1 rounded">&lt;iframe&gt;</code> seluruhnya.
            </p>
            @error('maps')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Preview Maps (jika sudah ada) --}}
        @if($hotel->maps)
        <div class="mt-5">
            <p class="text-xs font-semibold text-slate-500 mb-2 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Preview Peta Tersimpan
            </p>
            <div class="rounded-xl overflow-hidden border border-slate-200 h-48">
                {!! $hotel->maps !!}
            </div>
        </div>
        @endif
    </div>


</div>{{-- end kolom kiri --}}

{{-- ══ KOLOM KANAN: Upload Foto ══ --}}
<div class="xl:col-span-4">
    <div class="section-card">
        <h3 class="section-title">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Foto Hotel
        </h3>

        {{-- Drop zone upload foto baru --}}
        <div class="photo-dropzone mb-4" id="photoZone"
             onclick="document.getElementById('photosInput').click()">
            <svg class="w-10 h-10 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm font-semibold text-slate-600">Klik atau seret foto ke sini</p>
            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 2MB per foto</p>
            <p class="text-xs text-slate-400">Bisa pilih lebih dari 1 foto sekaligus</p>
        </div>
        <input type="file" id="photosInput" name="photos[]" multiple
               accept="image/jpeg,image/png,image/webp" class="hidden"
               onchange="handlePhotoSelect(event)">

        {{-- Preview foto yang akan diupload --}}
        <div id="newPhotoPreview" class="grid grid-cols-3 gap-2 mb-4"></div>

        {{-- Foto tersimpan --}}
        @if($hotel->photos->count() > 0)
        <div class="border-t border-slate-100 pt-4">
            <p class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $hotel->photos->count() }} Foto Tersimpan
            </p>
            <div class="grid grid-cols-3 gap-2">
                @foreach($hotel->photos as $photo)
                <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200">
                    <img src="{{ asset($photo->photo) }}"
                         alt="Foto Hotel"
                         class="w-full h-full object-cover">
                    {{-- Tombol hapus foto --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center">
                        <button type="button"
                                onclick="confirmDeletePhoto({{ $photo->id }}, '{{ route('admin.hotel.photos.delete', $photo->id) }}')"
                                class="opacity-0 group-hover:opacity-100 transition-opacity
                                       w-8 h-8 rounded-full bg-red-500 text-white
                                       flex items-center justify-center hover:bg-red-600
                                       shadow-lg"
                                title="Hapus foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="border-t border-slate-100 pt-4">
            <div class="text-center py-8 bg-slate-50 rounded-xl">
                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-slate-400">Belum ada foto hotel</p>
            </div>
        </div>
        @endif
    </div>
</div>

</div>{{-- end grid --}}

{{-- ══ ACTION BAR FIXED BOTTOM — di dalam <form> ══ --}}
<div class="mt-8 pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3
            sticky bottom-0 bg-slate-50/90 backdrop-blur-sm pb-6 z-10">
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-300
              text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors
              sm:w-auto w-full">
        Batal
    </a>
    <button type="submit" id="btnSimpan"
            class="inline-flex justify-center items-center gap-2 px-8 py-3
                   bg-yellow-500 hover:bg-yellow-600 text-yellow-950 text-sm font-bold
                   rounded-xl shadow-sm hover:shadow transition-all sm:w-auto w-full">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Simpan Profil Hotel
    </button>
</div>

</form>{{-- form ditutup SETELAH action bar --}}

 {{-- Form hapus foto (hidden, dipanggil JS) --}}
        <form method="POST" id="deletePhotoForm" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

<script>
/* ═══════════════════════════════════════════
   LOGO PREVIEW
═══════════════════════════════════════════ */
const MAX_SIZE = 2 * 1024 * 1024; // 2MB

function previewLogo(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > MAX_SIZE) {
        event.target.value = '';
        Swal.fire({
            icon: 'warning',
            title: 'Ukuran File Terlalu Besar',
            html: '<p class="text-slate-600 text-sm">Logo yang Anda pilih berukuran <strong>'
                  + (file.size / 1024 / 1024).toFixed(2) + ' MB</strong>.</p>'
                  + '<p class="text-slate-500 text-sm mt-1">Ukuran maksimal yang diizinkan adalah <strong>2 MB</strong>. Silakan kompres atau pilih file lain.</p>',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#eab308',
        });
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const display = document.getElementById('logoDisplay');
        display.innerHTML = '<img src="' + e.target.result + '" alt="Logo Preview" class="logo-preview" id="logoImg">';
    };
    reader.readAsDataURL(file);
}

/* ═══════════════════════════════════════════
   FOTO HOTEL — Multiple upload + preview
═══════════════════════════════════════════ */
let pendingPhotos = [];

function handlePhotoSelect(event) {
    const files = Array.from(event.target.files);
    const oversized = [];

    files.forEach(function (file) {
        if (file.size > MAX_SIZE) {
            oversized.push(file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)');
        } else {
            pendingPhotos.push(file);
        }
    });

    // Reset input supaya file yang sama bisa dipilih lagi
    setTimeout(function () { event.target.value = ''; }, 0);

    if (oversized.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Beberapa File Terlalu Besar',
            html: '<p class="text-slate-600 text-sm mb-2">File berikut melebihi batas <strong>2 MB</strong> dan tidak akan diupload:</p>'
                  + '<ul class="text-left text-sm text-red-500 space-y-0.5">'
                  + oversized.map(function (n) { return '<li>• ' + n + '</li>'; }).join('')
                  + '</ul>'
                  + (files.length - oversized.length > 0
                      ? '<p class="text-slate-500 text-sm mt-2">' + (files.length - oversized.length) + ' foto lain berhasil ditambahkan.</p>'
                      : ''),
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#eab308',
        });
    }

    renderNewPreviews();
}

function removeNewPhoto(index) {
    pendingPhotos.splice(index, 1);
    renderNewPreviews();
}

function renderNewPreviews() {
    const grid = document.getElementById('newPhotoPreview');
    grid.innerHTML = '';

    if (pendingPhotos.length === 0) return;

    pendingPhotos.forEach(function (file, i) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const item = document.createElement('div');
            item.className = 'relative aspect-square rounded-xl overflow-hidden border-2 border-yellow-300 shadow-sm';
            item.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">'
                + '<span class="absolute top-1 left-1 bg-yellow-400 text-yellow-900 text-[0.55rem] font-bold px-1.5 py-0.5 rounded-full">BARU</span>'
                + '<button type="button" onclick="removeNewPhoto(' + i + ')"'
                + ' class="absolute top-1 right-1 w-5 h-5 rounded-full bg-slate-900/60 text-white flex items-center justify-center hover:bg-red-500 transition-colors">'
                + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            grid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

/* ═══════════════════════════════════════════
   DRAG & DROP zona foto
═══════════════════════════════════════════ */
const photoZone = document.getElementById('photoZone');
photoZone.addEventListener('dragover', function (e) {
    e.preventDefault();
    photoZone.classList.add('dragover');
});
photoZone.addEventListener('dragleave', function () {
    photoZone.classList.remove('dragover');
});
photoZone.addEventListener('drop', function (e) {
    e.preventDefault();
    photoZone.classList.remove('dragover');
    const dropped = Array.from(e.dataTransfer.files).filter(function (f) {
        return f.type.startsWith('image/');
    });
    const oversized = [];
    dropped.forEach(function (file) {
        if (file.size > MAX_SIZE) {
            oversized.push(file.name);
        } else {
            pendingPhotos.push(file);
        }
    });
    if (oversized.length) {
        Swal.fire({
            icon: 'warning', title: 'File Terlalu Besar',
            html: '<p class="text-sm text-slate-600">File berikut melebihi 2MB:</p><ul class="text-sm text-red-500">'
                  + oversized.map(function (n) { return '<li>• ' + n + '</li>'; }).join('') + '</ul>',
            confirmButtonText: 'OK', confirmButtonColor: '#eab308',
        });
    }
    renderNewPreviews();
});

/* ═══════════════════════════════════════════
   SUBMIT FORM — inject pendingPhotos ke FormData
═══════════════════════════════════════════ */
document.getElementById('hotelForm').addEventListener('submit', function (e) {
    // Jika tidak ada foto baru yang dipilih, biarkan form submit secara normal (native)
    if (pendingPhotos.length === 0) {
        return true;
    }

    // Ada foto baru — gunakan fetch agar bisa inject file ke FormData
    e.preventDefault();
    const form = this;

    // Disable tombol agar tidak double-submit
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menyimpan...';

    const fd = new FormData(form);
    fd.delete('photos[]');
    pendingPhotos.forEach(function (file) {
        fd.append('photos[]', file, file.name);
    });

    fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(function (resp) {
        if (resp.redirected) {
            window.location.href = resp.url;
        } else {
            return resp.text().then(function (html) {
                document.open(); document.write(html); document.close();
                window.history.pushState({}, '', form.action);
            });
        }
    })
    .catch(function () {
        // Fallback: submit biasa jika fetch gagal
        form.submit();
    });
});

/* ═══════════════════════════════════════════
   KONFIRMASI HAPUS FOTO
═══════════════════════════════════════════ */
function confirmDeletePhoto(photoId, url) {
    Swal.fire({
        title: 'Hapus Foto?',
        text:  'Foto ini akan dihapus permanen dan tidak bisa dikembalikan.',
        icon:  'warning',
        showCancelButton:  true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText:  'Batal',
        reverseButtons:    true,
        focusCancel:       true,
        customClass: { confirmButton: 'swal-delete-btn' },
        buttonsStyling: true,
    }).then(function (result) {
        if (result.isConfirmed) {
            const form = document.getElementById('deletePhotoForm');
            form.action = url;
            form.submit();
        }
    });
}
</script>

@endsection
