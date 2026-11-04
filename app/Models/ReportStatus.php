<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perubahan status laporan (timeline).
 */
class ReportStatus extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'report_id',
        'diubah_oleh',
        'status_baru',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function statusLabel(): string
    {
        return Report::STATUSES[$this->status_baru]['label'] ?? $this->status_baru;
    }

    public function statusColor(): string
    {
        return Report::STATUSES[$this->status_baru]['color'] ?? 'gray';
    }
}
