<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun dummy untuk setiap peran. Kata sandi semua akun: password
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $fri = Unit::firstWhere('nama_unit', 'Fakultas Rekayasa Industri');
        $fif = Unit::firstWhere('nama_unit', 'Fakultas Informatika');
        $feb = Unit::firstWhere('nama_unit', 'Fakultas Ekonomi dan Bisnis');
        $ditmawa = Unit::firstWhere('nama_unit', 'Direktorat Kemahasiswaan');

        $accounts = [
            ['nama' => 'Admin SafeCampus', 'email' => 'admin@safecampus.test', 'role' => User::ROLE_ADMIN, 'unit_id' => $ditmawa?->id],

            ['nama' => 'Petugas Satgas', 'email' => 'petugas@safecampus.test', 'role' => User::ROLE_PETUGAS, 'unit_id' => $ditmawa?->id],
            ['nama' => 'Dewi Nuraini', 'email' => 'dewi.petugas@safecampus.test', 'role' => User::ROLE_PETUGAS, 'unit_id' => $ditmawa?->id],
            ['nama' => 'Rizky Hardian', 'email' => 'rizky.petugas@safecampus.test', 'role' => User::ROLE_PETUGAS, 'unit_id' => $ditmawa?->id],

            ['nama' => 'Mahasiswa Pelapor', 'email' => 'mahasiswa@safecampus.test', 'nim' => '1202240001', 'role' => User::ROLE_MAHASISWA, 'unit_id' => $fri?->id],
            ['nama' => 'Aisyah Rahmawati', 'email' => 'aisyah@safecampus.test', 'nim' => '1202240002', 'role' => User::ROLE_MAHASISWA, 'unit_id' => $fif?->id],
            ['nama' => 'Fahri Akbar', 'email' => 'fahri@safecampus.test', 'nim' => '1202240003', 'role' => User::ROLE_MAHASISWA, 'unit_id' => $feb?->id],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['password' => 'password', 'email_verified_at' => now(), 'is_active' => true],
            );
        }
    }
}
