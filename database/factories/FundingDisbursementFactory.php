<?php

namespace Database\Factories;

use App\Models\FundingContract;
use App\Models\FundingDisbursement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingDisbursement>
 */
class FundingDisbursementFactory extends Factory
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
            'sequence' => 1,
            'label' => 'Termin I',
            'percentage' => 70,
            'amount' => 7000000,
            'scheduled_at' => now(),
            'paid_at' => null,
            'status' => 'planned',
            'proof_path' => null,
            'notes' => null,
            'recorded_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
