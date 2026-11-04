<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'kode_tiket' => 'SC-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
            'kronologi' => 'Data dummy: '.fake()->paragraph(),
            'mode_identitas' => 'teridentifikasi',
            'terlapor_peran' => 'mahasiswa',
            'terlapor_keterangan' => 'Data dummy: teman satu kelas.',
            'status' => 'diajukan',
        ];
    }

    public function anonim(): static
    {
        return $this->state(fn () => ['mode_identitas' => 'anonim']);
    }

    public function status(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
