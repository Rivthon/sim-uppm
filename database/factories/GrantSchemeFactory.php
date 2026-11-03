<?php

namespace Database\Factories;

use App\Models\GrantScheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GrantScheme>
 */
class GrantSchemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('HB-###'),
            'name' => fake()->unique()->words(3, true),
            'type' => fake()->randomElement(['penelitian', 'pkm']),
            'description' => fake()->sentence(),
            'maximum_funding' => 15000000,
            'duration_months' => 12,
            'is_active' => true,
        ];
    }
}
