<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\SitePage;
use Livewire\Component;

new class extends Component
{
    public function render()
    {
        $page = SitePage::query()
            ->where('slug', 'home')
            ->firstOrFail();

        $content = $page->content;

        $services = Service::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        $projects = Project::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        $metaDescription =
            $content['hero']['description']
            ?? 'We build modern websites and web applications focused on performance, usability, and clean code.';

        $canonicalUrl = route('home');

        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Clean Stack — Building clean, modern web experiences.',
            'description' => $metaDescription,
            'url' => $canonicalUrl,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Clean Stack',
                'url' => url('/'),
            ],
        ];

        return $this->view([
            'page' => $page,
            'content' => $content,
            'services' => $services,
            'projects' => $projects,
        ])
            ->layout('layouts::public', [
                'metaDescription' => $metaDescription,

                'robots' => 'index, follow',

                'canonicalUrl' => $canonicalUrl,

                'articleSchema' => $webPageSchema,

                'ogTitle' =>
                    'Clean Stack — Building clean, modern web experiences.',

                'ogDescription' => $metaDescription,

                'ogType' => 'website',

                'ogUrl' => $canonicalUrl,

                'twitterTitle' =>
                    'Clean Stack — Building clean, modern web experiences.',

                'twitterDescription' => $metaDescription,
            ])
            ->title('Clean Stack — Building clean, modern web experiences.');
    }
};
?>


<div>

    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section class="relative overflow-hidden">

        <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">

            <div class="mx-auto max-w-4xl text-center">

                <flux:badge
                    icon="sparkles"
                    variant="subtle"
                    rounded
                >
                    {{ $content['hero']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="1"
                    size="xl"
                    class="mt-6"
                >
                    {{ $content['hero']['heading'] }}
                </flux:heading>

                <flux:text
                    size="lg"
                    class="mx-auto mt-6 max-w-2xl"
                >
                    {{ $content['hero']['description'] }}
                </flux:text>

                <div class="mt-8 flex flex-wrap justify-center gap-3">

                    <flux:button
                        href="{{ route('projects.index') }}"
                        variant="primary"
                    >
                        View Projects
                    </flux:button>

                    <flux:button
                        href="{{ route('services.index') }}"
                        variant="ghost"
                    >
                        Explore Services
                    </flux:button>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         SERVICES
         ========================================================= --}}

    <section
        id="services"
        class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900"
    >

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['services']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-4"
                >
                    {{ $content['services']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['services']['description'] }}
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-3">

                @forelse ($services as $service)

                    <flux:card
                        wire:key="service-{{ $service->id }}"
                        class="flex h-full flex-col"
                    >

                        <div>
                            @if ($service->icon)

                                <flux:icon
                                    :name="$service->icon"
                                    class="size-7"
                                />

                            @else

                                <flux:icon
                                    name="code-bracket"
                                    class="size-7"
                                />

                            @endif
                        </div>


                        <div class="mt-6">

                            <flux:heading size="sm">
                                {{ $service->title }}
                            </flux:heading>

                            <flux:text class="mt-3">
                                {{ $service->short_description }}
                            </flux:text>

                        </div>


                        <div class="mt-auto pt-8">

                            <flux:button
                                href="{{ route('services.show', $service) }}"
                                variant="subtle"
                                size="sm"
                                wire:navigate
                            >
                                Learn more
                            </flux:button>

                        </div>

                    </flux:card>

                @empty

                    <flux:card class="md:col-span-3">

                        <flux:text>
                            Services are coming soon.
                        </flux:text>

                    </flux:card>

                @endforelse

            </div>

        </div>

    </section>


    {{-- =========================================================
         PROJECTS
         ========================================================= --}}

    <section
        id="projects"
        class="border-t border-zinc-200 dark:border-zinc-800"
    >

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['projects']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-4"
                >
                    {{ $content['projects']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['projects']['description'] }}
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @forelse ($projects as $project)

                    <flux:card class="flex flex-col overflow-hidden">

                        @if ($project->cover_image)

                            <img
                                src="{{ Storage::url($project->cover_image) }}"
                                alt="{{ $project->title }}"
                                class="aspect-video w-full object-cover"
                            >

                        @else

                            <div
                                class="flex aspect-video items-center justify-center
                                   bg-zinc-100 dark:bg-zinc-800"
                            >
                                <flux:icon
                                    name="code-bracket"
                                    class="size-8 text-zinc-400 dark:text-zinc-500"
                                />
                            </div>

                        @endif


                        <div class="flex flex-1 flex-col p-6">

                            <flux:heading size="sm">
                                {{ $project->title }}
                            </flux:heading>

                            <flux:text class="mt-3">
                                {{ $project->summary }}
                            </flux:text>

                            <div class="mt-6 flex flex-wrap gap-2">

                                @if ($project->live_url)

                                    <flux:button
                                        href="{{ $project->live_url }}"
                                        target="_blank"
                                        variant="primary"
                                        size="sm"
                                    >
                                        Live Demo
                                    </flux:button>

                                @endif

                                @if ($project->github_url)

                                    <flux:button
                                        href="{{ $project->github_url }}"
                                        target="_blank"
                                        variant="subtle"
                                        size="sm"
                                    >
                                        GitHub
                                    </flux:button>

                                @endif

                            </div>

                        </div>

                    </flux:card>

                @empty

                    <flux:card class="md:col-span-2 lg:col-span-3">

                        <flux:text>
                            Projects are coming soon.
                        </flux:text>

                    </flux:card>

                @endforelse

            </div>

        </div>

    </section>


    {{-- =========================================================
         WHY CLEAN STACK
         ========================================================= --}}

    <section
        id="about"
        class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900"
    >

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['why_clean_stack']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-4"
                >
                    {{ $content['why_clean_stack']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['why_clean_stack']['description'] }}
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-3">

                @foreach ($content['why_clean_stack']['items'] as $item)

                    <flux:card>

                        <flux:icon
                            :name="$item['icon']"
                            class="size-6"
                        />

                        <flux:heading
                            size="sm"
                            class="mt-5"
                        >
                            {{ $item['title'] }}
                        </flux:heading>

                        <flux:text class="mt-3">
                            {{ $item['description'] }}
                        </flux:text>

                    </flux:card>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =========================================================
         PROCESS
         ========================================================= --}}

    <section class="border-t border-zinc-200 dark:border-zinc-800">

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['process']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-4"
                >
                    {{ $content['process']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['process']['description'] }}
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-3">

                @foreach ($content['process']['items'] as $index => $step)

                    <flux:card>

                        <flux:badge variant="outline">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </flux:badge>

                        <flux:heading
                            size="sm"
                            class="mt-5"
                        >
                            {{ $step['title'] }}
                        </flux:heading>

                        <flux:text class="mt-3">
                            {{ $step['description'] }}
                        </flux:text>

                    </flux:card>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =========================================================
         CALL TO ACTION
         ========================================================= --}}

    <section
        id="contact"
        class="border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950"
    >

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="mx-auto max-w-3xl text-center">

                <flux:badge
                    variant="subtle"
                    rounded
                >
                    {{ $content['cta']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-5 text-zinc-900 dark:text-white"
                >
                    {{ $content['cta']['heading'] }}
                </flux:heading>

                <flux:text
                    class="mx-auto mt-4 max-w-2xl text-zinc-600 dark:text-zinc-300"
                >
                    {{ $content['cta']['description'] }}
                </flux:text>

                <div class="mt-8">

                    <flux:button
                        href="{{ route('lets-talk') }}"
                        wire:navigate
                        variant="primary"
                    >
                        Let's Talk
                    </flux:button>

                </div>

            </div>

        </div>

    </section>

</div>

