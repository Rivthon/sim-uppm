<?php

namespace Database\Factories;

use App\Models\PublicPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicPage>
 */
class PublicPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'title' => fake()->unique()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'summary' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'sort_order' => 0,
            'is_published' => true,
            'updated_by' => null,
        ];
    }
}
