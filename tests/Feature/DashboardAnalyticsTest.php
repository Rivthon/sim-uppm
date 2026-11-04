<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\ResearchOutput;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_dashboard_uses_persisted_statistics(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        Proposal::factory()->submitted()->create(['code' => 'UPPM-'.now()->year.'-9001', 'type' => 'penelitian', 'created_at' => now()]);
        Proposal::factory()->submitted()->create(['code' => 'UPPM-'.now()->year.'-9002', 'type' => 'penelitian', 'created_at' => now()]);
        Proposal::factory()->create(['type' => 'pkm', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('dashboard', ['year' => now()->year, 'type' => 'all']));

        $response->assertOk()->assertViewHas('stats', fn (array $stats): bool => $stats['proposals'] === 3 && $stats['submitted'] === 2);
    }

    public function test_lecturer_dashboard_does_not_include_another_lecturers_proposal(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $other = User::factory()->create(['roles' => ['dosen']]);
        Proposal::factory()->for($lecturer, 'lead')->create(['created_at' => now()]);
        Proposal::factory()->for($other, 'lead')->create(['created_at' => now()]);

        $this->actingAs($lecturer)->get(route('dashboard', ['year' => now()->year]))
            ->assertOk()->assertViewHas('stats', fn (array $stats): bool => $stats['proposals'] === 1);
    }

    public function test_admin_can_set_kpi_target_and_lecturer_cannot(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $payload = ['year' => now()->year, 'metric' => 'verified_outputs', 'target_value' => 25, 'unit' => 'luaran'];

        $this->actingAs($lecturer)->post(route('dashboard.kpi.store'), $payload)->assertForbidden();
        $this->actingAs($admin)->post(route('dashboard.kpi.store'), $payload)->assertRedirect();

        $this->assertDatabaseHas('kpi_targets', ['year' => now()->year, 'metric' => 'verified_outputs', 'target_value' => 25]);
    }

    public function test_accreditation_recap_contains_verified_output(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $output = ResearchOutput::factory()->create(['verification_status' => 'verified', 'verified_at' => now(), 'title' => 'Artikel Terverifikasi UPPM']);

        $this->actingAs($admin)->get(route('accreditation.index', ['year' => now()->year]))
            ->assertOk()->assertSee($output->title);
    }

    public function test_accreditation_recap_and_export_are_admin_only(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->get(route('accreditation.index'))->assertForbidden();
        $this->actingAs($lecturer)->get(route('accreditation.export'))->assertForbidden();
    }

    public function test_admin_can_export_accreditation_recap_as_csv(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);

        $this->actingAs($admin)->get(route('accreditation.export', ['year' => now()->year]))
            ->assertOk()
            ->assertDownload('rekap-akreditasi-'.now()->year.'.csv');
    }
}
