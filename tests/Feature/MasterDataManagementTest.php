<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use App\Models\ResearchField;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterDataManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_and_update_study_program(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $payload = ['code' => 'TI-S1', 'name' => 'Teknik Informatika', 'faculty' => 'Fakultas Teknologi', 'level' => 'S1', 'is_active' => 1];

        $this->actingAs($admin)->post(route('master-data.study-programs.store'), $payload)->assertRedirect();
        $program = StudyProgram::query()->where('code', 'TI-S1')->firstOrFail();
        $this->actingAs($admin)->put(route('master-data.study-programs.update', $program), [...$payload, 'name' => 'Informatika'])->assertRedirect();

        $this->assertDatabaseHas('study_programs', ['id' => $program->id, 'name' => 'Informatika']);
    }

    public function test_lecturer_cannot_manage_master_data(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->post(route('master-data.research-fields.store'), [
            'code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'is_active' => 1,
        ])->assertForbidden();
    }

    public function test_master_data_code_must_be_unique(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        ResearchField::factory()->create(['code' => 'SISTEM']);

        $this->actingAs($admin)->post(route('master-data.research-fields.store'), [
            'code' => 'SISTEM', 'name' => 'Bidang ilmu lainnya', 'is_active' => 1,
        ])->assertSessionHasErrors('code');
    }

    public function test_activating_academic_year_deactivates_previous_year(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $previous = AcademicYear::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->post(route('master-data.academic-years.store'), [
            'name' => '2027/2028', 'starts_at' => '2027-08-01', 'ends_at' => '2028-07-31', 'is_active' => 1,
        ])->assertRedirect();

        $this->assertFalse($previous->fresh()->is_active);
        $this->assertDatabaseHas('academic_years', ['name' => '2027/2028', 'is_active' => true]);
    }

    public function test_scheme_used_by_grant_period_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $scheme = GrantScheme::factory()->create();
        GrantPeriod::factory()->for($scheme, 'scheme')->create();

        $response = $this->actingAs($admin)->delete(route('master-data.grant-schemes.destroy', $scheme));

        $response->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('grant_schemes', ['id' => $scheme->id]);
    }
}
