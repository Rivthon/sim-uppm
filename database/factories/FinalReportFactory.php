<?php

namespace Database\Factories;

use App\Models\FinalReport;
use App\Models\FundingContract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinalReport>
 */
class FinalReportFactory extends Factory
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
            'summary' => fake()->paragraph(3),
            'conclusions' => fake()->paragraph(2),
            'recommendations' => fake()->paragraph(),
            'budget_realized' => 9000000,
            'document_path' => 'final-reports/report.pdf',
            'status' => 'submitted',
            'verification_notes' => null,
            'submitted_at' => now(),
            'submitted_by' => User::factory()->state(['roles' => ['dosen']]),
            'verified_by' => null,
            'verified_at' => null,
        ];
    }
}
