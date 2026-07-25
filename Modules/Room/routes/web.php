<?php

use Illuminate\Support\Facades\Route;
use Modules\Room\Http\Controllers\Admin\RoomController;

/*
|--------------------------------------------------------------------------
| Room Module — Web Routes
|--------------------------------------------------------------------------
| Semua route kamar masuk grup admin (auth + role:admin)
| Prefix: /admin/rooms
| Name prefix: admin.rooms.
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/rooms')
    ->name('admin.rooms.')
    ->group(function () {
        Route::get('/',              [RoomController::class, 'index'])->name('index');
        Route::get('/create',        [RoomController::class, 'create'])->name('create');
        Route::post('/',             [RoomController::class, 'store'])->name('store');
        Route::get('/{uuid}/edit',   [RoomController::class, 'edit'])->name('edit');
        Route::put('/{uuid}',        [RoomController::class, 'update'])->name('update');
        Route::delete('/{uuid}',     [RoomController::class, 'destroy'])->name('destroy');
    });
