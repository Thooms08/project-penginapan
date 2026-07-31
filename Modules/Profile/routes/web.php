<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\Admin\ProfileController;
use Modules\Profile\Http\Controllers\Admin\ProfileHotelController;
use Modules\Profile\Http\Controllers\Visitor\VisitorProfileController;

/*
|--------------------------------------------------------------------------
| Profile Module — Web Routes
|--------------------------------------------------------------------------
*/

// ── Admin: Profile Pengguna ───────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/profile')
    ->name('admin.profile.')
    ->group(function () {
        Route::get('/',                 [ProfileController::class, 'index'])->name('index');
        Route::post('/update-info',     [ProfileController::class, 'updateInfo'])->name('updateInfo');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('updatePassword');
        Route::post('/check-password',  [ProfileController::class, 'checkPassword'])->name('checkPassword');
    });

// ── Admin: Profil Hotel ───────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/hotel')
    ->name('admin.hotel.')
    ->group(function () {
        Route::get('/',                      [ProfileHotelController::class, 'index'])->name('index');
        Route::post('/update',               [ProfileHotelController::class, 'update'])->name('update');
        Route::delete('/photos/{photo}',     [ProfileHotelController::class, 'deletePhoto'])->name('photos.delete');
    });

// ── Visitor: Profil Saya ──────────────────────────────
Route::middleware(['auth', 'role:visitor'])
    ->prefix('profil')
    ->name('visitor.profile.')
    ->group(function () {
        Route::get('/',             [VisitorProfileController::class, 'index'])->name('index');
        Route::put('/update-info',  [VisitorProfileController::class, 'updateInfo'])->name('update-info');
        Route::put('/update-password', [VisitorProfileController::class, 'updatePassword'])->name('update-password');
        Route::delete('/foto',      [VisitorProfileController::class, 'deleteFoto'])->name('delete-foto');
    });

