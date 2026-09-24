<?php

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\Component;

new class extends Component
{
    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function render()
    {
        $projects = Project::query()
            ->where('is_published', true)
            ->latest()
            ->paginate($this->perPage);

        $projectsTitle = 'Projects — Clean Stack';

        $projectsDescription =
            'Explore selected projects built by Clean Stack with modern technologies, thoughtful design, and clean architecture.';

        $projectsUrl = route('projects.index');

        $featuredProject = $projects->firstWhere(
            fn (Project $project) => $project->cover_image !== null
        );

        $projectsImageUrl = $featuredProject?->cover_image
            ? url(Storage::url($featuredProject->cover_image))
            : null;

        $itemListElements = $projects->values()
            ->map(function (Project $project, int $index): array {
                $item = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $project->title,
                ];

                if ($project->cover_image) {
                    $item['image'] = url(
                        Storage::url($project->cover_image)
                    );
                }

                return $item;
            })
            ->all();

        $projectsSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $projectsTitle,
            'description' => $projectsDescription,
            'url' => $projectsUrl,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Clean Stack',
                'url' => url('/'),
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'name' => 'Clean Stack Projects',
                'numberOfItems' => $projects->total(),
                'itemListElement' => $itemListElements,
            ],
        ];

        if ($projectsImageUrl) {
            $projectsSchema['image'] = $projectsImageUrl;
        }

        return $this->view([
            'projects' => $projects,
        ])
            ->layout('layouts::public', [
                'metaDescription' => $projectsDescription,

                'robots' => 'index, follow',
                'canonicalUrl' => $projectsUrl,

                'ogTitle' => $projectsTitle,
                'ogDescription' => $projectsDescription,
                'ogType' => 'website',
                'ogUrl' => $projectsUrl,
                'ogImage' => $projectsImageUrl,

                'twitterTitle' => $projectsTitle,
                'twitterDescription' => $projectsDescription,
                'twitterImage' => $projectsImageUrl,

                'projectsSchema' => $projectsSchema,
            ])
            ->title($projectsTitle);
    }
};
?>

<div>

    {{-- PAGE HEADER --}}
    <section class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-3xl">

                <flux:badge
                    icon="folder"
                    variant="subtle"
                    rounded
                >
                    Projects
                </flux:badge>

                <flux:heading
                    level="1"
                    size="xl"
                    class="mt-6"
                >
                    Selected work.
                </flux:heading>

                <flux:text
                    size="lg"
                    class="mt-6 max-w-2xl"
                >
                    A selection of projects built with modern
                    technologies, thoughtful design, and clean architecture.
                </flux:text>

            </div>

        </div>

    </section>


    {{-- PROJECTS --}}
    <section>

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:heading
                    level="2"
                    size="lg"
                >
                    What we've built.
                </flux:heading>

                <flux:text class="mt-3">
                    Explore our published projects and see how
                    ideas become real digital products.
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @forelse ($projects as $project)

                    <flux:card class="flex h-full flex-col overflow-hidden">

                        {{-- PROJECT IMAGE --}}
                        @if ($project->cover_image)

                            <img
                                src="{{ Storage::url($project->cover_image) }}"
                                alt="{{ $project->title }}"
                                class="aspect-video w-full object-cover"
                            >

                        @else

                            <div class="flex aspect-video items-center justify-center bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon
                                    name="folder-git-2"
                                    class="size-10 text-zinc-400"
                                />
                            </div>

                        @endif


                        {{-- PROJECT CONTENT --}}
                        <div class="flex flex-1 flex-col p-6">

                            <flux:heading size="sm">
                                {{ $project->title }}
                            </flux:heading>

                            <flux:text class="mt-3">
                                {{ $project->summary }}
                            </flux:text>


                            {{-- ACTIONS --}}
                            @if ($project->live_url || $project->github_url)

                                <div class="mt-auto flex flex-wrap gap-2 pt-6">

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

                            @endif

                        </div>

                    </flux:card>

                @empty

                    <flux:card class="md:col-span-2 lg:col-span-3">

                        <div class="py-10 text-center">

                            <flux:icon
                                name="folder-open"
                                class="mx-auto size-8 text-zinc-400"
                            />

                            <flux:heading
                                size="sm"
                                class="mt-4"
                            >
                                No projects available
                            </flux:heading>

                            <flux:text class="mt-2">
                                Our projects will be available here soon.
                            </flux:text>

                        </div>

                    </flux:card>

                @endforelse

            </div>


            {{-- INFINITE LOADING --}}
            @if ($projects->hasMorePages())

                <div
                    wire:intersect="loadMore"
                    class="flex justify-center py-8"
                >
                    <flux:icon
                        name="arrow-path"
                        class="size-5 animate-spin"
                        wire:loading
                        wire:target="loadMore"
                    />
                </div>

            @endif

        </div>

    </section>


    {{-- CTA --}}
    <section class="border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="mx-auto max-w-3xl text-center">

                <flux:badge
                    variant="subtle"
                    rounded
                >
                    Start a project
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-5 text-zinc-900 dark:text-white"
                >
                    Have an idea worth building?
                </flux:heading>

                <flux:text
                    class="mx-auto mt-4 max-w-2xl text-zinc-600 dark:text-zinc-300"
                >
                    Tell us what you're building and let's
                    turn the idea into something useful.
                </flux:text>

                <div class="mt-8">

                    <flux:button
                        href="{{ route('lets-talk') }}"
                        navigate
                        variant="primary"
                    >
                        Let's Talk
                    </flux:button>

                </div>

            </div>

        </div>

    </section>

</div>

