<?php

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

new
class extends Component
{
    use WithPagination;

    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function with(): array
    {
        $servicesUrl = route('services.index');

        $servicesDescription =
            'Explore Clean Stack services for modern websites and custom web applications, focused on performance, usability, and long-term maintainability.';

        $servicesSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Services — Clean Stack',
            'description' => $servicesDescription,
            'url' => $servicesUrl,
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => Service::query()
                    ->where('is_active', true)
                    ->latest()
                    ->get(['id', 'title', 'slug'])
                    ->values()
                    ->map(function (Service $service, int $index): array {
                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'name' => $service->title,
                            'url' => route('services.show', $service),
                        ];
                    })
                    ->all(),
            ],
        ];

        return [
            'services' => Service::query()
                ->where('is_active', true)
                ->latest()
                ->paginate($this->perPage),

            'servicesSchema' => $servicesSchema,
        ];
    }

    public function render()
    {
        $servicesDescription =
            'Explore Clean Stack services for modern websites and custom web applications, focused on performance, usability, and long-term maintainability.';

        $servicesUrl = route('services.index');

        return $this->view()
            ->layout('layouts::public', [
                'metaDescription' => $servicesDescription,

                'robots' => 'index, follow',

                'canonicalUrl' => $servicesUrl,

                'ogTitle' => 'Services — Clean Stack',
                'ogDescription' => $servicesDescription,
                'ogType' => 'website',
                'ogUrl' => $servicesUrl,

                'twitterTitle' => 'Services — Clean Stack',
                'twitterDescription' => $servicesDescription,
            ])
            ->title('Services — Clean Stack');
    }
};
?>

<div>
    {{-- PAGE HEADER --}}
    <section class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="max-w-3xl">
                <flux:badge icon="sparkles" variant="subtle" rounded>
                    Services
                </flux:badge>

                <flux:heading level="1" size="xl" class="mt-6">
                    Web solutions built for real businesses.
                </flux:heading>

                <flux:text size="lg" class="mt-6 max-w-2xl">
                    From modern websites to custom web applications,
                    we build practical digital solutions focused on
                    performance, usability, and long-term maintainability.
                </flux:text>
            </div>
        </div>
    </section>

    {{-- SERVICES --}}
    <section>
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="max-w-2xl">
                <flux:heading level="2" size="lg">
                    What we can build.
                </flux:heading>

                <flux:text class="mt-3">
                    Explore our services and find the right solution
                    for your next project.
                </flux:text>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @if ($services->isNotEmpty())

                    @foreach ($services as $service)

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

                    @endforeach

                @else

                    <flux:card class="md:col-span-2 lg:col-span-3">

                        <div class="py-8 text-center">

                            <flux:heading size="sm">
                                No services available
                            </flux:heading>

                            <flux:text class="mt-2">
                                Our services will be available here soon.
                            </flux:text>

                        </div>

                    </flux:card>

                @endif
            </div>


            {{-- Infinite Load Trigger --}}
            @if ($services->hasMorePages())

                <div
                    class="mt-10 flex justify-center"
                    wire:intersect="loadMore"
                >

                    <flux:button
                        variant="subtle"
                        wire:loading
                        wire:target="loadMore"
                        disabled
                    >
                        Loading...
                    </flux:button>

                </div>

            @endif


            {{-- Loading Indicator --}}
            <div
                wire:loading
                wire:target="loadMore"
                class="mt-6 flex justify-center"
            >

                <flux:text size="sm">
                    Loading more services...
                </flux:text>

            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">

                <flux:badge variant="subtle" rounded>
                    Start a project
                </flux:badge>

                <flux:heading
                    level="2"
                    size="lg"
                    class="mt-5 text-zinc-900 dark:text-white"
                >
                    Have an idea worth building?
                </flux:heading>

                <flux:text class="mx-auto mt-4 max-w-2xl text-zinc-600 dark:text-zinc-300">
                    Tell us what you need and let's figure out
                    the right solution for your project.
                </flux:text>

                <div class="mt-8">

                    <flux:button
                        href="{{ route('lets-talk') }}"
                        variant="primary"
                        wire:navigate
                    >
                        Let's Talk
                    </flux:button>

                </div>

            </div>
        </div>
    </section>
</div>
