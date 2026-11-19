<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// PBI-05: Kelola profil (semua peran)
Route::middleware(['auth'])
    ->prefix('profil')
    ->name('profil.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/kata-sandi', [ProfileController::class, 'updatePassword'])->name('password');
    });
