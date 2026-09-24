<?php

use App\Models\Service;
use Livewire\Component;

new class extends Component
{
    public Service $service;

    public function mount(Service $service): void
    {
        abort_unless(
            $service->is_active,
            404
        );

        $this->service = $service;
    }

    public function render()
    {
        $serviceTitle = $this->service->title . ' — Clean Stack';

        $serviceDescription = $this->service->short_description;

        $serviceUrl = route('services.show', $this->service);

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $this->service->title,
            'description' => $this->service->short_description,
            'url' => $serviceUrl,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Clean Stack',
            ],
        ];

        return $this->view()
            ->layout('layouts::public', [
                'metaDescription' => $serviceDescription,

                'robots' => 'index, follow',
                'canonicalUrl' => $serviceUrl,

                'ogTitle' => $serviceTitle,
                'ogDescription' => $serviceDescription,
                'ogType' => 'website',
                'ogUrl' => $serviceUrl,

                'twitterTitle' => $serviceTitle,
                'twitterDescription' => $serviceDescription,

                'serviceSchema' => $serviceSchema,
            ])
            ->title($serviceTitle);
    }
};
?>

<article class="mx-auto max-w-3xl px-6 py-16 lg:px-8">

    {{-- Back to Services --}}
    <flux:button
        href="{{ route('services.index') }}"
        variant="subtle"
        size="sm"
        wire:navigate
    >
        ← Back to Services
    </flux:button>


    {{-- Service Header --}}
    <header class="mt-8">

        @if ($service->icon)
            <div class="flex size-12 items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-700">
                <flux:icon
                    :name="$service->icon"
                    variant="outline"
                    class="size-6"
                />
            </div>
        @endif

        <flux:heading
            level="1"
            size="xl"
            class="{{ $service->icon ? 'mt-4' : '' }}"
        >
            {{ $service->title }}
        </flux:heading>

        <flux:text class="mt-6 text-lg">
            {{ $service->short_description }}
        </flux:text>

    </header>


    {{-- Service Content --}}
    <div class="mt-10">

        <div class="prose prose-zinc max-w-none dark:prose-invert">
            {!! $service->description !!}
        </div>

    </div>


    {{-- Call to Action --}}
    <div class="mt-12 border-t border-zinc-200 pt-8 dark:border-zinc-800">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <flux:heading size="lg">
                    Need this for your business?
                </flux:heading>

                <flux:text class="mt-1">
                    Let's discuss your project and find the right solution.
                </flux:text>
            </div>

            <flux:button
                href="{{ route('lets-talk') }}"
                variant="primary"
                wire:navigate
            >
                Start a project
            </flux:button>

        </div>

    </div>

</article>

