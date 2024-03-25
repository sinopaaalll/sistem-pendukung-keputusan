<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kriteria>
 */
class KriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->word(),
            'kriteria_name' => fake()->name(),
            'bobot' => fake()->randomFloat(1, 20, 50),
            'tipe' => fake()->randomElement(['benefit', 'cost']),
        ];
    }
}
