<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\Admin\ProfileController;
use Modules\Profile\Http\Controllers\Admin\ProfileHotelController;

/*
|--------------------------------------------------------------------------
| Profile Module — Web Routes
|--------------------------------------------------------------------------
| Admin profile routes
| Prefix  : /admin/profile
| Name    : admin.profile.
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/profile')
    ->name('admin.profile.')
    ->group(function () {
        Route::get('/',                    [ProfileController::class, 'index'])->name('index');
        Route::post('/update-info',        [ProfileController::class, 'updateInfo'])->name('updateInfo');
        Route::post('/update-password',    [ProfileController::class, 'updatePassword'])->name('updatePassword');
        Route::post('/check-password',     [ProfileController::class, 'checkPassword'])->name('checkPassword');
    });

/*
|--------------------------------------------------------------------------
| Profile Hotel routes
| Prefix  : /admin/hotel
| Name    : admin.hotel.
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/hotel')
    ->name('admin.hotel.')
    ->group(function () {
        Route::get('/',                       [ProfileHotelController::class, 'index'])->name('index');
        Route::post('/update',                [ProfileHotelController::class, 'update'])->name('update');
        Route::delete('/photos/{photo}',      [ProfileHotelController::class, 'deletePhoto'])->name('photos.delete');
    });
