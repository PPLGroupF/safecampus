<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Kategori mengacu pada bentuk kekerasan dalam Permendikbudristek No. 55 Tahun 2024.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kekerasan Fisik',
            'Kekerasan Psikis',
            'Perundungan',
            'Kekerasan Seksual',
            'Diskriminasi dan Intoleransi',
            'Kekerasan Berbasis Gender Online',
        ];

        foreach ($categories as $nama) {
            Category::updateOrCreate(['nama_kategori' => $nama], ['is_active' => true]);
        }
    }
}
