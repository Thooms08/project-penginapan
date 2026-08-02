<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;
use Modules\Booking\Http\Controllers\Admin\CheckController;
use Modules\Booking\Http\Controllers\Admin\BookingAdminController;

/*
|--------------------------------------------------------------------------
| Booking Module — Web Routes
|--------------------------------------------------------------------------
*/

// ── Visitor: Booking flow ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Show booking form for a specific room (by UUID)
    Route::get('/booking/room/{uuid}', [BookingController::class, 'create'])
        ->name('booking.create');

    // Store booking + return Midtrans Snap token (AJAX/JSON)
    Route::post('/booking/room/{uuid}', [BookingController::class, 'store'])
        ->name('booking.store');

    // Booking history (2-tab: success / failed)
    Route::get('/booking/history', [BookingController::class, 'history'])
        ->name('booking.history');

    // Midtrans finish redirect (called after payment)
    Route::get('/booking/success', [BookingController::class, 'success'])
        ->name('booking.success');

    // AJAX: verify payment status (client-side fallback after Snap onSuccess)
    Route::post('/booking/verify-payment', [BookingController::class, 'verifyPayment'])
        ->name('booking.verify-payment');

    // AJAX: check room availability for date range
    Route::get('/booking/check-availability', [BookingController::class, 'checkAvailability'])
        ->name('booking.check-availability');

    // AJAX: get blocked date ranges for a room
    Route::get('/booking/blocked-dates/{roomId}', [BookingController::class, 'blockedDates'])
        ->name('booking.blocked-dates');
});

// ── Admin: Check In & Out ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Admin: Booking list & confirmation ────────────────
        Route::get('/bookings',                   [BookingAdminController::class, 'index'])  ->name('bookings.index');
        Route::post('/bookings/{id}/confirm',     [BookingAdminController::class, 'confirm'])->name('bookings.confirm');
        Route::get('/bookings/{id}/detail',       [BookingAdminController::class, 'detail']) ->name('bookings.detail');

        // Check-in / Check-out schedule
        Route::get('/check',            [CheckController::class, 'index'])->name('check.index');
        Route::post('/check',           [CheckController::class, 'store'])->name('check.store');
        Route::delete('/check/{check}', [CheckController::class, 'destroy'])->name('check.destroy');
        Route::get('/check/today',      [CheckController::class, 'today'])->name('check.today');
        Route::get('/check/all',        [CheckController::class, 'all'])->name('check.all');
        Route::post('/check/checkout/{booking}', [CheckController::class, 'checkOut'])->name('check.checkout');

        // Surcharge settings (early check-in / late check-out fee)
        Route::post('/surcharge',                     [CheckController::class, 'storeSurcharge'])->name('surcharge.store');
        Route::put('/surcharge/{surcharge}',          [CheckController::class, 'updateSurcharge'])->name('surcharge.update');
        Route::patch('/surcharge/{surcharge}/toggle', [CheckController::class, 'toggleSurcharge'])->name('surcharge.toggle');
        Route::delete('/surcharge/{surcharge}',       [CheckController::class, 'destroySurcharge'])->name('surcharge.destroy');
    });
