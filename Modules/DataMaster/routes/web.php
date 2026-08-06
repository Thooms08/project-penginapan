<?php

use Illuminate\Support\Facades\Route;
use Modules\DataMaster\Http\Controllers\DataMasterController;
use Modules\DataMaster\Http\Controllers\Admin\VisitorDataController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('datamasters', DataMasterController::class)->names('datamaster');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/visitor-data', [VisitorDataController::class, 'index'])
            ->name('visitor-data.index');
    });
