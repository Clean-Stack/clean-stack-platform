<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(3),
            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(4, true),
            'icon' => null,
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
        ];
    }
}
