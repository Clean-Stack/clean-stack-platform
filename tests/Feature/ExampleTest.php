<?php

namespace Tests\Feature;

use App\Models\SitePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered(): void
    {
        SitePage::create([
            'slug' => 'home',
            'title' => 'Home',
            'content' => [
                'hero' => [
                    'badge' => 'Clean Stack',
                    'heading' => 'Building clean, modern web experiences.',
                    'description' => 'Modern websites built with clean code.',
                ],
                'services' => [
                    'badge' => 'Services',
                    'heading' => 'What I build',
                    'description' => 'Web development services.',
                ],
                'projects' => [
                    'badge' => 'Projects',
                    'heading' => 'Selected projects',
                    'description' => 'A selection of projects.',
                ],
                'why_clean_stack' => [
                    'badge' => 'Why Clean Stack',
                    'heading' => 'Why Clean Stack',
                    'description' => 'Clean and practical development.',
                    'items' => [],
                ],
                'process' => [
                    'badge' => 'Process',
                    'heading' => 'How I work',
                    'description' => 'A practical process.',
                    'items' => [],
                ],
                'cta' => [
                    'badge' => 'Let’s work together',
                    'heading' => 'Have a project in mind?',
                    'description' => 'Let’s talk.',
                ],
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
    }
}
