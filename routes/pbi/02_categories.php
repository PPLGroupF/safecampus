<?php

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

// PBI-02: Kelola kategori kekerasan (admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/kategori')
    ->name('admin.categories.')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/tambah', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/ubah', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::patch('/{category}/status', [CategoryController::class, 'toggle'])->name('toggle');
    });