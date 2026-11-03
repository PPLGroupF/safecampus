<?php

use App\Http\Controllers\Admin\CampusController;
use Illuminate\Support\Facades\Route;

// PBI-03: Kelola data kampus (admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/kampus')
    ->name('admin.campuses.')
    ->group(function () {
        Route::get('/', [CampusController::class, 'index'])->name('index');
        Route::get('/tambah', [CampusController::class, 'create'])->name('create');
        Route::post('/', [CampusController::class, 'store'])->name('store');
        Route::get('/{campus}/ubah', [CampusController::class, 'edit'])->name('edit');
        Route::put('/{campus}', [CampusController::class, 'update'])->name('update');
        Route::patch('/{campus}/status', [CampusController::class, 'toggle'])->name('toggle');
    });
