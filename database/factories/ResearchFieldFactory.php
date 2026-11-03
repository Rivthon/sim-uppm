<?php

namespace Database\Factories;

use App\Models\ResearchField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchField>
 */
class ResearchFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('RI-###'),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
