<?php

use App\Http\Controllers\Mahasiswa\ReportController;
use Illuminate\Support\Facades\Route;

// PBI-06: Kelola laporan (mahasiswa)
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('laporan')
    ->name('laporan.')
    ->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/buat', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/ubah', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::patch('/{report}/batal', [ReportController::class, 'cancel'])->name('cancel');
    });
