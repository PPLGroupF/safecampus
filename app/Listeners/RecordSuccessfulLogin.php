<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Login;

class RecordSuccessfulLogin
{
    public function handle(Login $event): void
    {
        AuditLogger::record('Login ke sistem', 'autentikasi', user: $event->user);
    }
}
