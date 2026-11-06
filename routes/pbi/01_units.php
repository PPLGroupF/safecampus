<?php

use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Route;

// PBI-01: Kelola data unit/fakultas (admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/unit')
    ->name('admin.units.')
    ->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::get('/tambah', [UnitController::class, 'create'])->name('create');
        Route::post('/', [UnitController::class, 'store'])->name('store');
        Route::get('/{unit}/ubah', [UnitController::class, 'edit'])->name('edit');
        Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
        Route::patch('/{unit}/status', [UnitController::class, 'toggle'])->name('toggle');
    });
