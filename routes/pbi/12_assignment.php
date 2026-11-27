<?php

use App\Http\Controllers\Petugas\VerificationController;
use Illuminate\Support\Facades\Route;

// SAF-410: Penugasan laporan ke petugas Satgas
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas/laporan')
    ->name('petugas.laporan.')
    ->group(function () {
        Route::post('/{report}/penugasan', [VerificationController::class, 'assign'])->name('assign');
        Route::patch('/{report}/penugasan', [VerificationController::class, 'reassign'])->name('reassign');
        Route::delete('/{report}/penugasan', [VerificationController::class, 'unassign'])->name('unassign');
    });
