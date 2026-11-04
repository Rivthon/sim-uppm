<?php

namespace Database\Factories;

use App\Models\KpiTarget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KpiTarget>
 */
class KpiTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => now()->year,
            'metric' => fake()->unique()->randomElement(['funded_activities', 'verified_outputs', 'verified_final_reports', 'paid_funding']),
            'target_value' => 10,
            'unit' => 'kegiatan',
            'created_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
