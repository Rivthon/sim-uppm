<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivityWorkspaceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_research_workspace_counts_only_research_proposals(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $existingResearchProposals = Proposal::query()->where('type', 'penelitian')->count();
        Proposal::factory()->create(['type' => 'penelitian']);
        Proposal::factory()->create(['type' => 'pkm']);

        $this->actingAs($admin)->get(route('workspaces.show', 'penelitian'))
            ->assertOk()->assertSee('Penelitian &amp; Pengembangan', false)
            ->assertViewHas('stats', fn (array $stats): bool => $stats['proposals'] === $existingResearchProposals + 1);
    }

    public function test_pkm_workspace_counts_only_current_lecturers_pkm_proposals(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $other = User::factory()->create(['roles' => ['dosen']]);
        Proposal::factory()->for($lecturer, 'lead')->create(['type' => 'pkm']);
        Proposal::factory()->for($other, 'lead')->create(['type' => 'pkm']);
        Proposal::factory()->for($lecturer, 'lead')->create(['type' => 'penelitian']);

        $this->actingAs($lecturer)->get(route('workspaces.show', 'pkm'))
            ->assertOk()->assertViewHas('stats', fn (array $stats): bool => $stats['proposals'] === 1);
    }

    public function test_proposal_index_type_filter_does_not_mix_research_and_pkm(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $research = Proposal::factory()->create(['type' => 'penelitian', 'title' => 'Riset Sistem Informasi Kampus']);
        $pkm = Proposal::factory()->create(['type' => 'pkm', 'title' => 'Pelatihan Digital untuk Masyarakat']);

        $response = $this->actingAs($admin)->get(route('proposals.index', ['type' => 'pkm']));

        $response->assertOk()->assertSee($pkm->title)->assertDontSee($research->title);
    }

    public function test_invalid_activity_type_filter_is_rejected(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);

        $this->actingAs($admin)->get(route('proposals.index', ['type' => 'keuangan']))
            ->assertSessionHasErrors('type');
    }
}
