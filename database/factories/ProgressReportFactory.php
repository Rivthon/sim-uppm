<?php

namespace Database\Factories;

use App\Models\FundingContract;
use App\Models\ProgressReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgressReport>
 */
class ProgressReportFactory extends Factory
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
            'reporting_period' => now()->startOfMonth(),
            'progress_percentage' => 50,
            'summary' => fake()->paragraph(3),
            'achievements' => fake()->paragraph(2),
            'obstacles' => fake()->sentence(),
            'budget_realized' => 5000000,
            'output_progress' => fake()->paragraph(2),
            'document_path' => 'progress-reports/report.pdf',
            'status' => 'submitted',
            'submitted_at' => now(),
            'created_by' => User::factory()->state(['roles' => ['dosen']]),
        ];
    }
}
