<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// SAF-424: Jadwal pendampingan
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.jadwal.')
    ->group(function () {
        Route::post('/laporan/{report}/jadwal', [ScheduleController::class, 'store'])->name('store');
        Route::get('/jadwal/{schedule}', [ScheduleController::class, 'show'])->name('show');
        Route::put('/jadwal/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/jadwal/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('laporan')
    ->name('laporan.jadwal.')
    ->group(function () {
        Route::patch('/jadwal/{schedule}/konfirmasi', [ScheduleController::class, 'konfirmasi'])->name('konfirmasi');
    });
