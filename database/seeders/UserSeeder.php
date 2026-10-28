<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun dummy untuk setiap peran. Kata sandi semua akun: password
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['nama' => 'Admin SafeCampus', 'email' => 'admin@safecampus.test', 'role' => User::ROLE_ADMIN],
            ['nama' => 'Petugas Satgas', 'email' => 'petugas@safecampus.test', 'role' => User::ROLE_PETUGAS],
            ['nama' => 'Mahasiswa Pelapor', 'email' => 'mahasiswa@safecampus.test', 'role' => User::ROLE_MAHASISWA],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['password' => 'password', 'email_verified_at' => now()],
            );
        }
    }
}
