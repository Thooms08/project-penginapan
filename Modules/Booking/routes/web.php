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
        // Check-in / Check-out schedule
        Route::get('/check',              [CheckController::class, 'index'])->name('check.index');
        Route::post('/check',             [CheckController::class, 'store'])->name('check.store');
        Route::delete('/check/{check}',   [CheckController::class, 'destroy'])->name('check.destroy');
        Route::get('/check/today',        [CheckController::class, 'today'])->name('check.today');
        Route::get('/check/all',          [CheckController::class, 'all'])->name('check.all');

        // Surcharge settings (early check-in / late check-out fee)
        Route::post('/surcharge',                        [CheckController::class, 'storeSurcharge'])->name('surcharge.store');
        Route::put('/surcharge/{surcharge}',             [CheckController::class, 'updateSurcharge'])->name('surcharge.update');
        Route::patch('/surcharge/{surcharge}/toggle',    [CheckController::class, 'toggleSurcharge'])->name('surcharge.toggle');
        Route::delete('/surcharge/{surcharge}',          [CheckController::class, 'destroySurcharge'])->name('surcharge.destroy');
    });

