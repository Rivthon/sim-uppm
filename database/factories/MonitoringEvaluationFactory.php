<?php

namespace Database\Factories;

use App\Models\FundingContract;
use App\Models\MonitoringEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitoringEvaluation>
 */
class MonitoringEvaluationFactory extends Factory
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
            'progress_report_id' => null,
            'evaluator_id' => User::factory()->state(['roles' => ['reviewer']]),
            'scheduled_at' => now()->addWeek(),
            'mode' => 'online',
            'score' => null,
            'findings' => null,
            'recommendations' => null,
            'follow_up' => null,
            'status' => 'scheduled',
            'completed_at' => null,
            'created_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
