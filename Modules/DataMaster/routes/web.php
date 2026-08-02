<?php

use Illuminate\Support\Facades\Route;
use Modules\DataMaster\Http\Controllers\DataMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('datamasters', DataMasterController::class)->names('datamaster');
});
