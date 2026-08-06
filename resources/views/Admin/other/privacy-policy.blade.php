@extends('Admin.layouts.app')

@section('title', 'Kebijakan & Privasi')
@section('page_title', 'Kebijakan & Privasi')
@section('page_subtitle', 'Kelola konten halaman Kebijakan & Privasi yang ditampilkan ke publik')

@section('content')

{{-- Flash --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success', title: 'Berhasil',
        text: @json(session('success')),
        timer: 3000, timerProgressBar: true,
        showConfirmButton: false,
        toast: true, position: 'top-end',
        customClass: { popup: 'swal-toast-popup' }
    });
});
</script>
@endif

<div class="max-w-4xl">

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:#fce7f3;">
            <svg class="w-5 h-5" style="color:#9d174d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Kebijakan &amp; Privasi</h2>
            <p class="text-[0.82rem] text-slate-500">Konten ini tampil di halaman publik <span class="font-semibold text-slate-700">/kebijakan-privasi</span></p>
        </div>
        <a href="{{ route('privacy-policy') }}" target="_blank"
           class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                  text-[0.78rem] font-semibold border border-slate-200 bg-white
                  text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Lihat Halaman
        </a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.other.privacy-policy.update') }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="flex items-center gap-2 px-5 py-3 border-b border-slate-100"
                 style="background:#fdf2f8;">
                <svg class="w-3.5 h-3.5 shrink-0" style="color:#9d174d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-[0.75rem] text-pink-900">
                    Anda dapat menggunakan format <strong>HTML</strong> seperti
                    <code class="bg-pink-100 px-1 rounded text-[0.72rem]">&lt;b&gt;</code>,
                    <code class="bg-pink-100 px-1 rounded text-[0.72rem]">&lt;p&gt;</code>,
                    <code class="bg-pink-100 px-1 rounded text-[0.72rem]">&lt;ul&gt;</code>, dll.
                    — atau tulis biasa saja.
                </p>
            </div>

            <div class="p-5">
                <textarea
                    name="privacy_policy"
                    id="privacyContent"
                    rows="22"
                    placeholder="Tulis konten Kebijakan &amp; Privasi di sini...&#10;&#10;Contoh:&#10;Kami berkomitmen untuk melindungi privasi data setiap tamu yang menggunakan layanan kami..."
                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50
                           text-[0.875rem] text-slate-800 leading-relaxed resize-y
                           focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100
                           transition-colors"
                    style="min-height: 420px; font-family: 'Inter', monospace;"
                >{{ old('privacy_policy', $other->privacy_policy) }}</textarea>

                @error('privacy_policy')
                    <p class="mt-2 text-[0.78rem] text-red-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror

                {{-- Versi English --}}
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <label class="flex items-center gap-2 text-[0.82rem] font-semibold text-slate-700 mb-2" for="privacyEnContent">
                        Privacy Policy — English Version
                        <span class="text-[0.68rem] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">EN</span>
                    </label>
                    <textarea
                        name="privacy_policy_en"
                        id="privacyEnContent"
                        rows="18"
                        placeholder="Write the Privacy Policy content in English here..."
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50
                               text-[0.875rem] text-slate-800 leading-relaxed resize-y
                               focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                               transition-colors"
                        style="min-height: 320px; font-family: 'Inter', monospace;"
                    >{{ old('privacy_policy_en', $other->privacy_policy_en) }}</textarea>
                    @error('privacy_policy_en')
                        <p class="mt-2 text-[0.78rem] text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 bg-slate-50/80">
                <p class="text-[0.72rem] text-slate-400">
                    @if($other->updated_at)
                        Terakhir diperbarui: {{ $other->updated_at->locale('id')->diffForHumans() }}
                        @if($other->updater) oleh <span class="font-semibold text-slate-600">{{ $other->updater->name }}</span>@endif
                    @else
                        Belum pernah diperbarui
                    @endif
                </p>
                <div class="flex gap-2.5">
                    <button type="button" onclick="clearContent()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl
                                   text-[0.8rem] font-semibold border border-slate-200 bg-white
                                   text-slate-600 hover:bg-slate-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Kosongkan
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl
                                   text-[0.875rem] font-bold border-none cursor-pointer
                                   transition-all active:scale-95"
                            style="background:#eab308;color:#713f12;"
                            onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                            onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </form>

    {{-- Preview --}}
    <div class="mt-5 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <p class="text-[0.82rem] font-bold text-slate-700 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Pratinjau
            </p>
            <button type="button" onclick="togglePreview()"
                    class="text-[0.72rem] font-semibold text-slate-400 hover:text-slate-700 transition-colors">
                Tampilkan / Sembunyikan
            </button>
        </div>
        <div id="previewBox" class="hidden px-6 py-5">
            <div id="previewContent"
                 class="prose prose-slate max-w-none text-[0.9rem] leading-relaxed text-slate-700">
            </div>
        </div>
    </div>

</div>

<script>
const textarea = document.getElementById('privacyContent');
const previewContent = document.getElementById('previewContent');

function togglePreview() {
    const box = document.getElementById('previewBox');
    box.classList.toggle('hidden');
    if (!box.classList.contains('hidden')) {
        previewContent.innerHTML = textarea.value || '<em class="text-slate-400">Belum ada konten</em>';
    }
}

function clearContent() {
    Swal.fire({
        title: 'Kosongkan konten?',
        text: 'Semua teks di editor akan dihapus. Perubahan belum tersimpan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { confirmButton: 'swal-delete-btn' },
        buttonsStyling: true,
    }).then(r => { if (r.isConfirmed) textarea.value = ''; });
}

textarea.addEventListener('input', function () {
    const box = document.getElementById('previewBox');
    if (!box.classList.contains('hidden')) {
        previewContent.innerHTML = textarea.value || '<em class="text-slate-400">Belum ada konten</em>';
    }
});
</script>

@endsection
