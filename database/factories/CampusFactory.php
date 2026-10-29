<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campus>
 */
class CampusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kampus' => 'Telkom University '.fake()->unique()->city(),
            'is_active' => true,
        ];
    }
}
