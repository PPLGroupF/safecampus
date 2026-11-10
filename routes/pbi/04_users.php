<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// PBI-04: Kelola akun pengguna (admin)
Route::middleware(['auth'])
    ->prefix('admin/pengguna')
    ->name('admin.users.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/tambah', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/ubah', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/status', [UserController::class, 'toggle'])->name('toggle');
    });
