@extends('Visitors.layouts.app')

@section('title', 'Syarat & Ketentuan')

@push('head')
<style>
    .other-wrap {
        max-width: 820px;
        margin: 0 auto;
        padding: 2.5rem 1.25rem 5rem;
    }
    @media (min-width: 768px) {
        .other-wrap { padding: 3.5rem 2rem 6rem; }
    }
    .other-hero {
        display: flex; align-items: center; gap: 1rem;
        margin-bottom: 2rem; padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .other-hero-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; background: #ede9fe;
    }
    .other-hero-title {
        font-size: 1.6rem; font-weight: 800;
        color: #0f172a; letter-spacing: -0.02em; line-height: 1.2;
    }
    .other-hero-sub { font-size: 0.82rem; color: #94a3b8; margin-top: 0.2rem; }
    .other-content {
        background: white; border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        padding: 2rem;
        font-size: 0.9375rem; line-height: 1.8;
        color: #374151;
        white-space: pre-wrap; word-break: break-word;
    }
    @media (max-width: 640px) {
        .other-content { padding: 1.25rem; font-size: 0.875rem; }
    }
    .other-content p  { margin-bottom: 0.9rem; }
    .other-content h1,.other-content h2,.other-content h3 {
        font-weight: 700; color: #0f172a; margin: 1.4rem 0 0.6rem;
    }
    .other-content h2 { font-size: 1.15rem; }
    .other-content h3 { font-size: 1rem; }
    .other-content ul,.other-content ol { padding-left: 1.5rem; margin-bottom: 0.9rem; }
    .other-content li  { margin-bottom: 0.3rem; }
    .other-content a   { color: #5b21b6; text-decoration: underline; }
    .other-content strong { font-weight: 700; color: #1e293b; }
    .other-content hr  { border-color: #e2e8f0; margin: 1.5rem 0; }
    .other-empty {
        display: flex; flex-direction: column; align-items: center;
        padding: 4rem 2rem; text-align: center; color: #94a3b8;
    }
    .other-empty svg { margin-bottom: 1rem; opacity: 0.4; }
    .other-empty p   { font-size: 0.875rem; }
    .other-back {
        display: inline-flex; align-items: center; gap: 0.5rem;
        margin-bottom: 1.5rem; font-size: 0.82rem; font-weight: 600;
        color: #64748b; text-decoration: none; transition: color 0.15s;
    }
    .other-back:hover { color: #5b21b6; }
    .other-links {
        margin-top: 2rem; display: flex; flex-wrap: wrap; gap: 0.75rem;
    }
    .other-links a {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1rem; border-radius: 0.75rem;
        font-size: 0.8rem; font-weight: 600; text-decoration: none;
        border: 1px solid #e2e8f0; background: white; color: #475569;
        transition: all 0.15s;
    }
    .other-links a:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
</style>
@endpush

@section('content')
<div class="other-wrap">

    <a href="{{ route('index') }}" class="other-back">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('visitor.back_to_home') }}
    </a>

    <div class="other-hero">
        <div class="other-hero-icon">
            <svg class="w-6 h-6" style="color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="other-hero-title">{{ __('visitor.terms_conditions') }}</h1>
            <p class="other-hero-sub">
                {{ optional(\Modules\Profile\Models\ProfileHotel::first())->trans('name') ?? 'Penginapan' }}
            </p>
        </div>
    </div>

    <div class="other-content">
        @php $termsContent = $other->trans('terms_conditions'); @endphp
        @if($termsContent)
            {!! $termsContent !!}
        @else
            <div class="other-empty">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>{{ __('visitor.no_content') }}</p>
                <p class="text-[0.78rem] mt-1">{{ __('visitor.no_content_sub') }}</p>
            </div>
        @endif
    </div>

    <div class="other-links">
        <a href="{{ route('about') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __('visitor.about_us') }}
        </a>
        <a href="{{ route('privacy-policy') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            {{ __('visitor.privacy_policy') }}
        </a>
    </div>

</div>
@endsection
