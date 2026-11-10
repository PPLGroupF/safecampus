<?php

use App\Http\Controllers\Mahasiswa\ReportController;
use Illuminate\Support\Facades\Route;

// PBI-06: Kelola laporan (mahasiswa)
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('laporan')
    ->name('laporan.')
    ->group(function () {
        Route::get('/buat', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
    });
