<?php

namespace App\Support;

use App\Models\Report;
use Illuminate\Support\Str;

/**
 * Pembuat kode tiket unik, contoh: SC-20261028-K7Q2M
 */
class TicketCode
{
    public static function generate(): string
    {
        do {
            // Hanya huruf besar dan angka yang mudah dibaca (tanpa 0/O dan 1/I).
            $random = collect(range(1, 5))
                ->map(fn () => Str::of('ABCDEFGHJKLMNPQRSTUVWXYZ23456789')->charAt(random_int(0, 31)))
                ->implode('');

            $code = 'SC-'.now()->format('Ymd').'-'.$random;
        } while (Report::where('kode_tiket', $code)->exists());

        return $code;
    }
}
