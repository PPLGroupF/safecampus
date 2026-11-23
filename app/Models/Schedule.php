<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jadwal pendampingan untuk pelapor (konseling, pendampingan psikologis/hukum, dsb.).
 */
class Schedule extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIKONFIRMASI = 'dikonfirmasi';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUSES = [
        self::STATUS_MENUNGGU => ['label' => 'Menunggu Konfirmasi', 'color' => 'amber'],
        self::STATUS_DIKONFIRMASI => ['label' => 'Dikonfirmasi', 'color' => 'green'],
        self::STATUS_DIBATALKAN => ['label' => 'Dibatalkan', 'color' => 'slate'],
    ];

    public const JENIS = [
        'konseling' => 'Konseling',
        'pendampingan_psikologis' => 'Pendampingan Psikologis',
        'pendampingan_hukum' => 'Pendampingan Hukum',
        'pemeriksaan' => 'Pemeriksaan / Klarifikasi',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'report_id',
        'petugas_id',
        'jenis',
        'waktu',
        'lokasi',
        'catatan',
        'status',
        'dikonfirmasi_at',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
            'dikonfirmasi_at' => 'datetime',
            'catatan' => 'encrypted',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /** Jadwal milik sebuah laporan, urut dari yang paling dekat. */
    public function scopeForReport(Builder $query, Report $report): Builder
    {
        return $query->where('report_id', $report->id)->orderBy('waktu');
    }

    public function jenisLabel(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'gray';
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    /** Pelapor dapat mengonfirmasi jadwal yang masih menunggu dan belum lewat. */
    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_MENUNGGU && $this->waktu->isFuture();
    }
}
