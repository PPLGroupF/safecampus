<?php

use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Route;

// SAF-443: Tanggapan pelapor pada laporannya
Route::middleware(['auth', 'role:mahasiswa'])
    ->name('tanggapan.')
    ->group(function () {
        Route::get('/laporan/{report}/tanggapan', [ResponseController::class, 'index'])->name('index');
        Route::post('/laporan/{report}/tanggapan', [ResponseController::class, 'store'])->name('store');
        Route::put('/tanggapan/{response}', [ResponseController::class, 'update'])->name('update');
        Route::delete('/tanggapan/{response}', [ResponseController::class, 'destroy'])->name('destroy');
    });
