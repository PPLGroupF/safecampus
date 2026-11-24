<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tanggapan pelapor pada laporan, dan balasan petugas (parent_id terisi).
 */
class Response extends Model
{
    /** Status laporan yang masih menerima tanggapan pelapor. */
    public const OPEN_STATUSES = [
        Report::STATUS_DALAM_VERIFIKASI,
        Report::STATUS_PERLU_INFORMASI,
        Report::STATUS_DALAM_PENANGANAN,
    ];

    protected $fillable = [
        'report_id',
        'user_id',
        'parent_id',
        'pesan',
        'dibaca_at',
    ];

    protected function casts(): array
    {
        return [
            // Isi tanggapan terenkripsi di basis data (NFR-01).
            'pesan' => 'encrypted',
            'dibaca_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Balasan petugas untuk tanggapan ini. */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->oldest()->oldest('id');
    }

    /** Tanggapan pelapor (bukan balasan) pada sebuah laporan, urut dari yang terlama. */
    public function scopeForReport(Builder $query, Report $report): Builder
    {
        return $query->where('report_id', $report->id)->whereNull('parent_id')->oldest()->oldest('id');
    }

    public static function isOpenFor(Report $report): bool
    {
        return in_array($report->status, self::OPEN_STATUSES, true);
    }

    public function isRead(): bool
    {
        return $this->dibaca_at !== null;
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }
}
