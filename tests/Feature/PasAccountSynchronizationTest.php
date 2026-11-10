<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ProposalMember;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasAccountSynchronizationTest extends TestCase
{
    use DatabaseTransactions;

    private bool $pasDatabaseInitialized = false;

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

    public function test_local_pas_database_authenticates_lecturer_by_nidn_without_copying_password(): void
    {
        $this->setUpPasDatabase();
        DB::connection('pas')->table('program_studi')->insert([
            'jurusan_id' => '15401',
            'nama' => 'Kebidanan',
        ]);
        DB::connection('pas')->table('dosen')->insert([
            'dosen_id' => 6,
            'jurusan_id' => '15401',
            'nidn' => '0417068401',
            'kd_dosen' => 'DTB04336501001',
            'nama' => 'Dosen PAS',
            'email' => 'dosen@sbh.ac.id',
            'password' => Hash::make('rahasia-pas'),
            'status_dosen' => 'Dosen Tetap',
        ]);

        $this->post(route('login.store'), [
            'username' => '0417068401',
            'password' => 'rahasia-pas',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-DOSEN-6',
            'username' => '0417068401',
            'identity_number' => '0417068401',
            'study_program' => 'Kebidanan',
            'password' => null,
        ]);
    }

    public function test_local_pas_database_rejects_an_incorrect_password(): void
    {
        $this->setUpPasDatabase();
        DB::connection('pas')->table('dosen')->insert([
            'dosen_id' => 7,
            'jurusan_id' => '15401',
            'nidn' => '0400000007',
            'kd_dosen' => 'DSN007',
            'nama' => 'Dosen PAS',
            'email' => 'dosen7@sbh.ac.id',
            'password' => Hash::make('password-benar'),
            'status_dosen' => 'Dosen Tetap',
        ]);

        $this->post(route('login.store'), [
            'username' => '0400000007',
            'password' => 'password-salah',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['pas_user_id' => 'PAS-DOSEN-7']);
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

    private function setUpPasDatabase(): void
    {
        config()->set('services.pas.driver', 'database');
        config()->set('services.pas.database_connection', 'pas');
        config()->set('database.connections.pas', [
            ...config('database.connections.mysql'),
            'prefix' => 'test_pas_auth_',
        ]);
        DB::purge('pas');
        $this->dropPasTestTables();
        $this->pasDatabaseInitialized = true;

        Schema::connection('pas')->create('program_studi', function (Blueprint $table): void {
            $table->string('jurusan_id')->primary();
            $table->string('nama');
        });
        Schema::connection('pas')->create('dosen', function (Blueprint $table): void {
            $table->integer('dosen_id')->primary();
            $table->string('jurusan_id');
            $table->string('nidn')->nullable();
            $table->string('kd_dosen')->nullable();
            $table->string('nama');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('status_dosen');
        });
        Schema::connection('pas')->create('mahasiswa', function (Blueprint $table): void {
            $table->integer('mahasiswa_id')->primary();
            $table->string('jurusan_id');
            $table->string('nim');
            $table->string('nama');
            $table->string('email');
            $table->string('password');
            $table->string('status_mhs');
        });
    }

    private function dropPasTestTables(): void
    {
        Schema::connection('pas')->dropIfExists('mahasiswa');
        Schema::connection('pas')->dropIfExists('dosen');
        Schema::connection('pas')->dropIfExists('program_studi');
    }

    protected function tearDown(): void
    {
        if ($this->pasDatabaseInitialized) {
            $this->dropPasTestTables();
        }

        parent::tearDown();
    }
}
