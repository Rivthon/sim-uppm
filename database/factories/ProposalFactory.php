<?php

namespace Database\Factories;

use App\Models\GrantPeriod;
use App\Models\Proposal;
use App\Models\ResearchField;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grant_period_id' => GrantPeriod::factory(),
            'research_field_id' => ResearchField::factory(),
            'lead_id' => User::factory(),
            'code' => null,
            'title' => fake()->sentence(8),
            'type' => 'penelitian',
            'abstract' => fake()->paragraph(3),
            'background' => fake()->paragraph(4),
            'objectives' => fake()->paragraph(2),
            'methodology' => fake()->paragraph(3),
            'roadmap_alignment' => fake()->paragraph(2),
            'partner_name' => null,
            'partner_location' => null,
            'requested_budget' => 10000000,
            'duration_months' => 12,
            'document_path' => null,
            'status' => 'draft',
            'submitted_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'code' => 'UPPM-2027-0001',
            'status' => 'submitted',
            'submitted_at' => now(),
            'document_path' => 'proposals/example.pdf',
        ]);
    }
}
