<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Business Website Development',
                'slug' => 'business-website-development',
                'short_description' => 'Modern, responsive websites designed around your business goals.',
                'description' => 'We build modern business websites that combine thoughtful design, clear content, responsive interfaces, and maintainable code. Every website is built with real users, business goals, and long-term maintainability in mind.',
                'icon' => 'globe-alt',
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'title' => 'Custom Web Application Development',
                'slug' => 'custom-web-application-development',
                'short_description' => 'Practical web applications built around your workflows and requirements.',
                'description' => 'We develop custom web applications for businesses that need more than a traditional website. From internal tools to customer-facing platforms, we focus on clear architecture, useful features, reliable functionality, and a maintainable codebase.',
                'icon' => 'squares-2x2',
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'title' => 'Laravel Development',
                'slug' => 'laravel-development',
                'short_description' => 'Robust Laravel applications built with clean architecture and practical solutions.',
                'description' => 'Laravel is our primary backend framework for building modern PHP applications. We use it to create reliable systems with clear application structure, well-organized business logic, secure authentication, database-driven features, and room for future growth.',
                'icon' => 'code-bracket',
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'title' => 'Livewire Interface Development',
                'slug' => 'livewire-interface-development',
                'short_description' => 'Fast, interactive interfaces without unnecessary frontend complexity.',
                'description' => 'We build dynamic interfaces with Laravel Livewire, combining server-side application logic with responsive user experiences. This approach helps keep projects simpler while still providing rich interactions, forms, dashboards, and business workflows.',
                'icon' => 'bolt',
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'title' => 'Website Redesign & Modernization',
                'slug' => 'website-redesign-modernization',
                'short_description' => 'Modernize outdated websites with better structure, usability, and performance.',
                'description' => 'We help improve existing websites that have become difficult to maintain, slow, outdated, or disconnected from current business needs. The focus is on improving structure, user experience, responsiveness, performance, and maintainability without adding unnecessary complexity.',
                'icon' => 'sparkles',
                'is_featured' => false,
                'is_active' => true,
            ],

            [
                'title' => 'Website Performance Optimization',
                'slug' => 'website-performance-optimization',
                'short_description' => 'Identify and improve the factors that make websites slower than they should be.',
                'description' => 'We analyze practical performance problems across the application, frontend, assets, images, database queries, and delivery process. The goal is a faster and more responsive experience without sacrificing maintainability or useful functionality.',
                'icon' => 'rocket-launch',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'title' => $service['title'],
                    'short_description' => $service['short_description'],
                    'description' => $service['description'],
                    'icon' => $service['icon'],
                    'is_featured' => $service['is_featured'],
                    'is_active' => $service['is_active'],
                ],
            );
        }
    }
}
