<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ProposalMember;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PasAccountSynchronizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_changed_password_uses_nidn_as_synced_lecturer_username(): void
    {
        $this->fakePas([
            'id' => 'PAS-SYNC-DOSEN-001',
            'username' => 'dosen.lama',
            'name' => 'Dosen NIDN',
            'nidn' => '0412345678',
            'role' => 'dosen',
            'password_changed' => true,
        ]);

        $this->post(route('login.store'), [
            'username' => '0412345678',
            'password' => 'password-baru',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-SYNC-DOSEN-001',
            'username' => '0412345678',
            'identity_number' => '0412345678',
            'pas_password_changed' => true,
            'password' => null,
        ]);
        Http::assertSent(fn (Request $request): bool => $request['username'] === '0412345678' && $request['password'] === 'password-baru');
    }

    public function test_changed_password_uses_lecturer_code_when_nidn_is_unavailable(): void
    {
        $this->fakePas([
            'id' => 'PAS-SYNC-DOSEN-002',
            'username' => 'dosen.dua',
            'name' => 'Dosen Kode',
            'kode_dosen' => 'DSN-204',
            'role' => 'lecturer',
            'status_ganti_password' => 'sudah',
        ]);

        $this->post(route('login.store'), [
            'username' => 'DSN-204',
            'password' => 'password-baru',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-SYNC-DOSEN-002',
            'username' => 'DSN-204',
            'identity_number' => 'DSN-204',
            'pas_password_changed' => true,
        ]);
    }

    public function test_unchanged_password_keeps_original_pas_username(): void
    {
        $this->fakePas([
            'id' => 'PAS-SYNC-DOSEN-003',
            'username' => 'dosen.awal',
            'name' => 'Dosen Username',
            'nidn' => '0499999999',
            'role' => 'dosen',
            'is_password_changed' => false,
        ]);

        $this->post(route('login.store'), [
            'username' => 'dosen.awal',
            'password' => 'password-awal',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-SYNC-DOSEN-003',
            'username' => 'dosen.awal',
            'identity_number' => '0499999999',
            'pas_password_changed' => false,
        ]);
    }

    public function test_changed_password_uses_nim_as_synced_student_username(): void
    {
        $proposal = Proposal::factory()->create();
        ProposalMember::factory()->for($proposal)->create([
            'identity_number' => '2310112233',
            'member_type' => 'mahasiswa',
        ]);
        $this->fakePas([
            'id' => 'PAS-SYNC-MHS-001',
            'username' => 'mahasiswa.lama',
            'name' => 'Mahasiswa NIM',
            'nim' => '2310112233',
            'role' => 'student',
            'password_changed_at' => '2026-11-05 10:00:00',
        ]);

        $this->post(route('login.store'), [
            'username' => '2310112233',
            'password' => 'password-baru',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-SYNC-MHS-001',
            'username' => '2310112233',
            'identity_number' => '2310112233',
            'pas_password_changed' => true,
        ]);
        $this->assertDatabaseHas('proposal_members', [
            'identity_number' => '2310112233',
            'member_type' => 'mahasiswa',
            'user_id' => auth()->id(),
        ]);
    }

    /** @param array<string, mixed> $identity */
    private function fakePas(array $identity): void
    {
        config()->set('services.pas.driver', 'api');
        config()->set('services.pas.endpoint', 'https://pas.example.test/api/login');
        Http::preventStrayRequests();
        Http::fake([
            'https://pas.example.test/api/login' => Http::response(['data' => $identity]),
        ]);
    }
}
