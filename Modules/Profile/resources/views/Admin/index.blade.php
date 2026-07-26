@extends('Admin.layouts.app')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun dan keamanan')

@section('content')

<style>
    .input-base {
        width: 100%;
        padding: 0.625rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        outline: none;
        transition: all 0.15s;
    }
    .input-base:focus {
        background: #fff;
        border-color: #eab308;
        box-shadow: 0 0 0 3px rgba(234,179,8,0.15);
    }
    .input-error { border-color: #ef4444 !important; }
    .input-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.12) !important; }
    .label-base {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.375rem;
    }
    .section-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        padding: 1.75rem;
    }
    .section-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .btn-yellow {
        background: #eab308; color: #713f12;
        border: none; cursor: pointer; font-weight: 700;
        border-radius: 0.75rem; padding: 0.625rem 1.75rem;
        font-size: 0.875rem;
        transition: background 0.15s, color 0.15s;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-yellow:hover { background: #ca8a04; color: #fff; }
    .btn-outline {
        background: #fff; color: #475569;
        border: 1px solid #e2e8f0; cursor: pointer; font-weight: 600;
        border-radius: 0.75rem; padding: 0.625rem 1.5rem;
        font-size: 0.875rem;
        transition: background 0.15s, border-color 0.15s;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
    .pw-wrap { position: relative; }
    .pw-eye {
        position: absolute; right: 0.875rem; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8; padding: 0.25rem;
        transition: color 0.15s;
    }
    .pw-eye:hover { color: #475569; }
    .pw-wrap input { padding-right: 2.5rem; }
    .match-ok  { color: #16a34a; background: #f0fdf4; border-color: #86efac; }
    .match-err { color: #dc2626; background: #fef2f2; border-color: #fca5a5; }
    .avatar-preview {
        width: 88px; height: 88px;
        border-radius: 9999px;
        object-fit: cover;
        border: 3px solid #fef9c3;
        flex-shrink: 0;
    }
    .avatar-initials {
        width: 88px; height: 88px;
        border-radius: 9999px;
        background: #eab308; color: #713f12;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 800;
        flex-shrink: 0;
        border: 3px solid #fef9c3;
    }
    .photo-dropzone {
        border: 2px dashed #e2e8f0;
        border-radius: 0.875rem;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
    }
    .photo-dropzone:hover { border-color: #eab308; background: #fefce8; }
    .photo-dropzone.active { border-color: #eab308; background: #fefce8; }
</style>

{{-- ── Flash SweetAlert ── --}}
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

@if(session('success_password'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success', title: 'Password Diperbarui',
            text: @json(session('success_password')),
            timer: 4000, timerProgressBar: true,
            showConfirmButton: false, toast: true,
            position: 'top-end',
            customClass: { popup: 'swal-toast-popup' }
        });
    });
</script>
@endif

{{-- ── Header ── --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Profil Saya</h2>
        <p class="text-[0.82rem] text-slate-500 mt-0.5">Perbarui informasi akun dan keamanan password Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-7">

    {{-- ══ KOLOM KIRI: Info Profil ══ --}}
    <div class="xl:col-span-7">
        <div class="section-card">
            <h3 class="section-title">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Informasi Profil
            </h3>

            @if($errors->has('name') || $errors->has('email') || $errors->has('wa') || $errors->has('foto'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                <p class="text-sm font-bold text-red-700 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Terdapat kesalahan:
                </p>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                    @foreach(['name','email','wa','foto'] as $field)
                        @error($field)<li>{{ $message }}</li>@enderror
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.updateInfo') }}"
                  enctype="multipart/form-data" id="formInfo">
                @csrf

                {{-- Foto Profil --}}
                <div class="flex items-center gap-5 mb-7 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div id="avatarDisplay">
                        @if($profile->foto)
                            <img src="{{ asset($profile->foto) }}"
                                 alt="Foto Profil"
                                 class="avatar-preview"
                                 id="avatarImg">
                        @else
                            <div class="avatar-initials" id="avatarInitials">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 mb-1">Foto Profil</p>
                        <p class="text-xs text-slate-400 mb-3">JPG, PNG, WEBP — Maks. 2MB. Akan di-crop 400×400px.</p>
                        <div class="photo-dropzone" id="fotoDropzone"
                             onclick="document.getElementById('fotoInput').click()">
                            <svg class="w-6 h-6 mx-auto mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-xs font-medium text-slate-500">Klik atau seret foto ke sini</p>
                        </div>
                        <input type="file" id="fotoInput" name="foto"
                               accept="image/jpeg,image/png,image/webp" class="hidden"
                               onchange="previewFoto(event)">
                        @error('foto')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>


                {{-- Nama --}}
                <div class="mb-5">
                    <label class="label-base" for="name">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Nama lengkap Anda"
                           class="input-base {{ $errors->has('name') ? 'input-error' : '' }}"
                           maxlength="255" required>
                    @error('name')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label class="label-base" for="email">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="email@contoh.com"
                           class="input-base {{ $errors->has('email') ? 'input-error' : '' }}"
                           maxlength="255" required>
                    @error('email')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Nomor WA --}}
                <div class="mb-7">
                    <label class="label-base" for="wa">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <input id="wa" name="wa" type="tel"
                               value="{{ old('wa', $profile->wa) }}"
                               placeholder="08xxxxxxxxxx atau +628xxxxxxxxxx"
                               class="input-base pl-10 {{ $errors->has('wa') ? 'input-error' : '' }}"
                               maxlength="20" required>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Format: 08xx... atau +628xx...</p>
                    @error('wa')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Tombol Simpan Info --}}
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="reset" class="btn-outline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                    <button type="submit" class="btn-yellow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ══ KOLOM KANAN: Ubah Password ══ --}}
    <div class="xl:col-span-5">
        <div class="section-card">
            <h3 class="section-title">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Ubah Password
            </h3>

            @if($errors->has('current_password') || $errors->has('password'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                <p class="text-sm font-bold text-red-700 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Terdapat kesalahan:
                </p>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                    @error('current_password')<li>{{ $message }}</li>@enderror
                    @error('password')<li>{{ $message }}</li>@enderror
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.updatePassword') }}"
                  id="formPassword">
                @csrf

                {{-- Password Saat Ini --}}
                <div class="mb-5">
                    <label class="label-base" for="current_password">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="pw-wrap">
                        <input id="current_password" name="current_password"
                               type="password" placeholder="••••••••"
                               class="input-base {{ $errors->has('current_password') ? 'input-error' : '' }}"
                               autocomplete="current-password">
                        <button type="button" class="pw-eye" onclick="togglePw('current_password', this)"
                                aria-label="Tampilkan password">
                            <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg class="w-5 h-5 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div class="mb-5">
                    <label class="label-base" for="password">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="pw-wrap">
                        <input id="password" name="password"
                               type="password" placeholder="Minimal 8 karakter"
                               class="input-base {{ $errors->has('password') ? 'input-error' : '' }}"
                               autocomplete="new-password"
                               oninput="checkPasswordMatch()">
                        <button type="button" class="pw-eye" onclick="togglePw('password', this)"
                                aria-label="Tampilkan password">
                            <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg class="w-5 h-5 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Minimal 8 karakter.</p>
                </div>


                {{-- Konfirmasi Password --}}
                <div class="mb-2">
                    <label class="label-base" for="password_confirmation">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="pw-wrap">
                        <input id="password_confirmation" name="password_confirmation"
                               type="password" placeholder="Ulangi password baru"
                               class="input-base"
                               autocomplete="new-password"
                               oninput="checkPasswordMatch()">
                        <button type="button" class="pw-eye" onclick="togglePw('password_confirmation', this)"
                                aria-label="Tampilkan konfirmasi password">
                            <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg class="w-5 h-5 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Alert Realtime Konfirmasi Password --}}
                <div id="matchAlert" class="hidden text-xs font-semibold px-3.5 py-2.5 rounded-xl border mt-3 flex items-center gap-2 transition-all duration-200">
                    <svg id="matchIcon" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                    <span id="matchText"></span>
                </div>

                {{-- Indikator kekuatan password --}}
                <div class="mt-4 mb-7">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-xs font-semibold text-slate-500">Kekuatan Password</p>
                        <span id="strengthLabel" class="text-xs font-bold text-slate-300">—</span>
                    </div>
                    <div class="flex gap-1.5" id="strengthBars">
                        <div class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all" id="bar1"></div>
                        <div class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all" id="bar2"></div>
                        <div class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all" id="bar3"></div>
                        <div class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all" id="bar4"></div>
                    </div>
                </div>

                {{-- Tombol Simpan Password --}}
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="reset" class="btn-outline" onclick="resetPasswordForm()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                    <button type="submit" id="btnSavePassword" class="btn-yellow" onclick="return confirmSavePassword()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Info akun card --}}
        <div class="section-card mt-5">
            <h3 class="section-title">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Info Akun
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Role</span>
                    <span class="inline-block px-3 py-0.5 rounded-full text-[0.72rem] font-bold"
                          style="background:#fef9c3;color:#713f12;">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Bergabung</span>
                    <span class="font-semibold text-slate-700">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Email</span>
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-200 px-2.5 py-0.5 rounded-full">
                            Belum Verifikasi
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>{{-- end grid --}}


<script>
/* ═══════════════════════════════════════
   EYE TOGGLE — sembunyikan/tampilkan PW
═══════════════════════════════════════ */
function togglePw(fieldId, btn) {
    const input  = document.getElementById(fieldId);
    const eyeOff = btn.querySelector('.eye-off');
    const eyeOn  = btn.querySelector('.eye-on');
    const show   = input.type === 'password';
    input.type   = show ? 'text' : 'password';
    eyeOff.classList.toggle('hidden', show);
    eyeOn.classList.toggle('hidden', !show);
}

/* ═══════════════════════════════════════
   AJAX — Cek kecocokan password realtime
═══════════════════════════════════════ */
let matchTimer = null;

function checkPasswordMatch() {
    clearTimeout(matchTimer);
    matchTimer = setTimeout(_doCheck, 350);
    updateStrength();
}

function _doCheck() {
    const pw    = document.getElementById('password').value;
    const conf  = document.getElementById('password_confirmation').value;
    const alert = document.getElementById('matchAlert');
    const icon  = document.getElementById('matchIcon');
    const text  = document.getElementById('matchText');

    if (!pw || !conf) {
        alert.classList.add('hidden');
        return;
    }

    fetch('{{ route("admin.profile.checkPassword") }}', {
        method : 'POST',
        headers: {
            'Content-Type'     : 'application/json',
            'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}',
            'X-Requested-With' : 'XMLHttpRequest',
        },
        body: JSON.stringify({ password: pw, password_confirmation: conf })
    })
    .then(r => r.json())
    .then(data => {
        alert.classList.remove('hidden', 'match-ok', 'match-err');
        if (data.match === true) {
            alert.classList.add('match-ok');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
            text.textContent = 'Password cocok!';
        } else {
            alert.classList.add('match-err');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>';
            text.textContent = 'Password tidak cocok.';
        }
    })
    .catch(() => {
        // fallback client-side
        const match = pw === conf;
        alert.classList.remove('hidden', 'match-ok', 'match-err');
        alert.classList.add(match ? 'match-ok' : 'match-err');
        icon.innerHTML = match
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>';
        text.textContent = match ? 'Password cocok!' : 'Password tidak cocok.';
    });
}

/* ═══════════════════════════════════════
   INDIKATOR KEKUATAN PASSWORD
═══════════════════════════════════════ */
function updateStrength() {
    const pw    = document.getElementById('password').value;
    const bars  = [document.getElementById('bar1'), document.getElementById('bar2'),
                   document.getElementById('bar3'), document.getElementById('bar4')];
    const label = document.getElementById('strengthLabel');

    let score = 0;
    if (pw.length >= 8)                score++;
    if (/[A-Z]/.test(pw))             score++;
    if (/[0-9]/.test(pw))             score++;
    if (/[^A-Za-z0-9]/.test(pw))      score++;

    const cfg = [
        { color: '#e2e8f0', label: '—',         text: '' },
        { color: '#ef4444', label: 'Lemah',      text: 'text-red-500' },
        { color: '#f97316', label: 'Sedang',     text: 'text-orange-500' },
        { color: '#eab308', label: 'Cukup Kuat', text: 'text-yellow-600' },
        { color: '#22c55e', label: 'Kuat',       text: 'text-green-600' },
    ];

    bars.forEach((b, i) => {
        b.style.background = i < score ? cfg[score].color : '#e2e8f0';
    });
    label.textContent  = pw.length === 0 ? '—' : cfg[score].label;
    label.className    = 'text-xs font-bold ' + (pw.length === 0 ? 'text-slate-300' : cfg[score].text);
}

/* ═══════════════════════════════════════
   VALIDASI sebelum submit password
═══════════════════════════════════════ */
function confirmSavePassword() {
    const pw   = document.getElementById('password').value;
    const conf = document.getElementById('password_confirmation').value;
    if (pw !== conf) {
        Swal.fire({
            icon: 'warning', title: 'Password Tidak Cocok',
            text: 'Pastikan password baru dan konfirmasinya sama.',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#eab308',
        });
        return false;
    }
    return true;
}

function resetPasswordForm() {
    document.getElementById('matchAlert').classList.add('hidden');
    document.getElementById('strengthLabel').textContent = '—';
    document.getElementById('strengthLabel').className   = 'text-xs font-bold text-slate-300';
    ['bar1','bar2','bar3','bar4'].forEach(id => {
        document.getElementById(id).style.background = '#e2e8f0';
    });
}

/* ═══════════════════════════════════════
   PREVIEW FOTO PROFIL
═══════════════════════════════════════ */
function previewFoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validasi ukuran: maks 2MB
    const maxSize = 2 * 1024 * 1024; // 2MB dalam bytes
    if (file.size > maxSize) {
        // Reset input supaya file terbuang
        document.getElementById('fotoInput').value = '';

        Swal.fire({
            icon: 'warning',
            title: 'Foto Terlalu Besar',
            html: `<div style="font-size:0.9rem;color:#475569;line-height:1.6;">
                        Ukuran foto yang kamu pilih <strong style="color:#ef4444;">${(file.size / 1024 / 1024).toFixed(2)} MB</strong>,
                        melebihi batas maksimal <strong>2 MB</strong>.<br><br>
                        Coba kompres fotonya terlebih dahulu, atau pilih foto yang lebih kecil.
                   </div>`,
            confirmButtonText: 'Oke, Ganti Foto',
            confirmButtonColor: '#eab308',
            customClass: {
                confirmButton: 'rounded-xl px-6 py-2.5 font-semibold text-yellow-900',
            },
        });
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const src = e.target.result;

        // Update avatar di form profil
        const display = document.getElementById('avatarDisplay');
        display.innerHTML = `<img src="${src}" alt="Preview" class="avatar-preview" id="avatarImg">`;

        // Update avatar di topbar navbar (realtime)
        const topbarWrap = document.getElementById('topbarAvatarWrap');
        if (topbarWrap) {
            topbarWrap.innerHTML = `<img src="${src}" alt="avatar" id="topbarAvatarImg"
                class="w-[34px] h-[34px] rounded-full object-cover flex-shrink-0"
                style="border:2px solid #fef9c3;">`;
        }
    };
    reader.readAsDataURL(file);
}

// Drag & drop foto
const dropzone = document.getElementById('fotoDropzone');
if (dropzone) {
    dropzone.addEventListener('dragover',  ev => { ev.preventDefault(); dropzone.classList.add('active'); });
    dropzone.addEventListener('dragleave', ()  => dropzone.classList.remove('active'));
    dropzone.addEventListener('drop', ev => {
        ev.preventDefault();
        dropzone.classList.remove('active');
        const files = ev.dataTransfer.files;
        if (files.length) {
            // Pasang ke input dulu agar validasi previewFoto bisa baca
            try {
                const dt   = new DataTransfer();
                dt.items.add(files[0]);
                document.getElementById('fotoInput').files = dt.files;
            } catch(e) {
                // fallback: langsung panggil dengan file
            }
            previewFoto({ target: { files } });
        }
    });
}
</script>

@endsection
