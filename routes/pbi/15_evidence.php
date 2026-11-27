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

// Unggah, ganti, dan hapus bukti hanya oleh pelapor pemilik laporan.
Route::middleware(['auth', 'role:mahasiswa'])
    ->name('bukti.')
    ->group(function () {
        Route::post('/laporan/{report}/bukti', [ReportFileController::class, 'store'])->name('store');
        Route::put('/bukti/{file}', [ReportFileController::class, 'update'])->name('update');
        Route::delete('/bukti/{file}', [ReportFileController::class, 'destroy'])->name('destroy');
    });
