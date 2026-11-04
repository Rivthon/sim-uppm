<?php

namespace Database\Factories;

use App\Models\RoadmapMilestone;
use App\Models\StrategicPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoadmapMilestone>
 */
class RoadmapMilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'strategic_plan_id' => StrategicPlan::factory(),
            'start_year' => now()->year,
            'end_year' => now()->year + 1,
            'theme' => fake()->sentence(4),
            'focus' => fake()->paragraph(2),
            'indicators' => fake()->paragraph(),
            'target_outputs' => fake()->sentence(),
            'sort_order' => 1,
            'created_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
