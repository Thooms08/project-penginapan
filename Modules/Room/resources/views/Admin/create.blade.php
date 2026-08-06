@extends('Admin.layouts.app')

@section('title', 'Tambah Kamar')
@section('page_title', 'Tambah Kamar')
@section('page_subtitle', 'Isi data kamar baru')

@section('content')

<style>
    /* Custom CSS minimalis khusus untuk elemen kompleks */
    .photo-zone.dragover {
        border-color: #eab308;
        background-color: #fefce8;
    }
    .photo-preview-grid::-webkit-scrollbar {
        width: 6px;
    }
    .photo-preview-grid::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .photo-preview-grid::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>

{{-- Back link --}}
<div class="mb-6">
    <a href="{{ route('admin.rooms.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Kamar
    </a>
</div>

{{-- Validation errors --}}
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-8 shadow-sm">
        <h4 class="text-sm font-bold text-red-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Terdapat kesalahan input:
        </h4>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorLines = @json($errors->all());
            var html = '<div class="text-left text-sm text-slate-600 leading-relaxed">'
                + errorLines.map(function(e){ return '&bull; ' + e; }).join('<br>')
                + '</div>';
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Valid',
                html: html,
                confirmButtonText: 'Perbaiki',
                confirmButtonColor: '#eab308',
                customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-semibold' },
            });
        });
    </script>
@endif

<form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" id="roomForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ── Kolom Kiri (Lebih lebar di desktop - 8 kolom) ── --}}
        <div class="lg:col-span-8 flex flex-col gap-8">

            {{-- Info Dasar --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Informasi Kamar
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama kamar --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="name">
                            Nama Kamar <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="cth. Kamar Deluxe" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all {{ $errors->has('name') ? 'border-red-500 focus:ring-red-400' : '' }}" maxlength="255">
                        @error('name')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nama kamar (English) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="name_en">
                            Room Name
                            <span class="ml-1.5 text-[0.7rem] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                        </label>
                        <input id="name_en" name="name_en" type="text" value="{{ old('name_en') }}" placeholder="e.g. Deluxe Room"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all {{ $errors->has('name_en') ? 'border-red-500' : '' }}" maxlength="255">
                        @error('name_en')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="capacity">
                            Kapasitas Tamu <span class="text-red-500">*</span>
                        </label>
                        <input id="capacity" name="capacity" type="number" value="{{ old('capacity', 2) }}" min="1" max="100" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all {{ $errors->has('capacity') ? 'border-red-500' : '' }}">
                        <p class="text-xs text-slate-400 mt-1.5">Jumlah maksimal orang per kamar</p>
                        @error('capacity')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="price">
                            Harga per Malam (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-slate-400 text-sm font-medium">Rp</span>
                            <input id="price" name="price" type="number" value="{{ old('price') }}" min="0" step="1000" required placeholder="350000"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all {{ $errors->has('price') ? 'border-red-500' : '' }}">
                        </div>
                        @error('price')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="status">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all {{ $errors->has('status') ? 'border-red-500' : '' }}">
                            <option value="available" {{ old('status','available') === 'available' ? 'selected' : '' }}>Tersedia (Siap disewa)</option>
                            <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia (Maintenance/Penuh)</option>
                        </select>
                        @error('status')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-end mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700" for="description">Deskripsi Lengkap</label>
                            <span class="text-xs text-slate-400"><span id="descCount">0</span>/5000 karakter</span>
                        </div>
                        <textarea id="description" name="description" rows="5" maxlength="5000" placeholder="Tulis informasi keunggulan kamar ini..."
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all resize-y {{ $errors->has('description') ? 'border-red-500' : '' }}">{{ old('description') }}</textarea>
                        @error('description')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Deskripsi (English) --}}
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-end mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700" for="description_en">
                                Room Description
                                <span class="ml-1.5 text-[0.7rem] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                            </label>
                            <span class="text-xs text-slate-400"><span id="descEnCount">0</span>/5000 chars</span>
                        </div>
                        <textarea id="description_en" name="description_en" rows="5" maxlength="5000" placeholder="Write the room's strengths and features in English..."
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition-all resize-y {{ $errors->has('description_en') ? 'border-red-500' : '' }}">{{ old('description_en') }}</textarea>
                        @error('description_en')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Fasilitas --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Fasilitas Kamar
                    </h3>
                    <button type="button" onclick="addFacility()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Fasilitas
                    </button>
                </div>

                <div id="facilityList" class="space-y-4">
                    {{-- Populated by JS or old input --}}
                </div>

                <div id="facilityEmpty" class="text-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl mt-4">
                    <p class="text-sm text-slate-500">Belum ada fasilitas yang ditambahkan.</p>
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan (Lebih sempit - 4 kolom) ── --}}
        <div class="lg:col-span-4 flex flex-col gap-8">

            {{-- Upload Foto --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Foto Kamar
                </h3>

                <div class="photo-zone border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center cursor-pointer transition-all hover:border-yellow-400 hover:bg-yellow-50" id="photoZone" onclick="document.getElementById('photos').click()">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-700">Klik atau seret foto ke sini</p>
                    <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, WEBP (Maks. 4MB)</p>
                    <p class="text-[0.65rem] text-slate-400 mt-1 uppercase tracking-wide font-semibold">Foto pertama jadi cover</p>
                </div>

                <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="handlePhotoChange(event)">

                <div class="photo-preview-grid grid grid-cols-3 gap-3 mt-5 max-h-48 overflow-y-auto pr-1" id="photoPreview"></div>
                @error('photos.*')<p class="text-xs text-red-500 mt-3">{{ $message }}</p>@enderror
            </div>

            {{-- Diskon --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                    </svg>
                    Pengaturan Diskon
                </h3>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="discount_type">Tipe Diskon</label>
                        <select id="discount_type" name="discount_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition-all" onchange="toggleDiscountFields()">
                            <option value="none" {{ old('discount_type','none') === 'none' ? 'selected':'' }}>Tidak Ada</option>
                            <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected':'' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected':'' }}>Nominal (Rp)</option>
                        </select>
                    </div>

                    <div id="discountFields" class="{{ old('discount_type','none') === 'none' ? 'hidden' : '' }} space-y-5 border-t border-slate-100 pt-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="discount_value">
                                Besaran Diskon <span id="discountUnit" class="text-slate-400 font-normal">{{ old('discount_type') === 'percentage' ? '(%)' : '(Rp)' }}</span>
                            </label>
                            <input id="discount_value" name="discount_value" type="number" value="{{ old('discount_value', 0) }}" min="0" step="any" placeholder="cth. 15"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition-all {{ $errors->has('discount_value') ? 'border-red-500':'' }}">
                            @error('discount_value')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="discount_min_nights">Syarat Minimal Malam</label>
                            <input id="discount_min_nights" name="discount_min_nights" type="number" value="{{ old('discount_min_nights', 0) }}" min="0" step="1"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition-all">
                            <p class="text-xs text-slate-400 mt-1.5">Isi 0 jika berlaku tanpa syarat min. menginap</p>
                        </div>

                        {{-- Preview perhitungan disulap pakai Tailwind --}}
                        <div id="discountPreview" class="hidden bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                            <!-- Injected by JS -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Action buttons (Sticky di bawah layarnya biar gampang diakses di desktop) --}}
    <div class="mt-8 pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 sticky bottom-0 bg-slate-50/90 backdrop-blur-sm pb-6 z-10">
        <a href="{{ route('admin.rooms.index') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors sm:w-auto w-full">
            Batal
        </a>
        <button type="submit" class="inline-flex justify-center items-center gap-2 px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-yellow-950 text-sm font-bold rounded-xl shadow-sm hover:shadow transition-all sm:w-auto w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Data Kamar
        </button>
    </div>
</form>

<script>
/* ── Facility management (Dirapihin struktur HTML Injeksinya) ── */
let facilityIndex = 0;

function addFacility(data) {
    const list  = document.getElementById('facilityList');
    const empty = document.getElementById('facilityEmpty');
    const idx   = facilityIndex++;
    const d     = data || { name: '', name_en: '', qty: 1, description: '', description_en: '' };

    const row = document.createElement('div');
    row.className = 'relative bg-slate-50 border border-slate-200 rounded-xl p-4 md:p-5 pr-12 transition-all hover:border-slate-300 group';
    row.id = 'frow_' + idx;
    row.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Fasilitas</label>
                <input type="text" name="facilities[${idx}][name]" value="${escHtml(d.name)}" placeholder="cth. AC / TV" required
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition-all" maxlength="100">
            </div>
            <div class="md:col-span-5">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Facility Name <span class="ml-1 text-[0.65rem] font-bold px-1 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                </label>
                <input type="text" name="facilities[${idx}][name_en]" value="${escHtml(d.name_en || '')}" placeholder="e.g. AC / TV"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-300 outline-none transition-all" maxlength="100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah</label>
                <input type="number" name="facilities[${idx}][qty]" value="${d.qty}" min="1" max="99" required
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition-all">
            </div>
            <div class="md:col-span-6">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi Singkat</label>
                <input type="text" name="facilities[${idx}][description]" value="${escHtml(d.description || '')}" placeholder="Opsional..." maxlength="150"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition-all">
            </div>
            <div class="md:col-span-6">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Short Description <span class="ml-1 text-[0.65rem] font-bold px-1 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                </label>
                <input type="text" name="facilities[${idx}][description_en]" value="${escHtml(d.description_en || '')}" placeholder="Optional..." maxlength="150"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-300 outline-none transition-all">
            </div>
        </div>
        <button type="button" onclick="removeFacility('frow_${idx}')" title="Hapus fasilitas"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 border border-transparent hover:border-red-200 transition-all opacity-100 md:opacity-50 md:group-hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    list.appendChild(row);
    empty.style.display = 'none';
}

function removeFacility(id) {
    document.getElementById(id).remove();
    if (!document.getElementById('facilityList').children.length) {
        document.getElementById('facilityEmpty').style.display = 'block';
    }
}

function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

/* ── Photo upload preview ── */
let selectedFiles = [];

function handlePhotoChange(event) {
    Array.from(event.target.files).forEach(f => selectedFiles.push(f));
    renderPreviews();
    setTimeout(() => { event.target.value = ''; }, 0);
}

function removePhoto(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
}

function renderPreviews() {
    const grid = document.getElementById('photoPreview');
    grid.innerHTML = '';

    if (selectedFiles.length === 0) return;

    selectedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const item = document.createElement('div');
            item.className = `relative aspect-square rounded-xl overflow-hidden border-2 ${i === 0 ? 'border-yellow-400 shadow-sm' : 'border-slate-200'}`;
            item.dataset.index = i;
            item.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                ${i === 0 ? '<span class="absolute bottom-1.5 left-1.5 bg-yellow-400 text-yellow-900 text-[0.6rem] font-bold px-2 py-0.5 rounded-full">COVER</span>' : ''}
                <button type="button" onclick="removePhoto(${i})" title="Hapus" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/60 text-white flex items-center justify-center hover:bg-red-500 transition-colors backdrop-blur-sm">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;
            grid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

// Drag & drop
const zone = document.getElementById('photoZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')).forEach(f => selectedFiles.push(f));
    renderPreviews();
});

// Intercept form submit
document.getElementById('roomForm').addEventListener('submit', function(e) {
    if (selectedFiles.length === 0) return;
    e.preventDefault();
    const form = this;
    const fd   = new FormData(form);
    fd.delete('photos[]');
    selectedFiles.forEach(function(file) { fd.append('photos[]', file, file.name); });

    fetch(form.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(resp) {
        if (resp.redirected) { window.location.href = resp.url; } 
        else if (resp.ok) { return resp.text().then(function() { window.location.href = resp.url || '{{ route("admin.rooms.index") }}'; }); } 
        else {
            return resp.text().then(function(html) {
                document.open(); document.write(html); document.close();
                window.history.pushState({}, '', form.action);
            });
        }
    }).catch(function() { form.submit(); });
});

// Description character counter
const descTA = document.getElementById('description');
const descCt = document.getElementById('descCount');
function updateCount() { descCt.textContent = descTA.value.length; }
descTA.addEventListener('input', updateCount);
updateCount();

const descEnTA = document.getElementById('description_en');
const descEnCt = document.getElementById('descEnCount');
if (descEnTA && descEnCt) {
    function updateEnCount() { descEnCt.textContent = descEnTA.value.length; }
    descEnTA.addEventListener('input', updateEnCount);
    updateEnCount();
}

/* ── Discount toggle & Preview (Dirapihkan dengan Utility Classes) ── */
function toggleDiscountFields() {
    const type    = document.getElementById('discount_type').value;
    const fields  = document.getElementById('discountFields');
    const unit    = document.getElementById('discountUnit');
    fields.classList.toggle('hidden', type === 'none');
    if (type === 'percentage') unit.textContent = '(%)';
    else if (type === 'fixed') unit.textContent = '(Rp)';
    updateDiscountPreview();
}

function updateDiscountPreview() {
    const type    = document.getElementById('discount_type').value;
    const value   = parseFloat(document.getElementById('discount_value').value) || 0;
    const minN    = parseInt(document.getElementById('discount_min_nights').value) || 0;
    const price   = parseFloat(document.getElementById('price').value) || 0;
    const preview = document.getElementById('discountPreview');

    if (type === 'none' || value <= 0 || price <= 0) { 
        preview.classList.add('hidden'); 
        return; 
    }

    let discounted = price;
    if (type === 'percentage') discounted = price - (price * value / 100);
    else discounted = price - value;
    discounted = Math.max(0, discounted);

    const fmt    = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const saving = type === 'percentage' ? 'Hemat ' + value + '%' : 'Hemat ' + fmt(value);
    
    let minInfo = minN > 0 
        ? `<div class="text-xs text-slate-500 mt-2 pt-2 border-t border-yellow-200/50">Berlaku min. <span class="font-bold text-slate-700">${minN}</span> malam</div>` 
        : '';

    preview.innerHTML = `
        <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="text-sm text-slate-400 line-through font-medium">${fmt(price)}</span>
            <span class="text-xs font-bold text-green-700 bg-green-100 border border-green-200 px-2 py-0.5 rounded-full">${saving}</span>
        </div>
        <div class="flex items-end gap-1">
            <span class="text-lg font-black text-green-600">${fmt(discounted)}</span>
            <span class="text-xs text-slate-500 mb-1">/ malam</span>
        </div>
        ${minInfo}
    `;
    preview.classList.remove('hidden');
}

['discount_type','discount_value','discount_min_nights','price'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updateDiscountPreview);
});
toggleDiscountFields(); 

@if(old('facilities'))
    @foreach(old('facilities', []) as $fi => $fac)
        addFacility({
            name:           @json($fac['name'] ?? ''),
            name_en:        @json($fac['name_en'] ?? ''),
            qty:            @json($fac['qty'] ?? 1),
            description:    @json($fac['description'] ?? ''),
            description_en: @json($fac['description_en'] ?? ''),
        });
    @endforeach
@endif
</script>

@endsection