<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Telkom University Bandung', 'Telkom University Jakarta', 'Telkom University Surabaya', 'Telkom University Purwokerto'] as $nama) {
            Campus::updateOrCreate(['nama_kampus' => $nama], ['is_active' => true]);
        }
    }
}
