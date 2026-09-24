<?php

use App\Models\SitePage;
use Livewire\Component;

new class extends Component
{
    public function render()
    {
        $page = SitePage::query()
            ->where('slug', 'about')
            ->firstOrFail();

        $content = $page->content;

        $metaDescription =
            $content['hero']['description']
            ?? 'Clean Stack is an independent web development brand focused on building clean, modern, and practical digital experiences.';

        $canonicalUrl = route('about');

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => 'About Clean Stack',
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
        ])
            ->layout('layouts::public', [
                'metaDescription' => $metaDescription,

                'robots' => 'index, follow',

                'canonicalUrl' => $canonicalUrl,

                'articleSchema' => $articleSchema,

                'ogTitle' => 'About Clean Stack',

                'ogDescription' => $metaDescription,

                'ogType' => 'website',

                'ogUrl' => $canonicalUrl,

                'twitterTitle' => 'About Clean Stack',

                'twitterDescription' => $metaDescription,
            ])
            ->title('About — Clean Stack');
    }
};
?>


<div>

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}

    <section class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-3xl">

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
                    class="mt-6 max-w-2xl"
                >
                    {{ $content['hero']['description'] }}
                </flux:text>

            </div>

        </div>

    </section>


    {{-- =========================================================
         STORY
         ========================================================= --}}

    <section>

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="grid gap-12 lg:grid-cols-2 lg:items-start">

                {{-- Left --}}
                <div>

                    <flux:badge variant="subtle">
                        {{ $content['story']['badge'] }}
                    </flux:badge>

                    <flux:heading
                        level="2"
                        size="lg"
                        class="mt-5"
                    >
                        {{ $content['story']['heading'] }}
                    </flux:heading>

                </div>


                {{-- Right --}}
                <div>

                    <flux:text
                        size="lg"
                        class="whitespace-pre-line"
                    >
                        {{ $content['story']['description'] }}
                    </flux:text>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         PRINCIPLES
         ========================================================= --}}

    <section class="border-y border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['principles']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-5"
                >
                    {{ $content['principles']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['principles']['description'] }}
                </flux:text>

            </div>


            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($content['principles']['items'] as $principle)

                    <flux:card>

                        <flux:icon
                            :name="$principle['icon']"
                            class="size-7"
                        />

                        <flux:heading
                            size="sm"
                            class="mt-6"
                        >
                            {{ $principle['title'] }}
                        </flux:heading>

                        <flux:text class="mt-3">
                            {{ $principle['description'] }}
                        </flux:text>

                    </flux:card>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =========================================================
         TECHNOLOGY
         ========================================================= --}}

    <section>

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-2xl">

                <flux:badge variant="subtle">
                    {{ $content['technology']['badge'] }}
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-5"
                >
                    {{ $content['technology']['heading'] }}
                </flux:heading>

                <flux:text class="mt-3">
                    {{ $content['technology']['description'] }}
                </flux:text>

            </div>


            <div class="mt-8 flex flex-wrap gap-2">

                @foreach ($content['technology']['items'] as $technology)

                    <flux:badge>
                        {{ $technology }}
                    </flux:badge>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =========================================================
         FOUNDER
         ========================================================= --}}

    <section class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="grid gap-12 lg:grid-cols-2 lg:items-center">

                <div>

                    <flux:badge variant="subtle">
                        {{ $content['founder']['badge'] }}
                    </flux:badge>

                    <flux:heading
                        level="2"
                        size="lg"
                        class="mt-5"
                    >
                        {{ $content['founder']['heading'] }}
                    </flux:heading>


                    @foreach ($content['founder']['description'] as $description)

                        <flux:text
                            @class([
                                'mt-5' => $loop->first,
                                'mt-4' => ! $loop->first,
                            ])
                        >
                            {{ $description }}
                        </flux:text>

                    @endforeach

                </div>


                <flux:card>

                    <div class="flex items-center gap-4">
                        <div class="flex aspect-square size-9 items-center justify-center rounded-md bg-accent-content">
                            <x-app-logo-icon class="block max-w-full max-h-full w-auto h-auto text-white dark:text-black" />
                        </div>


                        <div>

                            <flux:heading size="sm">
                                {{ $content['founder']['name'] }}
                            </flux:heading>

                            <flux:text>
                                {{ $content['founder']['subtitle'] }}
                            </flux:text>

                        </div>

                    </div>


                    <flux:text class="mt-6">
                        {{ $content['founder']['card_description'] }}
                    </flux:text>

                </flux:card>

            </div>

        </div>

    </section>


    {{-- =========================================================
         CTA
         ========================================================= --}}

    <section class="border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950">

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

