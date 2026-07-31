<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\OtherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama (public — tidak memerlukan login)
Route::get('/', function () {
    return redirect()->route('index');
});

Route::get('/home', [PublicController::class, 'index'])->name('index');
Route::get('/kamar/{uuid}', [PublicController::class, 'show'])->name('room.show');

// ── Public: Other pages ───────────────────────────────
Route::get('/tentang',           [PublicController::class, 'about'])->name('about');
Route::get('/kebijakan-privasi', [PublicController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/syarat-ketentuan',  [PublicController::class, 'termsConditions'])->name('terms-conditions');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('Admin.index');
    })->name('dashboard');

    // ── Admin: Other (Tentang, Kebijakan & Privasi, Syarat & Ketentuan) ──
    Route::get('/other/tentang',                    [OtherController::class, 'about'])->name('other.about');
    Route::put('/other/tentang',                    [OtherController::class, 'updateAbout'])->name('other.about.update');

    Route::get('/other/kebijakan-privasi',          [OtherController::class, 'privacyPolicy'])->name('other.privacy-policy');
    Route::put('/other/kebijakan-privasi',          [OtherController::class, 'updatePrivacyPolicy'])->name('other.privacy-policy.update');

    Route::get('/other/syarat-ketentuan',           [OtherController::class, 'termsConditions'])->name('other.terms-conditions');
    Route::put('/other/syarat-ketentuan',           [OtherController::class, 'updateTermsConditions'])->name('other.terms-conditions.update');
});

// Visitor routes — arahkan ke halaman utama publik
Route::middleware(['auth', 'role:visitor'])->prefix('visitor')->name('visitor.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('index');
    })->name('dashboard');
});
