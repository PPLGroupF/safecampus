<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'nama_unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Unit yang masih dipakai akun pengguna aktif tidak boleh dinonaktifkan.
     * Relasi users() ditambahkan bersama kolom users.unit_id (PBI-04).
     */
    public function hasActiveUsers(): bool
    {
        if (! method_exists($this, 'users')) {
            return false;
        }

        return $this->users()->where('is_active', true)->exists();
    }

    /**
     * Label lengkap untuk pilihan formulir, contoh: "Fakultas Rekayasa Industri (Telkom University Bandung)".
     */
    public function labelLengkap(): string
    {
        return $this->nama_unit.' ('.$this->campus?->nama_kampus.')';
    }
}
