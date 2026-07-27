<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;
use Modules\Booking\Http\Controllers\Admin\CheckController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('bookings', BookingController::class)->names('booking');
});

// ── Admin: Check In & Out ──────────────────────────────
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/check', [CheckController::class, 'index'])->name('check.index');
        Route::post('/check', [CheckController::class, 'store'])->name('check.store');
        Route::delete('/check/{check}', [CheckController::class, 'destroy'])->name('check.destroy');
        Route::get('/check/today', [CheckController::class, 'today'])->name('check.today');
        Route::get('/check/all', [CheckController::class, 'all'])->name('check.all');
    });

