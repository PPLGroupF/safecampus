<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notifikasi dalam aplikasi (ikon lonceng).
 *
 * Contoh:
 *   Notification::kirim($user, 'Status laporan diperbarui', 'Laporan SC-... kini Dalam Penanganan.', route('laporan.show', $report));
 *
 * Jangan menulis kronologi, catatan, atau identitas pelapor di judul maupun pesan.
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'url',
        'dibaca_at',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_at' => 'datetime',
        ];
    }

    public static function kirim(User|int $user, string $judul, string $pesan, ?string $url = null): self
    {
        return static::create([
            'user_id' => $user instanceof User ? $user->id : $user,
            'judul' => mb_substr($judul, 0, 100),
            'pesan' => mb_substr($pesan, 0, 255),
            'url' => $url,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('dibaca_at');
    }

    public function isRead(): bool
    {
        return $this->dibaca_at !== null;
    }

    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['dibaca_at' => now()]);
        }
    }
}
