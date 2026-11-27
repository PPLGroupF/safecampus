<?php

use App\Http\Controllers\ReportFileController;
use Illuminate\Support\Facades\Route;

// SAF-436: Bukti laporan (file disimpan privat)
Route::middleware(['auth'])
    ->prefix('bukti')
    ->name('bukti.')
    ->group(function () {
        Route::get('/{file}', [ReportFileController::class, 'show'])->name('show');
    });
