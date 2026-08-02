<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\Api\MidtransController;

/*
|--------------------------------------------------------------------------
| Booking Module — API Routes
|--------------------------------------------------------------------------
*/

// ── Midtrans webhook — NO auth, NO CSRF (called by Midtrans server) ────────
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->name('midtrans.callback');
