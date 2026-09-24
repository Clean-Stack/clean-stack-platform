<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(4),
            'summary' => fake()->sentence(15),
            'description' => fake()->paragraphs(5, true),
            'cover_image' => null,
            'live_url' => null,
            'github_url' => null,
            'is_featured' => fake()->boolean(30),
            'is_published' => true,
        ];
    }
}
