<?php

use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

// PBI-10: Audit log (admin)
Route::middleware(['auth'])
    ->prefix('admin/audit-log')
    ->name('admin.audit-logs.')
    ->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
    });
