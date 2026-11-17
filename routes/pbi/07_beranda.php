<?php

use App\Http\Controllers\Mahasiswa\BerandaController;
use Illuminate\Support\Facades\Route;

// PBI-07: Beranda dan panduan pelaporan (mahasiswa)
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
    Route::get('/panduan', [BerandaController::class, 'panduan'])->name('panduan');
});
