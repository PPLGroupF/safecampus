<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bukti pendukung laporan (foto atau dokumen PDF).
 */
class ReportFile extends Model
{
    /** Jenis file yang diizinkan. */
    public const MIMES = ['jpg', 'jpeg', 'png', 'pdf'];

    /** Ukuran maksimal per file dalam kilobyte (5 MB). */
    public const MAX_KB = 5120;

    /** Jumlah maksimal bukti untuk satu laporan. */
    public const MAX_FILES = 5;

    protected $fillable = [
        'report_id',
        'uploaded_by',
        'nama_file',
        'path',
        'mime',
        'ukuran',
    ];

    protected function casts(): array
    {
        return [
            'nama_file' => 'encrypted',
            'ukuran' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeForReport(Builder $query, Report $report): Builder
    {
        return $query->where('report_id', $report->id)->oldest()->oldest('id');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }

    public function sizeLabel(): string
    {
        return $this->ukuran >= 1048576
            ? number_format($this->ukuran / 1048576, 1, ',', '.').' MB'
            : max(1, (int) round($this->ukuran / 1024)).' KB';
    }
}
