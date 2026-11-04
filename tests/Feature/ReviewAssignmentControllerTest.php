<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewAssignmentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_assign_reviewer_to_submitted_proposal(): void
    {
        $this->travelTo('2027-03-01 08:00:00');
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $reviewer = User::factory()->create(['roles' => ['dosen', 'reviewer']]);
        $proposal = Proposal::factory()->submitted()->create();

        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'proposal_id' => $proposal->id,
            'reviewer_id' => $reviewer->id,
            'due_at' => '2027-03-15 23:59:00',
        ]);

        $assignment = ReviewAssignment::query()->whereBelongsTo($proposal)->whereBelongsTo($reviewer, 'reviewer')->firstOrFail();

        $response->assertRedirect(route('reviews.show', $assignment));
        $this->assertSame($admin->id, $assignment->assigned_by);
        $this->assertSame('assigned', $assignment->status);
    }

    public function test_admin_cannot_assign_proposal_lead_as_reviewer(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $lead = User::factory()->create(['roles' => ['dosen', 'reviewer']]);
        $proposal = Proposal::factory()->submitted()->for($lead, 'lead')->create();

        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'proposal_id' => $proposal->id,
            'reviewer_id' => $lead->id,
            'due_at' => now()->addWeek(),
        ]);

        $response->assertSessionHasErrors('reviewer_id');
        $this->assertDatabaseMissing('review_assignments', ['proposal_id' => $proposal->id]);
    }

    public function test_assigned_reviewer_can_save_review_draft(): void
    {
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $assignment = ReviewAssignment::factory()->for($reviewer, 'reviewer')->create();

        $response = $this->actingAs($reviewer)->put(route('reviews.update', $assignment), [
            'relevance_score' => 18,
            'methodology_score' => 17,
            'comments' => 'Metodologi cukup baik dan dapat dilaksanakan.',
            'has_conflict' => 0,
        ]);

        $response->assertRedirect();
        $assignment->refresh();
        $this->assertSame('in_review', $assignment->status);
        $this->assertSame(18, $assignment->relevance_score);
    }

    public function test_unassigned_reviewer_cannot_open_review(): void
    {
        $assignedReviewer = User::factory()->create(['roles' => ['reviewer']]);
        $otherReviewer = User::factory()->create(['roles' => ['reviewer']]);
        $assignment = ReviewAssignment::factory()->for($assignedReviewer, 'reviewer')->create();

        $this->actingAs($otherReviewer)->get(route('reviews.show', $assignment))->assertForbidden();
    }
}
