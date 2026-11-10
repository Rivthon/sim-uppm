<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ProposalMember;
use App\Models\ReviewAssignment;
use App\Models\User;
use App\Services\Pas\PasAuthenticator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class StudentDashboardAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unselected_student_is_logged_out_from_protected_pages(): void
    {
        $student = User::factory()->create([
            'identity_number' => 'MHS-UNSELECTED',
            'roles' => ['mahasiswa'],
        ]);

        $this->actingAs($student)->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_selected_student_sees_only_proposals_where_they_are_a_member(): void
    {
        $student = User::factory()->create([
            'identity_number' => 'MHS-SELECTED',
            'roles' => ['mahasiswa'],
        ]);
        $accessibleProposal = Proposal::factory()->create(['title' => 'Proposal Tim Mahasiswa', 'created_at' => now()]);
        Proposal::factory()->create(['title' => 'Proposal Tim Lain', 'created_at' => now()]);
        ProposalMember::factory()->for($accessibleProposal)->create([
            'user_id' => $student->id,
            'identity_number' => $student->identity_number,
            'member_type' => 'mahasiswa',
        ]);

        $this->actingAs($student)->get(route('dashboard', ['year' => now()->year]))
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats): bool => $stats['proposals'] === 1);

        $this->actingAs($student)->get(route('proposals.index'))
            ->assertOk()
            ->assertSee('Proposal Tim Mahasiswa')
            ->assertDontSee('Proposal Tim Lain');
    }

    public function test_selected_student_can_view_team_proposal_but_cannot_manage_it(): void
    {
        $student = User::factory()->create([
            'identity_number' => 'MHS-READONLY',
            'roles' => ['mahasiswa'],
        ]);
        $proposal = Proposal::factory()->create();
        ProposalMember::factory()->for($proposal)->create([
            'user_id' => $student->id,
            'identity_number' => $student->identity_number,
            'member_type' => 'mahasiswa',
        ]);
        ReviewAssignment::factory()->submitted()->for($proposal)->create([
            'comments' => 'HASIL REVIEW KHUSUS DOSEN',
        ]);

        $this->actingAs($student)->get(route('proposals.show', $proposal))
            ->assertOk()
            ->assertDontSee('HASIL REVIEW KHUSUS DOSEN');
        $this->actingAs($student)->get(route('proposals.create'))->assertForbidden();
        $this->actingAs($student)->get(route('proposals.edit', $proposal))->assertForbidden();
    }

    public function test_pas_login_rejects_student_who_has_not_been_selected(): void
    {
        $authenticator = Mockery::mock(PasAuthenticator::class);
        $authenticator->shouldReceive('authenticate')->once()->andReturn($this->studentIdentity('PAS-NOT-SELECTED', 'MHS-NOT-SELECTED'));
        $this->app->instance(PasAuthenticator::class, $authenticator);

        $this->post(route('login.store'), ['username' => 'student', 'password' => 'password'])
            ->assertRedirect()
            ->assertSessionHasErrors('username');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['pas_user_id' => 'PAS-NOT-SELECTED']);
    }

    public function test_pas_login_creates_selected_student_account_and_links_membership(): void
    {
        $proposal = Proposal::factory()->create();
        $membership = ProposalMember::factory()->for($proposal)->create([
            'user_id' => null,
            'identity_number' => 'MHS-PAS-SELECTED',
            'member_type' => 'mahasiswa',
        ]);
        $authenticator = Mockery::mock(PasAuthenticator::class);
        $authenticator->shouldReceive('authenticate')->once()->andReturn($this->studentIdentity('PAS-SELECTED', 'MHS-PAS-SELECTED'));
        $this->app->instance(PasAuthenticator::class, $authenticator);

        $this->post(route('login.store'), ['username' => 'student', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $student = User::query()->where('pas_user_id', 'PAS-SELECTED')->firstOrFail();
        $this->assertAuthenticatedAs($student);
        $this->assertSame($student->id, $membership->fresh()->user_id);
    }

    public function test_seeded_mock_user_can_login_with_local_password(): void
    {
        config()->set('services.pas.driver', 'mock');
        $lecturer = User::factory()->create([
            'username' => 'dosen.demo',
            'password' => Hash::make('password123'),
            'roles' => ['dosen'],
        ]);

        $this->post(route('login.store'), ['username' => 'dosen.demo', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($lecturer);
    }

    /** @return array<string, mixed> */
    private function studentIdentity(string $pasUserId, string $identityNumber): array
    {
        return [
            'pas_user_id' => $pasUserId,
            'username' => 'student',
            'name' => 'Mahasiswa Terpilih',
            'email' => 'student@example.test',
            'identity_number' => $identityNumber,
            'unit' => 'Kampus SBH',
            'study_program' => 'Sistem Informasi',
            'roles' => ['mahasiswa'],
            'is_active' => true,
        ];
    }
}
