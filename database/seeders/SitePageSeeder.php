<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [

            /*
            |--------------------------------------------------------------------------
            | Home
            |--------------------------------------------------------------------------
            */

            [
                'slug' => 'home',

                'title' => 'Home',

                'content' => [

                    'hero' => [
                        'badge' => 'Clean Stack',
                        'heading' => 'Building clean, modern web experiences.',
                        'description' => 'We build modern websites and web applications focused on performance, usability, and clean code.',
                    ],

                    'services' => [
                        'badge' => 'Services',
                        'heading' => 'What we build.',
                        'description' => 'Practical web solutions built around your business goals.',
                    ],

                    'projects' => [
                        'badge' => 'Projects',
                        'heading' => 'Selected work.',
                        'description' => 'Real projects built with modern technologies, thoughtful design, and clean architecture.',
                    ],

                    'why_clean_stack' => [

                        'badge' => 'Why Clean Stack',

                        'heading' => 'Clean code. Clear thinking. Better products.',

                        'description' => 'We focus on building reliable digital products without unnecessary complexity.',

                        'items' => [

                            [
                                'icon' => 'code-bracket',
                                'title' => 'Clean Code',
                                'description' => 'Maintainable and well-structured code built for long-term development.',
                            ],

                            [
                                'icon' => 'bolt',
                                'title' => 'Performance',
                                'description' => 'Fast and responsive experiences designed for real users and real devices.',
                            ],

                            [
                                'icon' => 'light-bulb',
                                'title' => 'Practical Solutions',
                                'description' => 'Technology serves the product and the business, not the other way around.',
                            ],

                        ],
                    ],

                    'process' => [

                        'badge' => 'Process',

                        'heading' => 'From idea to launch.',

                        'description' => 'A straightforward process designed to keep projects focused and moving forward.',

                        'items' => [

                            [
                                'title' => 'Discover',
                                'description' => 'Understand the business, users, goals, and requirements.',
                            ],

                            [
                                'title' => 'Build',
                                'description' => 'Design and develop the solution with modern technologies and clean architecture.',
                            ],

                            [
                                'title' => 'Launch',
                                'description' => 'Test, refine, optimize, and prepare the product for real users.',
                            ],

                        ],
                    ],

                    'cta' => [
                        'badge' => "Let's build something useful.",
                        'heading' => 'Have a project in mind?',
                        'description' => "Tell us what you're building. We'll figure out the next step together.",
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | About
            |--------------------------------------------------------------------------
            */

            [
                'slug' => 'about',

                'title' => 'About',

                'content' => [

                    'hero' => [
                        'badge' => 'About Clean Stack',
                        'heading' => 'Building the web with purpose.',
                        'description' => 'Clean Stack is an independent web development brand focused on building clean, modern, and practical digital experiences.',
                    ],

                    'story' => [
                        'badge' => 'Our approach',
                        'heading' => 'Clean code is only part of the job.',
                        'description' => "We believe good web development is about more than writing code.\n\nIt is about understanding the problem, choosing the right tools, and building something that is reliable, useful, and easy to maintain.\n\nClean Stack focuses on practical solutions that balance technology, design, performance, and business goals.",
                    ],

                    'principles' => [

                        'badge' => 'Principles',

                        'heading' => 'How we build.',

                        'description' => 'A few principles guide the way we approach every project.',

                        'items' => [

                            [
                                'icon' => 'code-bracket',
                                'title' => 'Clean Code',
                                'description' => 'We write maintainable and well-structured code that remains easy to understand and improve.',
                            ],

                            [
                                'icon' => 'bolt',
                                'title' => 'Performance',
                                'description' => 'We build fast and responsive experiences with real users and real devices in mind.',
                            ],

                            [
                                'icon' => 'building-office-2',
                                'title' => 'Practical Solutions',
                                'description' => 'We choose technology based on the product and business needs, not trends.',
                            ],

                        ],
                    ],

                    'technology' => [
                        'badge' => 'Technology',
                        'heading' => 'Modern tools. Practical solutions.',
                        'description' => 'We use modern technologies that help us build reliable, maintainable, and high-quality web products.',

                        'items' => [
                            'Laravel',
                            'PHP',
                            'Livewire',
                            'Flux',
                            'Tailwind CSS',
                            'Alpine.js',
                            'MySQL',
                            'JavaScript',
                        ],
                    ],

                    'founder' => [
                        'badge' => 'Behind Clean Stack',
                        'heading' => 'One developer. One growing brand.',

                        'description' => [
                            'Clean Stack is an independent web development brand built around a simple idea: build useful things with clean technology and thoughtful execution.',
                            'The goal is to create digital products that are practical today and maintainable tomorrow.',
                        ],

                        'name' => 'Clean Stack',
                        'subtitle' => 'Independent Web Development',
                        'card_description' => 'Building clean, modern web experiences.',
                    ],

                    'cta' => [
                        'badge' => "Let's build",
                        'heading' => 'Have something worth building?',
                        'description' => "Tell us what you're working on and let's figure out the next step.",
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            [
                'slug' => 'contact',

                'title' => 'Contact',

                'content' => [

                    'hero' => [
                        'icon' => 'chat-bubble-left-right',
                        'badge' => 'Contact',
                        'heading' => 'Get in touch with Clean Stack.',
                        'description' => "Have a question, want to learn more about Clean Stack, or simply want to get in touch? We'd be happy to hear from you.",
                    ],

                    'contact_options' => [

                        [
                            'icon' => 'paper-airplane',
                            'title' => 'Telegram',
                            'description' => 'Follow Clean Stack or start a conversation directly on Telegram.',
                            'button' => 'Open Telegram',
                            'url' => 'https://t.me/CleanStack',
                        ],

                        [
                            'icon' => 'code-bracket',
                            'title' => 'GitHub',
                            'description' => "Explore Clean Stack's open-source projects and technical work.",
                            'button' => 'Visit GitHub',
                            'url' => 'https://github.com/Clean-Stack',
                        ],

                        [
                            'icon' => 'briefcase',
                            'title' => 'Have a project?',
                            'description' => "If you're looking to build or improve a website, let's talk about your project.",
                        ],

                    ],

                    'final_cta' => [
                        'heading' => 'Looking to build something?',
                        'description' => "Tell us about your project and we'll take it from there.",
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | Let's Talk
            |--------------------------------------------------------------------------
            */

            [
                'slug' => 'lets-talk',

                'title' => "Let's Talk",

                'content' => [

                    'hero' => [
                        'badge' => 'Start a Project',
                        'heading' => 'Have a project in mind?',
                        'description' => "Tell us what you're building, what you need, and where you'd like to go. We'll take it from there.",
                    ],

                    'project_inquiry' => [
                        'heading' => 'Tell us about your project.',
                        'description' => "A few details will help us understand what you're trying to build and how Clean Stack might help.",
                    ],

                    'checklist' => [

                        [
                            'icon' => 'check-badge',
                            'text' => "Tell us what you're trying to build.",
                        ],

                        [
                            'icon' => 'check-badge',
                            'text' => 'Share your current website if you have one.',
                        ],

                        [
                            'icon' => 'check-badge',
                            'text' => 'Give us enough context to understand your goals.',
                        ],

                    ],

                    'success' => [
                        'heading' => 'Thanks for reaching out.',
                        'description' => "Your project inquiry has been received. We'll review the details and get back to you.",
                        'button' => 'Send another inquiry',
                    ],

                ],
            ],

        ];


        foreach ($pages as $page) {

            SitePage::query()->updateOrCreate(
                [
                    'slug' => $page['slug'],
                ],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                ],
            );

        }
    }
}

