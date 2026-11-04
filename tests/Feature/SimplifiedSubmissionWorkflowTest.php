<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SimplifiedSubmissionWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_inbox_only_lists_submitted_proposals(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $submitted = Proposal::factory()->submitted()->create(['title' => 'Proposal Siap Direview']);
        $draft = Proposal::factory()->create(['title' => 'Proposal Masih Draft']);

        $this->actingAs($admin)->get(route('incoming-proposals.index'))
            ->assertOk()
            ->assertSee($submitted->title)
            ->assertDontSee($draft->title)
            ->assertSee('Baca & assign reviewer', false);
    }

    public function test_lecturer_cannot_open_admin_inbox(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->get(route('incoming-proposals.index'))->assertForbidden();
    }

    public function test_admin_can_see_reviewer_assignment_form_on_proposal_detail(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $reviewer = User::factory()->create(['roles' => ['dosen', 'reviewer'], 'name' => 'Reviewer Tersedia']);
        $proposal = Proposal::factory()->submitted()->create();

        $this->actingAs($admin)->get(route('proposals.show', $proposal))
            ->assertOk()
            ->assertSee('Assign reviewer langsung')
            ->assertSee($reviewer->name)
            ->assertSee('return_to_proposal', false);
    }

    public function test_admin_assigns_reviewer_and_stays_on_proposal_detail(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $reviewer = User::factory()->create(['roles' => ['dosen', 'reviewer']]);
        $proposal = Proposal::factory()->submitted()->create();

        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'proposal_id' => $proposal->id,
            'reviewer_id' => $reviewer->id,
            'due_at' => now()->addWeek(),
            'return_to_proposal' => 1,
        ]);

        $response->assertRedirect(route('proposals.show', $proposal));
        $this->assertDatabaseHas('review_assignments', [
            'proposal_id' => $proposal->id,
            'reviewer_id' => $reviewer->id,
            'assigned_by' => $admin->id,
            'status' => 'assigned',
        ]);
        $this->assertSame(1, ReviewAssignment::query()->whereBelongsTo($proposal)->count());
    }

    public function test_proposal_lead_is_not_offered_as_own_reviewer(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $lead = User::factory()->create(['roles' => ['dosen', 'reviewer'], 'name' => 'Ketua Proposal Reviewer']);
        $proposal = Proposal::factory()->submitted()->for($lead, 'lead')->create();

        $this->actingAs($admin)->get(route('proposals.show', $proposal))
            ->assertOk()
            ->assertDontSee($lead->name.' —');
    }
}
