<?php

use App\Http\Controllers\Petugas\VerificationController;
use Illuminate\Support\Facades\Route;

// PBI-09: Verifikasi dan pembaruan status laporan (petugas)
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas/laporan')
    ->name('petugas.laporan.')
    ->group(function () {
        Route::post('/{report}/verifikasi', [VerificationController::class, 'verify'])->name('verify');
        Route::patch('/{report}/status', [VerificationController::class, 'updateStatus'])->name('status');
    });
