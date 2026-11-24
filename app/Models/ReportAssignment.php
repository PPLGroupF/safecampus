<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat penugasan laporan ke petugas Satgas.
 * Hanya satu penugasan yang aktif untuk setiap laporan.
 */
class ReportAssignment extends Model
{
    protected $fillable = [
        'report_id',
        'petugas_id',
        'ditugaskan_oleh',
        'alasan',
        'aktif',
        'dicabut_at',
        'dicabut_oleh',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'dicabut_at' => 'datetime',
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

    public function penugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditugaskan_oleh');
    }

    public function pencabut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicabut_oleh');
    }

    public function isRevoked(): bool
    {
        return $this->dicabut_at !== null;
    }
}
