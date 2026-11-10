<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;

class RecordFailedLogin
{
    public function handle(Failed $event): void
    {
        // Email yang dicoba tidak disimpan agar tidak membocorkan data akun.
        AuditLogger::record('Percobaan login gagal', 'autentikasi', status: 'gagal', user: $event->user);
    }
}
