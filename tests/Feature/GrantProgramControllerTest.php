<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GrantProgramControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_grant_program(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $academicYear = AcademicYear::factory()->create();
        $scheme = GrantScheme::factory()->create();

        $response = $this->actingAs($admin)->post(route('grants.store'), [
            'name' => 'Hibah Internal 2027',
            'academic_year_id' => $academicYear->id,
            'grant_scheme_id' => $scheme->id,
            'submission_starts_at' => '2027-01-01 08:00:00',
            'submission_ends_at' => '2027-01-31 23:59:00',
            'quota' => 25,
            'budget' => 250000000,
            'requirements' => 'Dosen aktif Kampus SBH.',
            'status' => 'draft',
        ]);

        $period = GrantPeriod::query()->where('name', 'Hibah Internal 2027')->firstOrFail();

        $response->assertRedirect(route('grants.edit', $period));
        $this->assertSame($admin->id, $period->created_by);
        $this->assertSame(250000000, $period->budget);
    }

    public function test_non_admin_cannot_open_grant_management(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->get(route('grants.index'))->assertForbidden();
    }

    public function test_rejects_submission_end_before_start(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $academicYear = AcademicYear::factory()->create();
        $scheme = GrantScheme::factory()->create();

        $response = $this->actingAs($admin)->from(route('grants.create'))->post(route('grants.store'), [
            'name' => 'Periode Tidak Valid',
            'academic_year_id' => $academicYear->id,
            'grant_scheme_id' => $scheme->id,
            'submission_starts_at' => '2027-02-01 08:00:00',
            'submission_ends_at' => '2027-01-01 08:00:00',
            'budget' => 10000000,
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('grants.create'))
            ->assertSessionHasErrors(['submission_ends_at']);
        $this->assertDatabaseMissing('grant_periods', ['name' => 'Periode Tidak Valid']);
    }
}
