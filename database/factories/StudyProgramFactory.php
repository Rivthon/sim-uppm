<?php

namespace Database\Factories;

use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyProgram>
 */
class StudyProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('PS-###'),
            'name' => fake()->unique()->words(2, true),
            'faculty' => fake()->words(2, true),
            'level' => 'S1',
            'is_active' => true,
        ];
    }
}
