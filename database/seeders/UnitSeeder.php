<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Telkom University Bandung' => [
                'Fakultas Teknik Elektro',
                'Fakultas Rekayasa Industri',
                'Fakultas Informatika',
                'Fakultas Ekonomi dan Bisnis',
                'Fakultas Komunikasi dan Ilmu Sosial',
                'Fakultas Industri Kreatif',
                'Fakultas Ilmu Terapan',
                'Direktorat Kemahasiswaan',
            ],
            'Telkom University Jakarta' => ['Fakultas Teknik dan Informatika', 'Fakultas Bisnis dan Komunikasi'],
            'Telkom University Surabaya' => ['Fakultas Teknik Elektro', 'Fakultas Informatika'],
            'Telkom University Purwokerto' => ['Fakultas Teknik Telekomunikasi dan Elektro', 'Fakultas Informatika'],
        ];

        foreach ($data as $namaKampus => $units) {
            $campus = Campus::firstWhere('nama_kampus', $namaKampus);

            if (! $campus) {
                continue;
            }

            foreach ($units as $namaUnit) {
                Unit::updateOrCreate(['campus_id' => $campus->id, 'nama_unit' => $namaUnit], ['is_active' => true]);
            }
        }
    }
}
