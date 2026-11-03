<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
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
     * Pilihan kategori pada formulir laporan: hanya kategori aktif.
     * Saat laporan diubah, kategori yang sedang dipakai laporan tetap ditampilkan.
     */
    public static function optionsForReport(?int $currentId = null)
    {
        return static::query()
            ->where(function (Builder $query) use ($currentId) {
                $query->where('is_active', true);
                if ($currentId) {
                    $query->orWhere('id', $currentId);
                }
            })
            ->orderBy('nama_kategori')
            ->get();
    }
}
