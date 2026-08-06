@extends('Visitors.layouts.app')

@section('title', __('visitor.profile_page_title'))

@push('head')
<style>
    :root { --y:#eab308;--yd:#ca8a04;--y50:#fefce8;--y100:#fef9c3;--ytext:#713f12; }
    .profile-wrap { max-width:740px;margin:0 auto;padding:2rem 1.25rem 5rem; }
    @media(min-width:768px){ .profile-wrap{padding:2.5rem 2rem 5rem;} }

    /* ── Avatar upload area ── */
    .avatar-zone {
        position:relative;width:100px;height:100px;border-radius:50%;
        cursor:pointer;flex-shrink:0;
    }
    .avatar-zone img, .avatar-zone .avatar-init {
        width:100%;height:100%;border-radius:50%;object-fit:cover;
        border:3px solid var(--y100);
    }
    .avatar-init {
        display:flex;align-items:center;justify-content:center;
        background:var(--y);font-size:2rem;font-weight:800;color:var(--ytext);
    }
    .avatar-overlay {
        position:absolute;inset:0;border-radius:50%;
        background:rgba(0,0,0,0.42);
        display:flex;flex-direction:column;align-items:center;justify-content:center;
        gap:2px;opacity:0;transition:opacity .18s;
    }
    .avatar-zone:hover .avatar-overlay { opacity:1; }
    .avatar-overlay span { font-size:.6rem;font-weight:700;color:#fff;letter-spacing:.04em; }

    /* ── Card ── */
    .pcard {
        background:white;border-radius:1.25rem;
        border:1px solid #e2e8f0;
        box-shadow:0 1px 4px rgba(0,0,0,.04);
        overflow:hidden;margin-bottom:1rem;
    }
    .pcard-hdr {
        display:flex;align-items:center;gap:.75rem;
        padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;
    }
    .pcard-hdr-icon {
        width:34px;height:34px;border-radius:10px;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
    }
    .pcard-hdr-title { font-size:.9rem;font-weight:700;color:#0f172a; }
    .pcard-hdr-sub   { font-size:.72rem;color:#94a3b8;margin-top:.1rem; }
    .pcard-body      { padding:1.25rem; }
</style>
@endpush

@section('content')
<div class="profile-wrap">

    {{-- Flash success --}}
    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        Swal.fire({icon:'success',title:'{{ __('visitor.profile_success') }}',text:@json(session('success')),
            timer:3000,timerProgressBar:true,showConfirmButton:false,
            toast:true,position:'top-end'});
    });
    </script>
    @endif
    @if(session('success_password'))
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        Swal.fire({icon:'success',title:'{{ __('visitor.profile_password_updated') }}',text:@json(session('success_password')),
            timer:3500,timerProgressBar:true,showConfirmButton:false,
            toast:true,position:'top-end'});
    });
    </script>
    @endif

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('index') }}"
           class="inline-flex items-center gap-1.5 text-[.8rem] font-semibold text-slate-500
                  hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('visitor.home') }}
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-[.8rem] font-semibold text-slate-900">{{ __('visitor.profile') }}</span>
    </div>

    {{-- ── Hero: foto + nama + email ── --}}
    <div class="pcard mb-5">
        <div class="pcard-body">
            <div class="flex items-center gap-4 flex-wrap">

                {{-- Avatar upload --}}
                <form id="fotoQuickForm" method="POST"
                      action="{{ route('visitor.profile.update-info') }}"
                      enctype="multipart/form-data" class="shrink-0">
                    @csrf @method('PUT')
                    {{-- hidden fields agar form valid --}}
                    <input type="hidden" name="name"     value="{{ $user->name }}">
                    <input type="hidden" name="email"    value="{{ $user->email }}">
                    <input type="hidden" name="wa"       value="{{ $profile->wa }}">
                    <input type="hidden" name="city"     value="{{ $profile->city }}">
                    <input type="hidden" name="province" value="{{ $profile->province }}">
                    <input type="hidden" name="country"  value="{{ $profile->country }}">
                    <input type="file" id="fotoInput" name="foto"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="hidden" onchange="this.form.submit()">
                </form>

                <div class="avatar-zone" onclick="document.getElementById('fotoInput').click()"
                     title="Klik untuk ganti foto">
                    @if($profile->foto)
                        <img src="{{ asset($profile->foto) }}"
                             alt="{{ $user->name }}" id="avatarPreview">
                    @elseif($user->avatar)
                        <img src="{{ $user->avatar }}"
                             alt="{{ $user->name }}" id="avatarPreview">
                    @else
                        <div class="avatar-init" id="avatarPreview">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="avatar-overlay">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ __('visitor.profile_change_photo') }}</span>
                    </div>
                </div>

                {{-- Nama & info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-[1.15rem] font-extrabold text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-[.82rem] text-slate-500 mt-0.5 truncate">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[.68rem] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($profile->city || $profile->country)
                            <span class="text-[.72rem] text-slate-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ collect([$profile->city, $profile->province, $profile->country])->filter()->implode(', ') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Hapus foto (jika ada) --}}
                @if($profile->foto)
                    <form method="POST" action="{{ route('visitor.profile.delete-foto') }}"
                          class="shrink-0" id="deleteFotoForm">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" onclick="confirmDeleteFoto()"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl
                                   text-[.72rem] font-semibold border border-red-200 bg-white
                                   text-red-500 hover:bg-red-50 transition-colors shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('visitor.profile_delete_photo') }}
                    </button>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Card: Info Pribadi ── --}}
    <div class="pcard">
        <div class="pcard-hdr">
            <div class="pcard-hdr-icon" style="background:#fef9c3;">
                <svg class="w-4 h-4" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="pcard-hdr-title">{{ __('visitor.profile_info_title') }}</p>
                <p class="pcard-hdr-sub">{{ __('visitor.profile_info_sub') }}</p>
            </div>
        </div>
        <div class="pcard-body">
            <form method="POST" action="{{ route('visitor.profile.update-info') }}"
                  enctype="multipart/form-data" id="infoForm">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_fullname') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name', $user->name) }}" required
                               class="w-full px-3 py-2.5 rounded-xl border text-[.875rem] text-slate-800
                                      bg-slate-50 focus:outline-none focus:ring-2 focus:ring-yellow-100
                                      focus:border-yellow-400 transition-colors
                                      @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        @error('name')
                            <p class="mt-1 text-[.72rem] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_email') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email', $user->email) }}" required
                               class="w-full px-3 py-2.5 rounded-xl border text-[.875rem] text-slate-800
                                      bg-slate-50 focus:outline-none focus:ring-2 focus:ring-yellow-100
                                      focus:border-yellow-400 transition-colors
                                      @error('email') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        @error('email')
                            <p class="mt-1 text-[.72rem] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- WA --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_wa') }}
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[.75rem]
                                         font-bold text-slate-400 pointer-events-none">📱</span>
                            <input type="text" name="wa"
                                   value="{{ old('wa', $profile->wa) }}"
                                   placeholder="08123456789"
                                   class="w-full pl-8 pr-3 py-2.5 rounded-xl border text-[.875rem]
                                          text-slate-800 bg-slate-50 focus:outline-none
                                          focus:ring-2 focus:ring-yellow-100 focus:border-yellow-400
                                          transition-colors
                                          @error('wa') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        </div>
                        @error('wa')
                            <p class="mt-1 text-[.72rem] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">{{ __('visitor.profile_city') }}</label>
                        <input type="text" name="city"
                               value="{{ old('city', $profile->city) }}"
                               placeholder="Jakarta"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-[.875rem]
                                      text-slate-800 bg-slate-50 focus:outline-none
                                      focus:ring-2 focus:ring-yellow-100 focus:border-yellow-400 transition-colors">
                    </div>

                    {{-- Provinsi --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">{{ __('visitor.profile_province') }}</label>
                        <input type="text" name="province"
                               value="{{ old('province', $profile->province) }}"
                               placeholder="DKI Jakarta"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-[.875rem]
                                      text-slate-800 bg-slate-50 focus:outline-none
                                      focus:ring-2 focus:ring-yellow-100 focus:border-yellow-400 transition-colors">
                    </div>

                    {{-- Negara --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">{{ __('visitor.profile_country') }}</label>
                        <input type="text" name="country"
                               value="{{ old('country', $profile->country) }}"
                               placeholder="Indonesia"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-[.875rem]
                                      text-slate-800 bg-slate-50 focus:outline-none
                                      focus:ring-2 focus:ring-yellow-100 focus:border-yellow-400 transition-colors">
                    </div>

                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-[.875rem] font-bold border-none cursor-pointer
                                   transition-all active:scale-95"
                            style="background:#eab308;color:#713f12;"
                            onmouseover="this.style.background='#ca8a04';this.style.color='#fff';"
                            onmouseout="this.style.background='#eab308';this.style.color='#713f12';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('visitor.profile_save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Card: Ganti Password ── --}}
    @if($user->password)
    <div class="pcard">
        <div class="pcard-hdr">
            <div class="pcard-hdr-icon" style="background:#ede9fe;">
                <svg class="w-4 h-4" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="pcard-hdr-title">{{ __('visitor.profile_security_title') }}</p>
                <p class="pcard-hdr-sub">{{ __('visitor.profile_security_sub') }}</p>
            </div>
        </div>
        <div class="pcard-body">
            <form method="POST" action="{{ route('visitor.profile.update-password') }}"
                  id="passwordForm">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Password saat ini --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_current_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="currentPass"
                                   autocomplete="current-password"
                                   class="w-full px-3 pr-10 py-2.5 rounded-xl border text-[.875rem]
                                          text-slate-800 bg-slate-50 focus:outline-none
                                          focus:ring-2 focus:ring-violet-100 focus:border-violet-400
                                          transition-colors
                                          @error('current_password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                            <button type="button" onclick="togglePass('currentPass', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400
                                           hover:text-slate-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5
                                           c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7
                                           -4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-[.72rem] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password baru --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_new_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="newPass"
                                   autocomplete="new-password"
                                   oninput="checkMatch()"
                                   class="w-full px-3 pr-10 py-2.5 rounded-xl border text-[.875rem]
                                          text-slate-800 bg-slate-50 focus:outline-none
                                          focus:ring-2 focus:ring-violet-100 focus:border-violet-400
                                          transition-colors
                                          @error('password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                            <button type="button" onclick="togglePass('newPass', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400
                                           hover:text-slate-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5
                                           c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7
                                           -4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-[.72rem] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi password --}}
                    <div>
                        <label class="block text-[.78rem] font-bold text-slate-700 mb-1.5">
                            {{ __('visitor.profile_confirm_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="confirmPass"
                                   autocomplete="new-password"
                                   oninput="checkMatch()"
                                   class="w-full px-3 pr-10 py-2.5 rounded-xl border text-[.875rem]
                                          text-slate-800 bg-slate-50 focus:outline-none
                                          focus:ring-2 focus:ring-violet-100 focus:border-violet-400
                                          transition-colors border-slate-200">
                            <button type="button" onclick="togglePass('confirmPass', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400
                                           hover:text-slate-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5
                                           c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7
                                           -4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p id="matchHint" class="mt-1 text-[.72rem] hidden"></p>
                    </div>

                </div>

                <p class="mt-3 text-[.72rem] text-slate-400 flex items-center gap-1">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('visitor.profile_password_hint') }}
                </p>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-[.875rem] font-bold border-none cursor-pointer
                                   transition-all active:scale-95"
                            style="background:#7c3aed;color:#fff;"
                            onmouseover="this.style.background='#6d28d9';"
                            onmouseout="this.style.background='#7c3aed';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        {{ __('visitor.profile_update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    {{-- Akun Google: tidak bisa ganti password --}}
    <div class="pcard">
        <div class="pcard-body flex items-center gap-3 py-4">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="text-[.82rem] font-semibold text-slate-700">{{ __('visitor.profile_google_login') }}</p>
                <p class="text-[.72rem] text-slate-400 mt-0.5">{{ __('visitor.profile_google_desc') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Card: Bahasa (mobile only) ── --}}
    @php $__profileLocale = app()->getLocale(); @endphp
    <div class="pcard">
        <div class="pcard-hdr">
            <div class="pcard-hdr-icon" style="background:#f0f9ff;">
                <svg class="w-4 h-4" style="color:#0369a1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </div>
            <div>
                <p class="pcard-hdr-title">{{ __('visitor.language') }}</p>
                <p class="pcard-hdr-sub">
                    {{ __('visitor.profile_lang_current') }}
                </p>
            </div>
        </div>
        <div class="pcard-body">
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('locale.set', 'id') }}"
                   class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl
                          font-bold text-[0.875rem] text-center transition-all no-underline"
                   style="{{ $__profileLocale === 'id'
                       ? 'background:#fef9c3;color:#713f12;border:2px solid #eab308;'
                       : 'background:#f8fafc;color:#64748b;border:2px solid #e2e8f0;' }}">
                    <span style="font-size:1.25rem;">🇮🇩</span>
                    <span>Indonesia</span>
                    @if($__profileLocale === 'id')
                        <svg class="w-4 h-4 shrink-0" style="color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </a>
                <a href="{{ route('locale.set', 'en') }}"
                   class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl
                          font-bold text-[0.875rem] text-center transition-all no-underline"
                   style="{{ $__profileLocale === 'en'
                       ? 'background:#fef9c3;color:#713f12;border:2px solid #eab308;'
                       : 'background:#f8fafc;color:#64748b;border:2px solid #e2e8f0;' }}">
                    <span style="font-size:1.25rem;">🇬🇧</span>
                    <span>English</span>
                    @if($__profileLocale === 'en')
                        <svg class="w-4 h-4 shrink-0" style="color:#713f12;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </a>
            </div>
        </div>
    </div>

</div>{{-- /profile-wrap --}}

@php
$profileLang = [
    'password_match'         => __('visitor.profile_password_match'),
    'password_mismatch'      => __('visitor.profile_password_mismatch'),
    'photo_delete_title'     => __('visitor.profile_photo_delete_title'),
    'photo_delete_text'      => __('visitor.profile_photo_delete_text'),
    'photo_delete_confirm'   => __('visitor.profile_photo_delete_confirm'),
    'photo_delete_cancel'    => __('visitor.profile_photo_delete_cancel'),
];
@endphp
<script>
window.__profileLang = @json($profileLang);

function togglePass(id, btn) {
    const inp = document.getElementById(id);
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

function checkMatch() {
    const np   = document.getElementById('newPass').value;
    const cp   = document.getElementById('confirmPass').value;
    const hint = document.getElementById('matchHint');
    if (!np || !cp) { hint.classList.add('hidden'); return; }
    hint.classList.remove('hidden');
    if (np === cp) {
        hint.textContent = window.__profileLang.password_match;
        hint.className   = 'mt-1 text-[.72rem] text-emerald-600';
    } else {
        hint.textContent = window.__profileLang.password_mismatch;
        hint.className   = 'mt-1 text-[.72rem] text-red-500';
    }
}

function confirmDeleteFoto() {
    Swal.fire({
        title: window.__profileLang.photo_delete_title,
        text:  window.__profileLang.photo_delete_text,
        icon:  'warning',
        showCancelButton: true,
        confirmButtonText: window.__profileLang.photo_delete_confirm,
        cancelButtonText:  window.__profileLang.photo_delete_cancel,
        reverseButtons: true,
        customClass: { confirmButton: 'swal-delete-btn' },
        buttonsStyling: true,
    }).then(r => { if (r.isConfirmed) document.getElementById('deleteFotoForm').submit(); });
}
</script>

@endsection
