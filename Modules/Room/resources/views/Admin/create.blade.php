@extends('Admin.layouts.app')

@section('title', 'Tambah Kamar')
@section('page_title', 'Tambah Kamar')
@section('page_subtitle', 'Isi data kamar baru')

@section('content')

<style>
    /* ── Form styles ── */
    .form-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.45rem;
    }
    .form-label span.req { color: #ef4444; margin-left: 2px; }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.72rem 1rem;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        color: #1e293b;
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #eab308;
        box-shadow: 0 0 0 3px rgba(234,179,8,0.15);
        background: #fff;
    }
    .form-input.is-error, .form-textarea.is-error { border-color: #ef4444; }
    .form-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem; }
    .error-msg { font-size: 0.78rem; color: #ef4444; margin-top: 0.35rem; }

    /* ── Facility row ── */
    .facility-row {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.1rem 1.25rem;
        margin-bottom: 0.75rem;
        position: relative;
    }

    /* ── Photo upload zone ── */
    .photo-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
    }
    .photo-zone:hover, .photo-zone.dragover {
        border-color: #eab308;
        background: #fefce8;
    }
    .photo-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .photo-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 2px solid #e2e8f0;
    }
    .photo-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .photo-preview-item .remove-btn {
        position: absolute; top: 4px; right: 4px;
        width: 22px; height: 22px; border-radius: 50%;
        background: rgba(15,23,42,0.7); color: #fff;
        border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; line-height: 1;
    }
    .photo-preview-item.is-cover { border-color: #eab308; }
    .cover-badge {
        position: absolute; bottom: 4px; left: 4px;
        background: #eab308; color: #713f12;
        font-size: 0.62rem; font-weight: 700;
        padding: 1px 6px; border-radius: 999px;
    }

    /* ── Buttons ── */
    .btn-primary {
        background: #eab308; color: #713f12;
        border: none; cursor: pointer;
        font-weight: 600; font-size: 0.875rem;
        transition: background 0.15s, color 0.15s;
    }
    .btn-primary:hover { background: #ca8a04; color: #fff; }

    .btn-outline {
        background: #f8fafc; color: #475569;
        border: 1.5px solid #e2e8f0; cursor: pointer;
        font-weight: 600; font-size: 0.875rem;
        transition: border-color 0.15s, background 0.15s;
    }
    .btn-outline:hover { border-color: #94a3b8; background: #fff; }
</style>

{{-- Back link --}}
<div class="mb-5">
    <a href="{{ route('admin.rooms.index') }}"
       class="inline-flex items-center gap-1.5 text-[0.85rem] text-slate-500 hover:text-slate-800 no-underline transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Kamar
    </a>
</div>

{{-- Validation errors --}}
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-6">
        <p class="text-[0.85rem] font-semibold text-red-700 mb-1.5">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside text-[0.82rem] text-red-600 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorLines = @json($errors->all());
            var html = '<div style="text-align:left;font-size:0.85rem;color:#475569;line-height:1.7;">'
                + errorLines.map(function(e){ return '&bull; ' + e; }).join('<br>')
                + '</div>';
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Valid',
                html: html,
                confirmButtonText: 'Perbaiki',
                customClass: { confirmButton: 'swal-confirm-btn' },
            });
        });
    </script>
@endif

<form method="POST" action="{{ route('admin.rooms.store') }}"
      enctype="multipart/form-data" id="roomForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Kolom kiri (2/3) ── --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Info Dasar --}}
            <div class="form-card">
                <h3 class="text-[0.9rem] font-bold text-slate-900 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Informasi Kamar
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama kamar --}}
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nama Kamar <span class="req">*</span></label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name') }}"
                               placeholder="cth. Kamar Deluxe"
                               class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                               maxlength="255" required>
                        @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="form-label" for="capacity">Kapasitas <span class="req">*</span></label>
                        <input id="capacity" name="capacity" type="number"
                               value="{{ old('capacity', 2) }}"
                               min="1" max="100" required
                               class="form-input {{ $errors->has('capacity') ? 'is-error' : '' }}">
                        <p class="form-hint">Jumlah tamu maksimal</p>
                        @error('capacity')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="form-label" for="price">Harga per Malam (Rp) <span class="req">*</span></label>
                        <input id="price" name="price" type="number"
                               value="{{ old('price') }}"
                               min="0" step="1000" required
                               placeholder="cth. 350000"
                               class="form-input {{ $errors->has('price') ? 'is-error' : '' }}">
                        @error('price')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="form-label" for="status">Status <span class="req">*</span></label>
                        <select id="status" name="status" required
                                class="form-select {{ $errors->has('status') ? 'is-error' : '' }}">
                            <option value="available"   {{ old('status','available') === 'available'   ? 'selected' : '' }}>Tersedia</option>
                            <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                        @error('status')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="sm:col-span-2">
                        <label class="form-label" for="description">
                            Deskripsi Lengkap
                            <span class="font-normal text-slate-400 text-[0.78rem]">(maks. 5000 karakter)</span>
                        </label>
                        <textarea id="description" name="description"
                                  rows="6" maxlength="5000"
                                  placeholder="Tulis informasi lengkap tentang kamar ini..."
                                  class="form-textarea {{ $errors->has('description') ? 'is-error' : '' }}">{{ old('description') }}</textarea>
                        <p class="form-hint">
                            <span id="descCount">0</span>/5000 karakter
                        </p>
                        @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Fasilitas --}}
            <div class="form-card">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-[0.9rem] font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Fasilitas Kamar
                    </h3>
                    <button type="button" onclick="addFacility()"
                        class="btn-outline flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[0.8rem]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Fasilitas
                    </button>
                </div>

                <div id="facilityList">
                    {{-- Populated by JS or old input --}}
                </div>

                <p class="text-[0.78rem] text-slate-400 mt-1" id="facilityEmpty">
                    Belum ada fasilitas. Klik "Tambah Fasilitas" untuk menambah.
                </p>
            </div>
        </div>

        {{-- ── Kolom kanan (1/3) ── --}}
        <div class="flex flex-col gap-6">

            {{-- Upload Foto --}}
            <div class="form-card">
                <h3 class="text-[0.9rem] font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" style="color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Foto Kamar
                </h3>

                <div class="photo-zone" id="photoZone" onclick="document.getElementById('photos').click()">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-[0.82rem] font-semibold text-slate-500">Klik atau seret foto ke sini</p>
                    <p class="text-[0.72rem] text-slate-400 mt-1">JPG, PNG, WEBP · Maks. 4MB/foto</p>
                    <p class="text-[0.72rem] text-slate-400">Foto pertama akan menjadi cover</p>
                </div>

                <input type="file" id="photos" name="photos[]"
                       multiple accept="image/*"
                       class="hidden" onchange="handlePhotoChange(event)">

                <div class="photo-preview-grid" id="photoPreview"></div>
                @error('photos.*')<p class="error-msg mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Action buttons --}}
            <div class="form-card">
                <button type="submit"
                    class="btn-primary w-full flex items-center justify-center gap-2
                           px-5 py-3 rounded-xl text-[0.9rem] font-semibold mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Kamar
                </button>
                <a href="{{ route('admin.rooms.index') }}"
                   class="btn-outline w-full flex items-center justify-center gap-2
                          px-5 py-3 rounded-xl text-[0.9rem] no-underline">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

<script>
/* ── Facility management ─────────────────────────── */
let facilityIndex = 0;

function addFacility(data) {
    const list  = document.getElementById('facilityList');
    const empty = document.getElementById('facilityEmpty');
    const idx   = facilityIndex++;
    const d     = data || { name: '', qty: 1, description: '' };

    const row = document.createElement('div');
    row.className = 'facility-row';
    row.id = 'frow_' + idx;
    row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-8">
            <div>
                <label class="form-label text-xs">Nama Fasilitas</label>
                <input type="text" name="facilities[${idx}][name]"
                       value="${escHtml(d.name)}"
                       placeholder="cth. TV, Kasur, AC"
                       class="form-input" maxlength="100" required>
            </div>
            <div>
                <label class="form-label text-xs">Jumlah (qty)</label>
                <input type="number" name="facilities[${idx}][qty]"
                       value="${d.qty}" min="1" max="99"
                       class="form-input" required>
            </div>
            <div>
                <label class="form-label text-xs">Deskripsi <span class="font-normal text-slate-400">(maks. 150 karakter)</span></label>
                <input type="text" name="facilities[${idx}][description]"
                       value="${escHtml(d.description || '')}"
                       placeholder="Keterangan singkat..."
                       class="form-input" maxlength="150">
            </div>
        </div>
        <button type="button" onclick="removeFacility('frow_${idx}')"
            class="absolute top-3 right-3 w-7 h-7 rounded-lg flex items-center justify-center
                   bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-colors"
            title="Hapus fasilitas">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    list.appendChild(row);
    empty.style.display = 'none';
}

function removeFacility(id) {
    document.getElementById(id).remove();
    if (!document.getElementById('facilityList').children.length) {
        document.getElementById('facilityEmpty').style.display = '';
    }
}

function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

/* ── Photo upload preview ────────────────────────── */
let selectedFiles = [];

function handlePhotoChange(event) {
    const newFiles = Array.from(event.target.files);
    selectedFiles  = selectedFiles.concat(newFiles);
    renderPreviews();
    // Reset input agar bisa pilih file yang sama lagi
    event.target.value = '';
}

function removePhoto(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
    syncFilesInput();
}

function renderPreviews() {
    const grid = document.getElementById('photoPreview');
    grid.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const item = document.createElement('div');
            item.className = 'photo-preview-item' + (i === 0 ? ' is-cover' : '');
            item.innerHTML = `
                <img src="${e.target.result}" alt="">
                ${i === 0 ? '<span class="cover-badge">Cover</span>' : ''}
                <button type="button" class="remove-btn" onclick="removePhoto(${i})" title="Hapus foto">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            grid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
    syncFilesInput();
}

function syncFilesInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    document.getElementById('photos').files = dt.files;
}

// Drag & drop
const zone = document.getElementById('photoZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    selectedFiles = selectedFiles.concat(files);
    renderPreviews();
});

// Description character counter
const descTA = document.getElementById('description');
const descCt = document.getElementById('descCount');
function updateCount() { descCt.textContent = descTA.value.length; }
descTA.addEventListener('input', updateCount);
updateCount();

// Restore old facility input on validation error
@if(old('facilities'))
    @foreach(old('facilities', []) as $fi => $fac)
        addFacility({
            name:        @json($fac['name'] ?? ''),
            qty:         @json($fac['qty'] ?? 1),
            description: @json($fac['description'] ?? ''),
        });
    @endforeach
@endif
</script>

@endsection
