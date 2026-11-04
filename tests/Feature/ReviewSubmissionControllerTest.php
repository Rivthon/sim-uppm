<?php

namespace Tests\Feature;

use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewSubmissionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reviewer_can_submit_complete_review_and_it_becomes_locked(): void
    {
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $assignment = ReviewAssignment::factory()->for($reviewer, 'reviewer')->create([
            'status' => 'in_review',
            'relevance_score' => 18,
            'methodology_score' => 17,
            'feasibility_score' => 16,
            'budget_score' => 15,
            'output_score' => 18,
            'strengths' => 'Topik sangat relevan.',
            'weaknesses' => 'Jadwal perlu diperinci.',
            'comments' => 'Layak dengan perbaikan minor.',
            'recommendation' => 'revision',
        ]);

        $response = $this->actingAs($reviewer)->post(route('reviews.submissions.store', $assignment));

        $response->assertRedirect(route('reviews.show', $assignment));
        $this->assertSame('submitted', $assignment->fresh()->status);
        $this->actingAs($reviewer)->get(route('reviews.edit', $assignment))->assertForbidden();
    }

    public function test_reviewer_can_report_conflict_of_interest(): void
    {
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $assignment = ReviewAssignment::factory()->for($reviewer, 'reviewer')->create([
            'status' => 'in_review',
            'has_conflict' => true,
            'conflict_notes' => 'Ketua pengusul merupakan anggota tim riset aktif saya.',
        ]);

        $response = $this->actingAs($reviewer)->post(route('reviews.submissions.store', $assignment));

        $response->assertRedirect(route('reviews.show', $assignment));
        $assignment->refresh();
        $this->assertSame('conflict', $assignment->status);
        $this->assertNull($assignment->recommendation);
    }

    public function test_incomplete_review_is_not_submitted(): void
    {
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $assignment = ReviewAssignment::factory()->for($reviewer, 'reviewer')->create(['status' => 'in_review']);

        $response = $this->actingAs($reviewer)->from(route('reviews.show', $assignment))
            ->post(route('reviews.submissions.store', $assignment));

        $response->assertRedirect(route('reviews.show', $assignment))->assertSessionHasErrors('review');
        $this->assertSame('in_review', $assignment->fresh()->status);
    }
}
