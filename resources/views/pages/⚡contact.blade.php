<?php

use App\Models\SitePage;
use Livewire\Component;

new class extends Component
{
    public function render()
    {
        $page = SitePage::query()
            ->where('slug', 'contact')
            ->firstOrFail();

        $content = $page->content;

        $metaDescription =
            $content['hero']['description']
            ?? 'Get in touch with Clean Stack about questions, collaborations, or web development projects.';

        $canonicalUrl = route('contact');

        $contactPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contact Clean Stack',
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

                'articleSchema' => $contactPageSchema,

                'ogTitle' => 'Contact — Clean Stack',

                'ogDescription' => $metaDescription,

                'ogType' => 'website',

                'ogUrl' => $canonicalUrl,

                'twitterTitle' => 'Contact — Clean Stack',

                'twitterDescription' => $metaDescription,
            ])
            ->title('Contact — Clean Stack');
    }
};
?>


<div>

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}

    <section
        class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900"
    >

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="max-w-3xl">

                <flux:badge
                    :icon="$content['hero']['icon']"
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
         CONTACT OPTIONS
         ========================================================= --}}

    <section>

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($content['contact_options'] as $index => $option)

                    <flux:card>

                        <flux:icon
                            :name="$option['icon']"
                            class="size-7"
                        />

                        <flux:heading
                            size="sm"
                            class="mt-6"
                        >
                            {{ $option['title'] }}
                        </flux:heading>

                        <flux:text class="mt-3">
                            {{ $option['description'] }}
                        </flux:text>


                        @if ($index < 2)

                            <div class="mt-6">

                                <flux:button
                                    href="{{ $option['url'] }}"
                                    target="_blank"
                                    variant="subtle"
                                >
                                    {{ $option['button'] }}
                                </flux:button>

                            </div>

                        @else

                            <div class="mt-6">

                                <flux:button
                                    href="{{ route('lets-talk') }}"
                                    wire:navigate
                                    variant="primary"
                                >
                                    Let's Talk
                                </flux:button>

                            </div>

                        @endif

                    </flux:card>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =========================================================
         FINAL CTA
         ========================================================= --}}

    <section
        class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900"
    >

        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

            <div class="mx-auto max-w-2xl text-center">

                <flux:heading
                    level="2"
                    size="lg"
                >
                    {{ $content['final_cta']['heading'] }}
                </flux:heading>

                <flux:text class="mt-4">
                    {{ $content['final_cta']['description'] }}
                </flux:text>

                <div class="mt-7">

                    <flux:button
                        href="{{ route('lets-talk') }}"
                        wire:navigate
                        variant="primary"
                    >
                        Start a Conversation
                    </flux:button>

                </div>

            </div>

        </div>

    </section>

</div>

