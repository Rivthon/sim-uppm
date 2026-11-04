<?php

namespace Tests\Feature;

use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use App\Models\Proposal;
use App\Models\ResearchField;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_lecturer_can_save_proposal_with_members_and_private_document(): void
    {
        Storage::fake('local');
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $scheme = GrantScheme::factory()->create(['type' => 'penelitian', 'maximum_funding' => 15000000]);
        $period = GrantPeriod::factory()->for($scheme, 'scheme')->create(['status' => 'open']);
        $field = ResearchField::factory()->create();

        $response = $this->actingAs($lecturer)->post(route('proposals.store'), $this->validPayload($period, $field, [
            'document' => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
            'members' => [[
                'name' => 'Anggota Dosen',
                'identity_number' => '0123456789',
                'member_type' => 'dosen',
                'study_program' => 'Sistem Informasi',
            ]],
        ]));

        $proposal = Proposal::query()->where('title', 'Sistem Monitoring Penelitian Terintegrasi')->firstOrFail();

        $response->assertRedirect(route('proposals.edit', $proposal));
        $this->assertSame($lecturer->id, $proposal->lead_id);
        $this->assertSame('penelitian', $proposal->type);
        $this->assertSame('Anggota Dosen', $proposal->members()->sole()->name);
        Storage::disk('local')->assertExists($proposal->document_path);
    }

    public function test_lecturer_cannot_view_another_lecturers_proposal(): void
    {
        $owner = User::factory()->create(['roles' => ['dosen']]);
        $otherLecturer = User::factory()->create(['roles' => ['dosen']]);
        $proposal = Proposal::factory()->for($owner, 'lead')->create();

        $this->actingAs($otherLecturer)->get(route('proposals.show', $proposal))->assertForbidden();
    }

    public function test_rejects_requested_budget_above_scheme_limit(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $scheme = GrantScheme::factory()->create(['maximum_funding' => 10000000]);
        $period = GrantPeriod::factory()->for($scheme, 'scheme')->create(['status' => 'open']);
        $field = ResearchField::factory()->create();

        $response = $this->actingAs($lecturer)->from(route('proposals.create'))->post(
            route('proposals.store'),
            $this->validPayload($period, $field, ['requested_budget' => 10000001]),
        );

        $response->assertRedirect(route('proposals.create'))->assertSessionHasErrors('requested_budget');
        $this->assertDatabaseMissing('proposals', ['title' => 'Sistem Monitoring Penelitian Terintegrasi']);
    }

    public function test_create_accepts_short_proposal_content(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $scheme = GrantScheme::factory()->create(['maximum_funding' => 15000000]);
        $period = GrantPeriod::factory()->for($scheme, 'scheme')->create(['status' => 'open']);
        $field = ResearchField::factory()->create();

        $response = $this->actingAs($lecturer)->post(route('proposals.store'), $this->validPayload($period, $field, [
            'abstract' => 'Terlalu singkat.',
            'members' => $this->emptyMemberRows(),
        ]));

        $response->assertSessionDoesntHaveErrors(['abstract']);
        $this->assertDatabaseHas('proposals', [
            'lead_id' => $lecturer->id,
            'abstract' => 'Terlalu singkat.',
        ]);
    }

    public function test_edit_accepts_short_proposal_content(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $scheme = GrantScheme::factory()->create(['maximum_funding' => 15000000]);
        $period = GrantPeriod::factory()->for($scheme, 'scheme')->create(['status' => 'open']);
        $field = ResearchField::factory()->create();
        $proposal = Proposal::factory()->for($lecturer, 'lead')->create([
            'grant_period_id' => $period->id,
            'research_field_id' => $field->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($lecturer)->put(route('proposals.update', $proposal), $this->validPayload($period, $field, [
            'methodology' => 'Terlalu singkat.',
            'members' => $this->emptyMemberRows(),
        ]));

        $response->assertSessionDoesntHaveErrors(['methodology']);
        $this->assertSame('Terlalu singkat.', $proposal->fresh()->methodology);
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(GrantPeriod $period, ResearchField $field, array $overrides = []): array
    {
        return [
            'grant_period_id' => $period->id,
            'research_field_id' => $field->id,
            'title' => 'Sistem Monitoring Penelitian Terintegrasi',
            'abstract' => str_repeat('Ringkasan penelitian dan manfaat yang akan dihasilkan. ', 3),
            'background' => str_repeat('Permasalahan pengelolaan penelitian membutuhkan solusi terintegrasi. ', 3),
            'objectives' => 'Menghasilkan sistem monitoring penelitian yang akurat dan transparan.',
            'methodology' => str_repeat('Tahapan penelitian meliputi analisis, perancangan, implementasi, dan evaluasi. ', 2),
            'roadmap_alignment' => 'Sesuai roadmap transformasi digital dan tata kelola penelitian kampus.',
            'requested_budget' => 10000000,
            'duration_months' => 12,
            ...$overrides,
        ];
    }

    /** @return array<int, array<string, string>> */
    private function emptyMemberRows(): array
    {
        return array_fill(0, 3, [
            'name' => '',
            'identity_number' => '',
            'member_type' => 'dosen',
            'study_program' => '',
        ]);
    }
}
