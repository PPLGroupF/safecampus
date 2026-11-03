<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kampus',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Kampus yang masih memiliki unit/fakultas aktif tidak boleh dinonaktifkan.
     * Relasi units() ditambahkan bersama tabel units (PBI-01).
     */
    public function hasActiveUnits(): bool
    {
        if (! method_exists($this, 'units')) {
            return false;
        }

        return $this->units()->where('is_active', true)->exists();
    }
}
