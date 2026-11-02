<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Catatan aktivitas. Hanya boleh ditambah, tidak boleh diubah atau dihapus.
 * Tidak menyimpan isi laporan, kronologi, atau data sensitif lain.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    public const MODULES = [
        'autentikasi' => 'Autentikasi',
        'kampus' => 'Kampus',
        'unit' => 'Unit / Fakultas',
        'kategori' => 'Kategori',
        'pengguna' => 'Pengguna',
        'profil' => 'Profil',
        'laporan' => 'Laporan',
        'verifikasi' => 'Verifikasi & Status',
        'penugasan' => 'Penugasan',
        'catatan_internal' => 'Catatan Internal',
        'pendampingan' => 'Pendampingan',
        'bukti' => 'Bukti Laporan',
        'tanggapan' => 'Tanggapan',
        'rekap' => 'Rekap & Ekspor',
    ];

    protected $fillable = [
        'user_id',
        'peran',
        'aksi',
        'modul',
        'kode_tiket',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit log tidak dapat diubah.'));
        static::deleting(fn () => throw new LogicException('Audit log tidak dapat dihapus.'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modulLabel(): string
    {
        return self::MODULES[$this->modul] ?? $this->modul;
    }
}
