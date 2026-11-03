<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProposalSubmissionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_submit_complete_draft_and_it_becomes_locked(): void
    {
        $this->travelTo('2027-02-10 09:00:00');
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $proposal = Proposal::factory()->for($lecturer, 'lead')->create([
            'document_path' => 'proposals/proposal.pdf',
        ]);

        $response = $this->actingAs($lecturer)->post(route('proposals.submissions.store', $proposal));

        $response->assertRedirect(route('proposals.show', $proposal));
        $proposal->refresh();
        $this->assertSame('submitted', $proposal->status);
        $this->assertSame('UPPM-2027-'.str_pad((string) $proposal->id, 5, '0', STR_PAD_LEFT), $proposal->code);
        $this->actingAs($lecturer)->get(route('proposals.edit', $proposal))->assertForbidden();
    }

    public function test_draft_without_document_is_not_submitted(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $proposal = Proposal::factory()->for($lecturer, 'lead')->create(['document_path' => null]);

        $response = $this->actingAs($lecturer)->from(route('proposals.show', $proposal))
            ->post(route('proposals.submissions.store', $proposal));

        $response->assertRedirect(route('proposals.show', $proposal))->assertSessionHasErrors('document');
        $this->assertSame('draft', $proposal->fresh()->status);
    }

    public function test_other_lecturer_cannot_submit_proposal(): void
    {
        $owner = User::factory()->create(['roles' => ['dosen']]);
        $otherLecturer = User::factory()->create(['roles' => ['dosen']]);
        $proposal = Proposal::factory()->for($owner, 'lead')->create(['document_path' => 'proposals/proposal.pdf']);

        $this->actingAs($otherLecturer)->post(route('proposals.submissions.store', $proposal))->assertForbidden();
        $this->assertSame('draft', $proposal->fresh()->status);
    }
}
