<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalDecision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProposalDecision>
 */
class ProposalDecisionFactory extends Factory
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
            'decision' => 'accepted',
            'approved_budget' => 10000000,
            'notes' => 'Ditetapkan berdasarkan hasil penilaian reviewer.',
            'decided_by' => User::factory()->state(['roles' => ['admin_uppm']]),
            'decided_at' => now(),
        ];
    }
}
