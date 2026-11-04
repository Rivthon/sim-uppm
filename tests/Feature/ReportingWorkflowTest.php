<?php

namespace Tests\Feature;

use App\Models\FinalReport;
use App\Models\FundingContract;
use App\Models\Proposal;
use App\Models\ResearchOutput;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportingWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_project_lead_can_submit_one_final_report(): void
    {
        Storage::fake('local');
        $lead = User::factory()->create(['roles' => ['dosen']]);
        $contract = $this->contractFor($lead);

        $response = $this->actingAs($lead)->post(route('reporting.final-reports.store', $contract), [
            'summary' => str_repeat('Kegiatan penelitian telah dilaksanakan sesuai tahapan dan menghasilkan capaian yang direncanakan. ', 2),
            'conclusions' => 'Hasil penelitian menjawab tujuan dan memberikan kontribusi terhadap bidang ilmu terkait.',
            'recommendations' => 'Penelitian dapat dikembangkan dengan cakupan responden yang lebih luas.',
            'budget_realized' => 9000000,
            'document' => UploadedFile::fake()->create('laporan-akhir.pdf', 500, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $report = FinalReport::query()->whereBelongsTo($contract, 'contract')->firstOrFail();
        $this->assertSame($lead->id, $report->submitted_by);
        Storage::disk('local')->assertExists($report->document_path);
    }

    public function test_other_lecturer_cannot_submit_output_for_contract(): void
    {
        $contract = $this->contractFor(User::factory()->create(['roles' => ['dosen']]));
        $other = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($other)->post(route('reporting.outputs.store', $contract), [])->assertForbidden();
        $this->assertDatabaseCount('research_outputs', 0);
    }

    public function test_project_lead_can_add_research_output(): void
    {
        Storage::fake('local');
        $lead = User::factory()->create(['roles' => ['dosen']]);
        $contract = $this->contractFor($lead);

        $response = $this->actingAs($lead)->post(route('reporting.outputs.store', $contract), [
            'type' => 'jurnal', 'title' => 'Analisis Sistem Informasi Penelitian dan Pengabdian Masyarakat',
            'achievement_status' => 'published', 'identifier' => '10.1234/sim.2026.01',
            'publisher' => 'Jurnal Teknologi Kampus', 'publication_date' => '2026-11-04',
            'url' => 'https://example.com/article',
            'evidence' => UploadedFile::fake()->create('artikel.pdf', 300, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $output = ResearchOutput::query()->whereBelongsTo($contract, 'contract')->firstOrFail();
        $this->assertSame('pending', $output->verification_status);
        Storage::disk('local')->assertExists($output->evidence_path);
    }

    public function test_only_admin_can_verify_research_output(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $output = ResearchOutput::factory()->create();
        $payload = ['verification_status' => 'verified', 'verification_notes' => 'Bukti luaran sesuai dan dapat diverifikasi.'];

        $this->actingAs($lecturer)->put(route('reporting.outputs.verify', $output), $payload)->assertForbidden();
        $this->actingAs($admin)->put(route('reporting.outputs.verify', $output), $payload)->assertRedirect();

        $this->assertDatabaseHas('research_outputs', ['id' => $output->id, 'verification_status' => 'verified', 'verified_by' => $admin->id]);
    }

    public function test_revision_verification_requires_notes(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $output = ResearchOutput::factory()->create();

        $this->actingAs($admin)->put(route('reporting.outputs.verify', $output), [
            'verification_status' => 'revision', 'verification_notes' => '',
        ])->assertSessionHasErrors('verification_notes');
    }

    private function contractFor(User $lead): FundingContract
    {
        $proposal = Proposal::factory()->for($lead, 'lead')->submitted()->create();

        return FundingContract::factory()->for($proposal)->create(['status' => 'active']);
    }
}
