<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Clean Stack Business Platform',
                'slug' => 'clean-stack-business-platform',
                'summary' => 'A modern business platform built to showcase Clean Stack capabilities in web development.',
                'description' => 'The Clean Stack Business Platform is a portfolio-grade Laravel application built as the foundation of the Clean Stack brand. It combines a public business website with a custom administration panel for managing services, projects, blog content, site pages, and leads. The platform focuses on clean architecture, maintainable code, responsive interfaces, and practical business functionality.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => 'https://github.com/Clean-Stack',
                'is_featured' => true,
                'is_published' => true,
            ],

            [
                'title' => 'Business Website System',
                'slug' => 'business-website-system',
                'summary' => 'A flexible foundation for modern business websites focused on clarity, usability, and maintainability.',
                'description' => 'A business website concept designed around clear communication, responsive interfaces, structured content, and practical conversion paths. The project explores how a modern website can present services, projects, company information, and contact opportunities without unnecessary complexity.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => true,
                'is_published' => true,
            ],

            [
                'title' => 'Laravel Content Management System',
                'slug' => 'laravel-content-management-system',
                'summary' => 'A structured content management experience built around Laravel and modern server-driven interfaces.',
                'description' => 'A CMS concept focused on managing structured content through a clean administrative interface. The project explores content organization, publishing workflows, categories, media management, and maintainable Laravel application architecture.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => true,
                'is_published' => true,
            ],

            [
                'title' => 'Service Business Website',
                'slug' => 'service-business-website',
                'summary' => 'A focused website concept for service-based businesses that need a clear online presence.',
                'description' => 'A modern website concept for service businesses that need to communicate their value clearly and turn visitors into potential customers. The project focuses on service presentation, clear calls to action, responsive design, and a straightforward user experience.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => false,
                'is_published' => true,
            ],

            [
                'title' => 'Client Portal',
                'slug' => 'client-portal',
                'summary' => 'A practical authenticated portal concept for managing client information and workflows.',
                'description' => 'A client portal concept designed to provide authenticated users with a focused environment for accessing information and interacting with business workflows. The project explores authentication, permissions, dashboards, and structured client-facing interfaces.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => false,
                'is_published' => true,
            ],

            [
                'title' => 'Laravel Admin Dashboard',
                'slug' => 'laravel-admin-dashboard',
                'summary' => 'A clean administration interface for managing business data and application workflows.',
                'description' => 'An admin dashboard concept built around practical business operations. The interface focuses on clear navigation, structured data management, responsive layouts, forms, validation, and role-based access to administrative functionality.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => false,
                'is_published' => true,
            ],

            [
                'title' => 'Project Showcase Platform',
                'slug' => 'project-showcase-platform',
                'summary' => 'A portfolio-focused platform for presenting projects, technologies, and development work.',
                'description' => 'A project showcase concept designed to present digital work in a clear and professional way. The project explores structured project information, responsive layouts, technology presentation, and the relationship between portfolio content and business credibility.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => false,
                'is_published' => true,
            ],

            [
                'title' => 'Performance-Focused Web Experience',
                'slug' => 'performance-focused-web-experience',
                'summary' => 'A web experience focused on responsive interfaces, efficient assets, and practical performance.',
                'description' => 'A performance-focused web project exploring practical techniques for creating fast and responsive experiences. The project considers frontend assets, image delivery, page structure, efficient code, and the balance between visual quality and performance.',
                'cover_image' => null,
                'live_url' => null,
                'github_url' => null,
                'is_featured' => false,
                'is_published' => true,
            ],
        ];

        foreach ($projects as $project) {
            Project::query()->updateOrCreate(
                ['slug' => $project['slug']],
                [
                    'title' => $project['title'],
                    'summary' => $project['summary'],
                    'description' => $project['description'],
                    'cover_image' => $project['cover_image'],
                    'live_url' => $project['live_url'],
                    'github_url' => $project['github_url'],
                    'is_featured' => $project['is_featured'],
                    'is_published' => $project['is_published'],
                ],
            );
        }
    }
}
