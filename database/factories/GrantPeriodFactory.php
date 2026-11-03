<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GrantPeriod>
 */
class GrantPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'grant_scheme_id' => GrantScheme::factory(),
            'name' => 'Hibah Internal '.fake()->year(),
            'submission_starts_at' => now()->addDay(),
            'submission_ends_at' => now()->addMonth(),
            'quota' => 20,
            'budget' => 300000000,
            'requirements' => 'Ketua tim merupakan dosen aktif Kampus SBH.',
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }
}
