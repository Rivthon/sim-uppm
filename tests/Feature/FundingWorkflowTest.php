<?php

namespace Tests\Feature;

use App\Models\FundingContract;
use App\Models\Proposal;
use App\Models\ProposalDecision;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FundingWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_accept_proposal_with_approved_budget(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $proposal = Proposal::factory()->submitted()->create(['requested_budget' => 12000000]);

        $response = $this->actingAs($admin)->post(route('proposals.decisions.store', $proposal), [
            'decision' => 'accepted',
            'approved_budget' => 10000000,
            'notes' => 'Diterima berdasarkan hasil penilaian reviewer.',
        ]);

        $response->assertRedirect();
        $decision = $proposal->decision()->firstOrFail();
        $this->assertSame('accepted', $decision->decision);
        $this->assertSame(10000000, $decision->approved_budget);
        $this->assertSame($admin->id, $decision->decided_by);
    }

    public function test_approved_budget_cannot_exceed_requested_budget(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $proposal = Proposal::factory()->submitted()->create(['requested_budget' => 10000000]);

        $response = $this->actingAs($admin)->post(route('proposals.decisions.store', $proposal), [
            'decision' => 'accepted',
            'approved_budget' => 10000001,
        ]);

        $response->assertSessionHasErrors('approved_budget');
        $this->assertDatabaseMissing('proposal_decisions', ['proposal_id' => $proposal->id]);
    }

    public function test_admin_can_create_contract_for_accepted_proposal(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $proposal = Proposal::factory()->submitted()->create();
        ProposalDecision::factory()->for($proposal)->create(['approved_budget' => 10000000]);

        $response = $this->actingAs($admin)->post(route('funding.store'), [
            'proposal_id' => $proposal->id,
            'contract_number' => 'CTR/UPPM/001/2027',
            'decree_number' => 'SK/UPPM/001/2027',
            'signed_at' => '2027-04-01',
            'starts_at' => '2027-04-01',
            'ends_at' => '2027-12-01',
            'contract_value' => 10000000,
            'status' => 'active',
        ]);

        $contract = FundingContract::query()->where('contract_number', 'CTR/UPPM/001/2027')->firstOrFail();
        $response->assertRedirect(route('funding.show', $contract));
        $this->assertSame($admin->id, $contract->created_by);
    }

    public function test_lecturer_cannot_view_another_lecturers_contract(): void
    {
        $owner = User::factory()->create(['roles' => ['dosen']]);
        $other = User::factory()->create(['roles' => ['dosen']]);
        $proposal = Proposal::factory()->for($owner, 'lead')->create();
        $contract = FundingContract::factory()->for($proposal)->create();

        $this->actingAs($other)->get(route('funding.show', $contract))->assertForbidden();
    }

    public function test_total_disbursements_cannot_exceed_contract_value(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $contract = FundingContract::factory()->create(['contract_value' => 10000000]);
        $contract->disbursements()->create([
            'sequence' => 1, 'label' => 'Termin I', 'percentage' => 70, 'amount' => 7000000,
            'scheduled_at' => '2027-04-01', 'status' => 'planned', 'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('funding.disbursements.store', $contract), [
            'sequence' => 2,
            'label' => 'Termin II',
            'percentage' => 40,
            'amount' => 4000000,
            'scheduled_at' => '2027-09-01',
            'status' => 'planned',
        ]);

        $response->assertSessionHasErrors(['amount', 'percentage']);
        $this->assertSame(1, $contract->disbursements()->count());
    }
}
