<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Pemicu notifikasi terkait laporan. Semua fitur memakai kelas ini supaya isi pesan seragam
 * dan tidak pernah memuat kronologi, catatan, atau identitas pelapor (cukup kode tiket).
 *
 * Contoh:
 *   NotifikasiLaporan::tanggapanBaru($report);
 *   NotifikasiLaporan::jadwal($report, 'dibuat');
 */
class NotifikasiLaporan
{
    /** Perubahan status oleh petugas → pelapor. Dipanggil otomatis saat riwayat status baru dibuat. */
    public static function statusBerubah(ReportStatus $status): void
    {
        // Perubahan otomatis oleh sistem (misalnya Diajukan ke Dalam Verifikasi) dan status awal tidak dikirim.
        if ($status->diubah_oleh === null || $status->status_baru === Report::STATUS_DIAJUKAN) {
            return;
        }

        $report = $status->report;

        Notification::kirim(
            $report->user_id,
            'Status laporan diperbarui',
            'Laporan '.$report->kode_tiket.' kini berstatus '.$status->statusLabel().'.',
            route('laporan.show', $report, false),
        );
    }

    /** Laporan ditugaskan ke petugas → petugas tersebut. */
    public static function penugasan(Report $report, User $petugas): void
    {
        Notification::kirim(
            $petugas,
            'Penugasan laporan baru',
            'Anda ditugaskan menangani laporan '.$report->kode_tiket.'.',
            route('petugas.laporan.show', $report, false),
        );
    }

    /** Pelapor mengirim tanggapan → petugas penanggung jawab (atau semua petugas jika belum ditugaskan). */
    public static function tanggapanBaru(Report $report): void
    {
        foreach (static::petugasUntuk($report) as $petugas) {
            Notification::kirim(
                $petugas,
                'Tanggapan baru dari pelapor',
                'Pelapor menambahkan tanggapan pada laporan '.$report->kode_tiket.'.',
                route('petugas.laporan.show', $report, false),
            );
        }
    }

    /** Petugas membalas tanggapan → pelapor. */
    public static function balasanBaru(Report $report): void
    {
        Notification::kirim(
            $report->user_id,
            'Balasan dari petugas',
            'Petugas Satgas membalas tanggapan Anda pada laporan '.$report->kode_tiket.'.',
            route('laporan.show', $report, false),
        );
    }

    /**
     * Jadwal pendampingan.
     * $aksi: dibuat, diubah, dibatalkan → ke pelapor; dikonfirmasi → ke petugas pembuat jadwal.
     */
    public static function jadwal(Report $report, string $aksi, ?User $petugas = null): void
    {
        if ($aksi === 'dikonfirmasi') {
            if ($petugas) {
                Notification::kirim(
                    $petugas,
                    'Jadwal dikonfirmasi pelapor',
                    'Pelapor mengonfirmasi jadwal pendampingan laporan '.$report->kode_tiket.'.',
                    route('petugas.laporan.show', $report, false),
                );
            }

            return;
        }

        $judul = [
            'dibuat' => 'Jadwal pendampingan baru',
            'diubah' => 'Jadwal pendampingan diubah',
            'dibatalkan' => 'Jadwal pendampingan dibatalkan',
        ][$aksi] ?? 'Jadwal pendampingan';

        Notification::kirim(
            $report->user_id,
            $judul,
            'Ada pembaruan jadwal pendampingan untuk laporan '.$report->kode_tiket.'. Buka laporan untuk melihat detailnya.',
            route('laporan.show', $report, false),
        );
    }

    /**
     * Penerima notifikasi di pihak Satgas: petugas penanggung jawab jika sudah ada,
     * jika belum, semua petugas aktif.
     *
     * @return Collection<int, User>
     */
    public static function petugasUntuk(Report $report): Collection
    {
        if ($report->petugas_id && $report->petugas?->is_active) {
            return collect([$report->petugas]);
        }

        return User::query()->active()->where('role', User::ROLE_PETUGAS)->get();
    }
}
