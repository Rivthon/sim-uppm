<?php

namespace Database\Factories;

use App\Models\StrategicPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StrategicPlan>
 */
class StrategicPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Renstra Penelitian '.now()->year.'-'.(now()->year + 4),
            'scope' => 'institution',
            'study_program_id' => null,
            'type' => 'penelitian',
            'start_year' => now()->year,
            'end_year' => now()->year + 4,
            'vision' => fake()->paragraph(2),
            'objectives' => fake()->paragraph(3),
            'document_path' => null,
            'status' => 'active',
            'created_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
