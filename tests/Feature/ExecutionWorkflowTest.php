<?php

namespace Tests\Feature;

use App\Models\FundingContract;
use App\Models\MonitoringEvaluation;
use App\Models\ProgressReport;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExecutionWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_project_lead_can_add_activity_to_active_contract(): void
    {
        $lead = User::factory()->create(['roles' => ['dosen']]);
        $contract = $this->contractFor($lead);

        $response = $this->actingAs($lead)->post(route('execution.activities.store', $contract), [
            'activity_date' => now()->toDateString(),
            'description' => 'Pelaksanaan pengumpulan data lapangan tahap pertama.',
            'result' => 'Data responden tahap pertama berhasil dihimpun.',
            'progress_percentage' => 35,
            'expense_amount' => 1500000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('activity_logs', ['funding_contract_id' => $contract->id, 'created_by' => $lead->id, 'progress_percentage' => 35]);
    }

    public function test_other_lecturer_cannot_add_activity_to_contract(): void
    {
        $contract = $this->contractFor(User::factory()->create(['roles' => ['dosen']]));
        $other = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($other)->post(route('execution.activities.store', $contract), [
            'activity_date' => now()->toDateString(), 'description' => 'Catatan kegiatan yang tidak boleh tersimpan di kontrak ini.',
            'result' => 'Hasil kegiatan tidak sah.', 'progress_percentage' => 10, 'expense_amount' => 0,
        ])->assertForbidden();
    }

    public function test_project_lead_can_submit_progress_report(): void
    {
        Storage::fake('local');
        $lead = User::factory()->create(['roles' => ['dosen']]);
        $contract = $this->contractFor($lead);

        $response = $this->actingAs($lead)->post(route('execution.reports.store', $contract), [
            'reporting_period' => now()->startOfMonth()->toDateString(), 'progress_percentage' => 50,
            'summary' => str_repeat('Ringkasan pelaksanaan kegiatan berjalan sesuai rencana. ', 2),
            'achievements' => 'Pengumpulan dan validasi data tahap pertama selesai.',
            'obstacles' => 'Penjadwalan beberapa responden mengalami penyesuaian.', 'budget_realized' => 4000000,
            'output_progress' => 'Draf artikel dan dokumentasi data mulai disusun.',
            'document' => UploadedFile::fake()->create('kemajuan.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $report = ProgressReport::query()->whereBelongsTo($contract, 'contract')->firstOrFail();
        Storage::disk('local')->assertExists($report->document_path);
    }

    public function test_admin_can_schedule_monev_with_reviewer(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $contract = FundingContract::factory()->create();

        $this->actingAs($admin)->post(route('execution.evaluations.store', $contract), [
            'evaluator_id' => $reviewer->id, 'scheduled_at' => now()->addWeek()->format('Y-m-d H:i:s'), 'mode' => 'online',
        ])->assertRedirect();

        $this->assertDatabaseHas('monitoring_evaluations', ['funding_contract_id' => $contract->id, 'evaluator_id' => $reviewer->id, 'status' => 'scheduled']);
    }

    public function test_assigned_evaluator_can_complete_monev_but_other_reviewer_cannot(): void
    {
        $reviewer = User::factory()->create(['roles' => ['reviewer']]);
        $other = User::factory()->create(['roles' => ['reviewer']]);
        $evaluation = MonitoringEvaluation::factory()->create(['evaluator_id' => $reviewer->id]);
        $payload = ['score' => 88, 'findings' => 'Pelaksanaan kegiatan telah sesuai dengan metode yang disetujui.',
            'recommendations' => 'Tim perlu mempercepat penyusunan luaran artikel ilmiah.',
            'follow_up' => 'Tim menyerahkan draf artikel pada laporan periode berikutnya.'];

        $this->actingAs($other)->put(route('execution.evaluations.update', $evaluation), $payload)->assertForbidden();
        $this->actingAs($reviewer)->put(route('execution.evaluations.update', $evaluation), $payload)->assertRedirect();

        $this->assertDatabaseHas('monitoring_evaluations', ['id' => $evaluation->id, 'score' => 88, 'status' => 'completed']);
    }

    private function contractFor(User $lead): FundingContract
    {
        $proposal = Proposal::factory()->for($lead, 'lead')->submitted()->create();

        return FundingContract::factory()->for($proposal)->create(['starts_at' => now()->subDay(), 'ends_at' => now()->addMonths(6), 'status' => 'active']);
    }
}
