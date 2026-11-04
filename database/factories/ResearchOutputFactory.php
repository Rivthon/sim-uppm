<?php

namespace Database\Factories;

use App\Models\FundingContract;
use App\Models\ResearchOutput;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchOutput>
 */
class ResearchOutputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'funding_contract_id' => FundingContract::factory(),
            'type' => 'jurnal',
            'title' => fake()->sentence(8),
            'achievement_status' => 'published',
            'identifier' => fake()->numerify('10.####/uppm.####'),
            'publisher' => fake()->company(),
            'publication_date' => now(),
            'url' => fake()->url(),
            'evidence_path' => 'research-outputs/evidence.pdf',
            'notes' => null,
            'verification_status' => 'pending',
            'verification_notes' => null,
            'created_by' => User::factory()->state(['roles' => ['dosen']]),
            'verified_by' => null,
            'verified_at' => null,
        ];
    }
}
