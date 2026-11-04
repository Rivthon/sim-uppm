<?php

namespace Tests\Feature;

use App\Models\StrategicPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StrategicPlanWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_strategic_plan_with_private_document(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);

        $response = $this->actingAs($admin)->post(route('strategic-plans.store'), [
            'title' => 'Renstra Penelitian Institusi 2026-2030', 'scope' => 'institution', 'type' => 'penelitian',
            'start_year' => 2026, 'end_year' => 2030,
            'vision' => 'Menjadi institusi unggul dalam penelitian inovatif dan berdampak bagi masyarakat luas.',
            'objectives' => 'Meningkatkan kualitas penelitian, publikasi ilmiah, kolaborasi, dan hilirisasi hasil penelitian.',
            'status' => 'active', 'document' => UploadedFile::fake()->create('renstra.pdf', 500, 'application/pdf'),
        ]);

        $plan = StrategicPlan::query()->where('title', 'Renstra Penelitian Institusi 2026-2030')->firstOrFail();
        $response->assertRedirect(route('strategic-plans.show', $plan));
        Storage::disk('local')->assertExists($plan->document_path);
    }

    public function test_lecturer_can_view_active_plan_but_cannot_view_draft_plan(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $active = StrategicPlan::factory()->create(['status' => 'active']);
        $draft = StrategicPlan::factory()->create(['status' => 'draft']);

        $this->actingAs($lecturer)->get(route('strategic-plans.show', $active))->assertOk();
        $this->actingAs($lecturer)->get(route('strategic-plans.show', $draft))->assertForbidden();
    }

    public function test_lecturer_cannot_create_strategic_plan(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->post(route('strategic-plans.store'), [])->assertForbidden();
    }

    public function test_admin_can_add_milestone_inside_plan_period(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $plan = StrategicPlan::factory()->create(['start_year' => 2026, 'end_year' => 2030]);

        $this->actingAs($admin)->post(route('strategic-plans.milestones.store', $plan), [
            'start_year' => 2026, 'end_year' => 2027, 'theme' => 'Penguatan fondasi riset',
            'focus' => 'Pemetaan kapasitas peneliti dan penguatan kelompok keahlian institusi.',
            'indicators' => 'Terbentuk kelompok riset dan tersedianya peta kompetensi dosen.',
            'target_outputs' => 'Artikel nasional dan proposal kolaboratif.', 'sort_order' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('roadmap_milestones', ['strategic_plan_id' => $plan->id, 'theme' => 'Penguatan fondasi riset']);
    }

    public function test_milestone_period_must_stay_inside_strategic_plan_period(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $plan = StrategicPlan::factory()->create(['start_year' => 2026, 'end_year' => 2030]);

        $this->actingAs($admin)->post(route('strategic-plans.milestones.store', $plan), [
            'start_year' => 2025, 'end_year' => 2027, 'theme' => 'Tahap di luar periode',
            'focus' => 'Fokus tahapan roadmap yang berada di luar periode Renstra.',
            'indicators' => 'Indikator capaian tahapan tersedia.', 'sort_order' => 1,
        ])->assertSessionHasErrors('start_year');

        $this->assertDatabaseCount('roadmap_milestones', 0);
    }
}
