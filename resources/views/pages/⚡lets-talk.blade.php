<?php

use App\Models\Lead;
use App\Models\SitePage;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $company = '';

    public string $website = '';

    public string $service = 'Business Website';

    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'company' => [
                'nullable',
                'string',
                'max:150',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'service' => [
                'nullable',
                'string',
                'max:100',
            ],

            'message' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],
        ]);

        Lead::create([
            ...$validated,
            'status' => 'new',
        ]);

        $this->reset([
            'name',
            'email',
            'company',
            'website',
            'service',
            'message',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        $page = SitePage::query()
            ->where('slug', 'lets-talk')
            ->firstOrFail();

        $content = $page->content;

        $metaDescription =
            $content['hero']['description']
            ?? 'Tell Clean Stack about your project, goals, and web development needs.';

        $canonicalUrl = route('lets-talk');

        $contactPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => "Let's Talk — Clean Stack",
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

                'ogTitle' => "Let's Talk — Clean Stack",

                'ogDescription' => $metaDescription,

                'ogType' => 'website',

                'ogUrl' => $canonicalUrl,

                'twitterTitle' => "Let's Talk — Clean Stack",

                'twitterDescription' => $metaDescription,
            ])
            ->title("Let's Talk — Clean Stack");
    }
};
?>


<div>

    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section
        class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900"
    >

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
         PROJECT INQUIRY
         ========================================================= --}}

    <section>

        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">

            <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">

                {{-- INTRO --}}
                <div>

                    <flux:heading
                        level="2"
                        size="lg"
                    >
                        {{ $content['project_inquiry']['heading'] }}
                    </flux:heading>

                    <flux:text class="mt-4">
                        {{ $content['project_inquiry']['description'] }}
                    </flux:text>


                    <div class="mt-8 space-y-5">

                        @foreach ($content['checklist'] as $item)

                            <div class="flex gap-3">

                                <flux:icon
                                    :name="$item['icon']"
                                    class="mt-0.5 size-5"
                                />

                                <flux:text>
                                    {{ $item['text'] }}
                                </flux:text>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- FORM --}}
                <flux:card>

                    @if ($submitted)

                        <div class="py-10 text-center">

                            <flux:icon
                                name="check-circle"
                                class="mx-auto size-10"
                            />

                            <flux:heading
                                size="sm"
                                class="mt-5"
                            >
                                {{ $content['success']['heading'] }}
                            </flux:heading>

                            <flux:text class="mx-auto mt-3 max-w-md">
                                {{ $content['success']['description'] }}
                            </flux:text>

                            <div class="mt-6">

                                <flux:button
                                    wire:click="$set('submitted', false)"
                                    variant="subtle"
                                >
                                    {{ $content['success']['button'] }}
                                </flux:button>

                            </div>

                        </div>

                    @else

                        <form
                            wire:submit="submit"
                            class="space-y-6"
                        >

                            {{-- NAME --}}
                            <flux:field>

                                <flux:label>
                                    Name
                                </flux:label>

                                <flux:input
                                    wire:model="name"
                                    placeholder="Your name"
                                />

                                <flux:error name="name" />

                            </flux:field>


                            {{-- EMAIL --}}
                            <flux:field>

                                <flux:label>
                                    Email
                                </flux:label>

                                <flux:input
                                    type="text"
                                    wire:model="email"
                                    placeholder="you@example.com"
                                />

                                <flux:error name="email" />

                            </flux:field>


                            {{-- COMPANY --}}
                            <flux:field>

                                <flux:label>
                                    Company
                                </flux:label>

                                <flux:input
                                    wire:model="company"
                                    placeholder="Company name (optional)"
                                />

                                <flux:error name="company" />

                            </flux:field>


                            {{-- WEBSITE --}}
                            <flux:field>

                                <flux:label>
                                    Current website
                                </flux:label>

                                <flux:input
                                    type="text"
                                    wire:model="website"
                                    placeholder="https://example.com"
                                />

                                <flux:error name="website" />

                            </flux:field>


                            {{-- SERVICE --}}
                            <flux:field>

                                <flux:label>
                                    What do you need?
                                </flux:label>

                                <flux:select
                                    wire:model="service"
                                    variant="listbox"
                                    prefix="Service : "
                                >

                                    <flux:select.option value="Business Website">
                                        Business Website
                                    </flux:select.option>

                                    <flux:select.option value="Landing Page">
                                        Landing Page
                                    </flux:select.option>

                                    <flux:select.option value="Website Redesign">
                                        Website Redesign
                                    </flux:select.option>

                                    <flux:select.option value="Custom Web Application">
                                        Custom Web Application
                                    </flux:select.option>

                                    <flux:select.option value="Laravel Development">
                                        Laravel Development
                                    </flux:select.option>

                                    <flux:select.option value="Performance Optimization">
                                        Performance Optimization
                                    </flux:select.option>

                                    <flux:select.option value="Other">
                                        Other
                                    </flux:select.option>

                                </flux:select>

                                <flux:error name="service" />

                            </flux:field>


                            {{-- MESSAGE --}}
                            <flux:field>

                                <flux:label>
                                    Project details
                                </flux:label>

                                <flux:textarea
                                    wire:model="message"
                                    rows="7"
                                    placeholder="Tell us about your project, goals, timeline, or anything else that might help..."
                                />

                                <flux:error name="message" />

                            </flux:field>


                            {{-- SUBMIT --}}
                            <div>

                                <flux:button
                                    type="submit"
                                    variant="primary"
                                    wire:loading.attr="disabled"
                                    wire:target="submit"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="submit"
                                    >
                                        Send Project Inquiry
                                    </span>

                                    <span
                                        wire:loading
                                        wire:target="submit"
                                    >
                                        Sending...
                                    </span>

                                </flux:button>

                            </div>

                        </form>

                    @endif

                </flux:card>

            </div>

        </div>

    </section>

</div>

