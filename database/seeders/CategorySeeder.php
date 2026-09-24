<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
            ],
            [
                'name' => 'Livewire',
                'slug' => 'livewire',
            ],
            [
                'name' => 'Alpine.js',
                'slug' => 'alpine-js',
            ],
            [
                'name' => 'Clean Code',
                'slug' => 'clean-code',
            ],
            [
                'name' => 'Performance',
                'slug' => 'performance',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }
    }
}
