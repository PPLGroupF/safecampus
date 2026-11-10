<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Layanan pencatatan audit log yang dipanggil dari fitur lain.
 *
 * Contoh:
 *   AuditLogger::record('Menambah kampus Telkom University Bandung', 'kampus');
 *   AuditLogger::record('Mengajukan laporan', 'laporan', $report->kode_tiket, anonymous: true);
 *
 * Jangan pernah mengirim isi kronologi, catatan, atau data pribadi ke dalam teks aksi.
 */
class AuditLogger
{
    public static function record(
        string $aksi,
        string $modul,
        ?string $kodeTiket = null,
        string $status = 'berhasil',
        ?User $user = null,
        bool $anonymous = false,
    ): ?AuditLog {
        $user ??= Auth::user();

        try {
            return AuditLog::create([
                // Aksi pelapor anonim dicatat tanpa user_id agar identitasnya tidak terungkap.
                'user_id' => $anonymous ? null : $user?->id,
                'peran' => $user?->role,
                'aksi' => mb_substr($aksi, 0, 100),
                'modul' => $modul,
                'kode_tiket' => $kodeTiket,
                'status' => $status,
            ]);
        } catch (Throwable $e) {
            // Kegagalan mencatat log tidak boleh menggagalkan aksi utama pengguna.
            report($e);

            return null;
        }
    }
}
