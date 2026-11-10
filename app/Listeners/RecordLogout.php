<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Logout;

class RecordLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            AuditLogger::record('Logout dari sistem', 'autentikasi', user: $event->user);
        }
    }
}
