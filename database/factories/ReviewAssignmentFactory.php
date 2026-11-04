<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewAssignment>
 */
class ReviewAssignmentFactory extends Factory
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
            'reviewer_id' => User::factory()->state(['roles' => ['reviewer']]),
            'assigned_by' => User::factory()->state(['roles' => ['admin_uppm']]),
            'due_at' => now()->addWeek(),
            'status' => 'assigned',
            'has_conflict' => false,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'status' => 'submitted',
            'relevance_score' => 18,
            'methodology_score' => 17,
            'feasibility_score' => 16,
            'budget_score' => 15,
            'output_score' => 18,
            'strengths' => 'Topik relevan dan memiliki dampak yang jelas.',
            'weaknesses' => 'Metodologi perlu diperinci.',
            'comments' => 'Layak didanai dengan perbaikan minor.',
            'recommendation' => 'revision',
            'submitted_at' => now(),
        ]);
    }
}
