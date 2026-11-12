<?php

use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

// PBI-10: Audit log (admin)
// Hanya-baca: sengaja tidak ada route untuk mengubah atau menghapus log.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/audit-log')
    ->name('admin.audit-logs.')
    ->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
    });
