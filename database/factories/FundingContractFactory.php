<?php

namespace Database\Factories;

use App\Models\FundingContract;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingContract>
 */
class FundingContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory()->submitted(),
            'contract_number' => fake()->unique()->bothify('CTR/UPPM/####/2027'),
            'decree_number' => fake()->bothify('SK/UPPM/###/2027'),
            'signed_at' => now(),
            'starts_at' => now(),
            'ends_at' => now()->addMonths(10),
            'contract_value' => 10000000,
            'status' => 'active',
            'document_path' => null,
            'created_by' => User::factory()->state(['roles' => ['admin_uppm']]),
        ];
    }
}
