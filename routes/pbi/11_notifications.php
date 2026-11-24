<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// SAF-455: Notifikasi dalam aplikasi (semua peran)
Route::middleware(['auth'])
    ->prefix('notifikasi')
    ->name('notifikasi.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/baca', [NotificationController::class, 'read'])->name('read');
        Route::post('/baca-semua', [NotificationController::class, 'readAll'])->name('read-all');
    });
