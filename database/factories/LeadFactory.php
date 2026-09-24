<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'company' => fake()->optional()->company(),
            'website' => fake()->optional()->url(),
            'service' => fake()->randomElement([
                'Web Development',
                'Laravel Development',
                'Custom Web Application',
                'Website Development',
                'UI/UX Development',
            ]),
            'message' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement([
                'new',
                'contacted',
                'qualified',
                'proposal',
                'won',
                'lost',
            ]),
        ];
    }
}
