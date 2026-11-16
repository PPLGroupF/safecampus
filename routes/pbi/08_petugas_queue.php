<?php

use App\Http\Controllers\Petugas\VerificationController;
use Illuminate\Support\Facades\Route;

// PBI-08: Antrean dan detail laporan (petugas)
// Hanya petugas Satgas. Admin dan mahasiswa ditolak (403) sehingga admin tidak bisa membuka isi laporan.
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/antrean', [VerificationController::class, 'queue'])->name('antrean');
        Route::get('/laporan/{report}', [VerificationController::class, 'show'])->name('laporan.show');
    });
