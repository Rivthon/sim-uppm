<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\FundingContract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'funding_contract_id' => FundingContract::factory(),
            'activity_date' => now(),
            'description' => fake()->paragraph(),
            'result' => fake()->sentence(),
            'progress_percentage' => 25,
            'expense_amount' => 1000000,
            'attachment_path' => null,
            'created_by' => User::factory()->state(['roles' => ['dosen']]),
        ];
    }
}
