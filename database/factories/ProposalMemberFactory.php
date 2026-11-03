<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProposalMember>
 */
class ProposalMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'name' => fake()->name(),
            'identity_number' => fake()->numerify('##########'),
            'member_type' => fake()->randomElement(['dosen', 'mahasiswa']),
            'study_program' => 'Sistem Informasi',
            'sort_order' => 0,
        ];
    }
}
