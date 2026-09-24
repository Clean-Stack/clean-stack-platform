<?php

use App\Models\SitePage;
use App\Support\ServiceIcons;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::app')]
#[Title('Edit About Page')]
class extends Component
{
    public SitePage $page;

    /*
    |--------------------------------------------------------------------------
    | Hero
    |--------------------------------------------------------------------------
    */

    public string $heroBadge = '';

    public string $heroHeading = '';

    public string $heroDescription = '';

    /*
    |--------------------------------------------------------------------------
    | Story
    |--------------------------------------------------------------------------
    */

    public string $storyBadge = '';

    public string $storyHeading = '';

    public string $storyDescription = '';

    /*
    |--------------------------------------------------------------------------
    | Principles
    |--------------------------------------------------------------------------
    */

    public string $principlesBadge = '';

    public string $principlesHeading = '';

    public string $principlesDescription = '';

    public array $principles = [];

    public ?int $iconSelectionIndex = null;

    public bool $showIconModal = false;

    public bool $iconCopied = false;

    /*
    |--------------------------------------------------------------------------
    | Technology
    |--------------------------------------------------------------------------
    */

    public string $technologyBadge = '';

    public string $technologyHeading = '';

    public string $technologyDescription = '';

    public string $technologyItems = '';

    /*
    |--------------------------------------------------------------------------
    | Founder
    |--------------------------------------------------------------------------
    */

    public string $founderBadge = '';

    public string $founderHeading = '';

    public array $founderDescriptions = [];

    public string $founderName = '';

    public string $founderSubtitle = '';

    public string $founderCardDescription = '';

    /*
    |--------------------------------------------------------------------------
    | CTA
    |--------------------------------------------------------------------------
    */

    public string $ctaBadge = '';

    public string $ctaHeading = '';

    public string $ctaDescription = '';

    public function mount(): void
    {
        $this->page = SitePage::query()
            ->where('slug', 'about')
            ->firstOrFail();

        $content = $this->page->content;

        /*
        |--------------------------------------------------------------------------
        | Hero
        |--------------------------------------------------------------------------
        */

        $this->heroBadge =
            $content['hero']['badge'] ?? '';

        $this->heroHeading =
            $content['hero']['heading'] ?? '';

        $this->heroDescription =
            $content['hero']['description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Story
        |--------------------------------------------------------------------------
        */

        $this->storyBadge =
            $content['story']['badge'] ?? '';

        $this->storyHeading =
            $content['story']['heading'] ?? '';

        $this->storyDescription =
            $content['story']['description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Principles
        |--------------------------------------------------------------------------
        */

        $this->principlesBadge =
            $content['principles']['badge'] ?? '';

        $this->principlesHeading =
            $content['principles']['heading'] ?? '';

        $this->principlesDescription =
            $content['principles']['description'] ?? '';

        $this->principles =
            $content['principles']['items'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | Technology
        |--------------------------------------------------------------------------
        */

        $this->technologyBadge =
            $content['technology']['badge'] ?? '';

        $this->technologyHeading =
            $content['technology']['heading'] ?? '';

        $this->technologyDescription =
            $content['technology']['description'] ?? '';

        $technologyItems =
            $content['technology']['items'] ?? [];

        $this->technologyItems = implode(
            ', ',
            array_map('trim', $technologyItems)
        );

        /*
        |--------------------------------------------------------------------------
        | Founder
        |--------------------------------------------------------------------------
        */

        $this->founderBadge =
            $content['founder']['badge'] ?? '';

        $this->founderHeading =
            $content['founder']['heading'] ?? '';

        $this->founderDescriptions =
            $content['founder']['description'] ?? [];

        $this->founderName =
            $content['founder']['name'] ?? '';

        $this->founderSubtitle =
            $content['founder']['subtitle'] ?? '';

        $this->founderCardDescription =
            $content['founder']['card_description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | CTA
        |--------------------------------------------------------------------------
        */

        $this->ctaBadge =
            $content['cta']['badge'] ?? '';

        $this->ctaHeading =
            $content['cta']['heading'] ?? '';

        $this->ctaDescription =
            $content['cta']['description'] ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Icon Picker
    |--------------------------------------------------------------------------
    */

    public function openIconModal(int $index): void
    {
        if (! isset($this->principles[$index])) {
            return;
        }

        $this->iconSelectionIndex = $index;

        $this->iconCopied = false;

        $this->showIconModal = true;
    }

    public function closeIconModal(): void
    {
        $this->showIconModal = false;

        $this->iconSelectionIndex = null;

        $this->iconCopied = false;
    }

    public function isValidIcon(?string $icon): bool
    {
        if ($icon === null || $icon === '') {
            return false;
        }

        return in_array(
            trim($icon),
            ServiceIcons::ALL,
            true
        );
    }

    public function updatedPrinciples(
        mixed $value,
        ?string $key
    ): void {
        if ($key === null || ! str_ends_with($key, '.icon')) {
            return;
        }

        $index = (int) str_replace('.icon', '', $key);

        if (! isset($this->principles[$index])) {
            return;
        }

        $icon = $this->principles[$index]['icon'] ?? null;

        $this->principles[$index]['icon'] = $icon !== null
            ? trim($icon)
            : null;

        $this->validateOnly(
            "principles.{$index}.icon",
            [
                "principles.{$index}.icon" => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in(ServiceIcons::ALL),
                ],
            ]
        );
    }

    public function selectIcon(string $icon): void
    {
        if ($this->iconSelectionIndex === null) {
            return;
        }

        if (! in_array($icon, ServiceIcons::ALL, true)) {
            return;
        }

        $index = $this->iconSelectionIndex;

        if (! isset($this->principles[$index])) {
            return;
        }

        $this->principles[$index]['icon'] = $icon;

        $this->resetValidation(
            "principles.{$index}.icon"
        );

        $this->iconCopied = true;

        $this->showIconModal = false;

        $this->iconSelectionIndex = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        foreach ($this->principles as $index => $item) {
            $this->principles[$index]['icon'] =
                isset($item['icon'])
                    ? trim($item['icon'])
                    : '';
        }

        $technologyItems = array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(',', $this->technologyItems)
                ),
                fn (string $item): bool => $item !== ''
            )
        );

        $validated = $this->validate([
            /*
            |--------------------------------------------------------------------------
            | Hero
            |--------------------------------------------------------------------------
            */

            'heroBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'heroHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'heroDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Story
            |--------------------------------------------------------------------------
            */

            'storyBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'storyHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'storyDescription' => [
                'required',
                'string',
                'max:6000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Principles
            |--------------------------------------------------------------------------
            */

            'principlesBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'principlesHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'principlesDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            'principles' => [
                'required',
                'array',
                'size:3',
            ],

            'principles.*.icon' => [
                'required',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],

            'principles.*.title' => [
                'required',
                'string',
                'max:100',
            ],

            'principles.*.description' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Technology
            |--------------------------------------------------------------------------
            */

            'technologyBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'technologyHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'technologyDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Founder
            |--------------------------------------------------------------------------
            */

            'founderBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'founderHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'founderDescriptions' => [
                'required',
                'array',
                'size:2',
            ],

            'founderDescriptions.*' => [
                'required',
                'string',
                'max:2000',
            ],

            'founderName' => [
                'required',
                'string',
                'max:100',
            ],

            'founderSubtitle' => [
                'required',
                'string',
                'max:150',
            ],

            'founderCardDescription' => [
                'required',
                'string',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | CTA
            |--------------------------------------------------------------------------
            */

            'ctaBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'ctaHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'ctaDescription' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        if (count($technologyItems) < 1) {
            $this->addError(
                'technologyItems',
                'Please enter at least one technology.'
            );

            return;
        }

        if (count($technologyItems) > 20) {
            $this->addError(
                'technologyItems',
                'You can enter up to 20 technologies.'
            );

            return;
        }

        foreach ($technologyItems as $technology) {
            if (mb_strlen($technology) > 100) {
                $this->addError(
                    'technologyItems',
                    'Each technology must be 100 characters or less.'
                );

                return;
            }
        }

        $this->page->update([
            'content' => [
                'hero' => [
                    'badge' => $validated['heroBadge'],
                    'heading' => $validated['heroHeading'],
                    'description' => $validated['heroDescription'],
                ],

                'story' => [
                    'badge' => $validated['storyBadge'],
                    'heading' => $validated['storyHeading'],
                    'description' => $validated['storyDescription'],
                ],

                'principles' => [
                    'badge' => $validated['principlesBadge'],
                    'heading' => $validated['principlesHeading'],
                    'description' => $validated['principlesDescription'],
                    'items' => $validated['principles'],
                ],

                'technology' => [
                    'badge' => $validated['technologyBadge'],
                    'heading' => $validated['technologyHeading'],
                    'description' => $validated['technologyDescription'],
                    'items' => $technologyItems,
                ],

                'founder' => [
                    'badge' => $validated['founderBadge'],
                    'heading' => $validated['founderHeading'],
                    'description' => $validated['founderDescriptions'],
                    'name' => $validated['founderName'],
                    'subtitle' => $validated['founderSubtitle'],
                    'card_description' => $validated['founderCardDescription'],
                ],

                'cta' => [
                    'badge' => $validated['ctaBadge'],
                    'heading' => $validated['ctaHeading'],
                    'description' => $validated['ctaDescription'],
                ],
            ],
        ]);

        $this->redirectRoute(
            'dashboard.site-pages.index',
            navigate: true
        );

        Flux::toast(
            heading: 'About page updated',
            text: 'The About page content has been saved successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>

        <flux:button
            variant="subtle"
            size="sm"
            href="{{ route('dashboard.site-pages.index') }}"
            wire:navigate
        >
            ← Back to Site Pages
        </flux:button>


        <flux:heading
            size="xl"
            class="mt-4"
        >
            Edit About Page
        </flux:heading>


        <flux:text class="mt-1">
            Manage the content displayed on the public About page.
        </flux:text>

    </div>


    {{-- Form --}}
    <div class="mt-6">

        <flux:card>

            <form wire:submit="save">

                {{-- =====================================================
                     HERO
                     ===================================================== --}}

                <div class="space-y-6">

                    <div>

                        <flux:heading size="lg">
                            Hero
                        </flux:heading>

                        <flux:text class="mt-1">
                            Main content displayed at the top of the About page.
                        </flux:text>

                    </div>


                    <flux:field>

                        <flux:label>
                            Badge
                        </flux:label>

                        <flux:input
                            wire:model="heroBadge"
                            placeholder="About Clean Stack"
                            :invalid="$errors->has('heroBadge')"
                        />

                        <flux:error name="heroBadge" />

                    </flux:field>


                    <flux:field>

                        <flux:label>
                            Heading
                        </flux:label>

                        <flux:input
                            wire:model="heroHeading"
                            :invalid="$errors->has('heroHeading')"
                        />

                        <flux:error name="heroHeading" />

                    </flux:field>


                    <flux:field>

                        <flux:label>
                            Description
                        </flux:label>

                        <flux:textarea
                            wire:model="heroDescription"
                            rows="4"
                            :invalid="$errors->has('heroDescription')"
                        />

                        <flux:error name="heroDescription" />

                    </flux:field>

                </div>


                {{-- =====================================================
                     STORY
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Story
                            </flux:heading>

                            <flux:text class="mt-1">
                                The main story and approach section.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="storyBadge"
                                :invalid="$errors->has('storyBadge')"
                            />

                            <flux:error name="storyBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="storyHeading"
                                :invalid="$errors->has('storyHeading')"
                            />

                            <flux:error name="storyHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Story
                            </flux:label>

                            <flux:textarea
                                wire:model="storyDescription"
                                rows="10"
                                :invalid="$errors->has('storyDescription')"
                            />

                            <flux:description>
                                Separate paragraphs with an empty line.
                            </flux:description>

                            <flux:error name="storyDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     PRINCIPLES
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Principles
                            </flux:heading>

                            <flux:text class="mt-1">
                                The three principles displayed on the About page.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="principlesBadge"
                                :invalid="$errors->has('principlesBadge')"
                            />

                            <flux:error name="principlesBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="principlesHeading"
                                :invalid="$errors->has('principlesHeading')"
                            />

                            <flux:error name="principlesHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="principlesDescription"
                                rows="4"
                                :invalid="$errors->has('principlesDescription')"
                            />

                            <flux:error name="principlesDescription" />

                        </flux:field>


                        @foreach ($principles as $index => $item)

                            <div
                                wire:key="principle-{{ $index }}"
                                class="space-y-4"
                            >

                                <flux:heading size="sm">
                                    Principle {{ $index + 1 }}
                                </flux:heading>


                                {{-- Icon --}}
                                <flux:field>

                                    <div class="flex items-center justify-between gap-4">

                                        <flux:label>
                                            Icon
                                        </flux:label>


                                        <flux:button
                                            type="button"
                                            variant="subtle"
                                            size="sm"
                                            icon="squares-2x2"
                                            wire:click="openIconModal({{ $index }})"
                                        >
                                            Choose Icon
                                        </flux:button>

                                    </div>


                                    <div class="flex items-start gap-3">

                                        <div class="flex-1">

                                            <flux:input
                                                wire:model.live.debounce.300ms="principles.{{ $index }}.icon"
                                                placeholder="code-bracket"
                                                :invalid="$errors->has('principles.' . $index . '.icon')"
                                            />

                                        </div>


                                        @if ($this->isValidIcon($item['icon'] ?? null))

                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-700">

                                                <flux:icon
                                                    :name="$item['icon']"
                                                    variant="outline"
                                                    class="size-5"
                                                />

                                            </div>

                                        @endif

                                    </div>


                                    <flux:description>
                                        Enter a valid Flux icon name or choose one from the icon library.
                                    </flux:description>


                                    <flux:error
                                        name="principles.{{ $index }}.icon"
                                    />

                                </flux:field>


                                {{-- Title --}}
                                <flux:field>

                                    <flux:label>
                                        Title
                                    </flux:label>

                                    <flux:input
                                        wire:model="principles.{{ $index }}.title"
                                        :invalid="$errors->has('principles.' . $index . '.title')"
                                    />

                                    <flux:error
                                        name="principles.{{ $index }}.title"
                                    />

                                </flux:field>


                                {{-- Description --}}
                                <flux:field>

                                    <flux:label>
                                        Description
                                    </flux:label>

                                    <flux:textarea
                                        wire:model="principles.{{ $index }}.description"
                                        rows="4"
                                        :invalid="$errors->has('principles.' . $index . '.description')"
                                    />

                                    <flux:error
                                        name="principles.{{ $index }}.description"
                                    />

                                </flux:field>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- =====================================================
                     TECHNOLOGY
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Technology
                            </flux:heading>

                            <flux:text class="mt-1">
                                Technology information displayed on the About page.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="technologyBadge"
                                :invalid="$errors->has('technologyBadge')"
                            />

                            <flux:error name="technologyBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="technologyHeading"
                                :invalid="$errors->has('technologyHeading')"
                            />

                            <flux:error name="technologyHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="technologyDescription"
                                rows="4"
                                :invalid="$errors->has('technologyDescription')"
                            />

                            <flux:error name="technologyDescription" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Technologies
                            </flux:label>

                            <flux:textarea
                                wire:model="technologyItems"
                                rows="5"
                                placeholder="Laravel, PHP, Livewire, Flux, Tailwind CSS, Alpine.js, MySQL, JavaScript"
                                :invalid="$errors->has('technologyItems')"
                            />

                            <flux:description>
                                Separate technologies with commas.
                            </flux:description>

                            <flux:error name="technologyItems" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     FOUNDER
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Founder
                            </flux:heading>

                            <flux:text class="mt-1">
                                Content displayed in the About page founder section.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="founderBadge"
                                :invalid="$errors->has('founderBadge')"
                            />

                            <flux:error name="founderBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="founderHeading"
                                :invalid="$errors->has('founderHeading')"
                            />

                            <flux:error name="founderHeading" />

                        </flux:field>


                        @foreach ($founderDescriptions as $index => $description)

                            <flux:field
                                wire:key="founder-description-{{ $index }}"
                            >

                                <flux:label>
                                    Paragraph {{ $index + 1 }}
                                </flux:label>

                                <flux:textarea
                                    wire:model="founderDescriptions.{{ $index }}"
                                    rows="5"
                                    :invalid="$errors->has('founderDescriptions.' . $index)"
                                />

                                <flux:error
                                    name="founderDescriptions.{{ $index }}"
                                />

                            </flux:field>

                        @endforeach


                        <flux:field>

                            <flux:label>
                                Name
                            </flux:label>

                            <flux:input
                                wire:model="founderName"
                                :invalid="$errors->has('founderName')"
                            />

                            <flux:error name="founderName" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Subtitle
                            </flux:label>

                            <flux:input
                                wire:model="founderSubtitle"
                                :invalid="$errors->has('founderSubtitle')"
                            />

                            <flux:error name="founderSubtitle" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Card Description
                            </flux:label>

                            <flux:textarea
                                wire:model="founderCardDescription"
                                rows="3"
                                :invalid="$errors->has('founderCardDescription')"
                            />

                            <flux:error name="founderCardDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     CTA
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Call to Action
                            </flux:heading>

                            <flux:text class="mt-1">
                                Final call to action displayed at the bottom of the About page.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="ctaBadge"
                                :invalid="$errors->has('ctaBadge')"
                            />

                            <flux:error name="ctaBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="ctaHeading"
                                :invalid="$errors->has('ctaHeading')"
                            />

                            <flux:error name="ctaHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="ctaDescription"
                                rows="4"
                                :invalid="$errors->has('ctaDescription')"
                            />

                            <flux:error name="ctaDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     ACTIONS
                     ===================================================== --}}

                <div class="flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">

                    <flux:button
                        variant="subtle"
                        href="{{ route('dashboard.site-pages.index') }}"
                        wire:navigate
                    >
                        Cancel
                    </flux:button>


                    <flux:button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >

                        <span
                            wire:loading.remove
                            wire:target="save"
                        >
                            Save Changes
                        </span>


                        <span
                            wire:loading
                            wire:target="save"
                        >
                            Saving...
                        </span>

                    </flux:button>

                </div>

            </form>

        </flux:card>

    </div>


    {{-- =========================================================
         ICON PICKER MODAL
         ========================================================= --}}

    <flux:modal
        wire:model.self="showIconModal"
        class="w-full max-w-4xl"
    >

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Choose an Icon
                </flux:heading>

                <flux:text class="mt-1">
                    Select an icon for this principle.
                </flux:text>

            </div>


            <div class="grid max-h-[60vh] grid-cols-3 gap-2 overflow-y-auto sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">

                @foreach (ServiceIcons::ALL as $serviceIcon)

                    <button
                        type="button"
                        class="group flex min-h-24 flex-col items-center justify-center gap-2 rounded-lg border border-zinc-200 p-3 text-center transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800"
                        x-data
                        @click="
                            navigator.clipboard.writeText(@js($serviceIcon));
                            $wire.selectIcon(@js($serviceIcon));
                        "
                    >

                        <flux:icon
                            :name="$serviceIcon"
                            variant="outline"
                            class="size-6 transition group-hover:scale-110"
                        />


                        <span class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $serviceIcon }}
                        </span>

                    </button>

                @endforeach

            </div>


            <div class="flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-700">

                <div class="text-sm">

                    @if ($iconCopied)

                        <span class="text-zinc-600 dark:text-zinc-400">
                            Copied!
                        </span>

                    @else

                        <span class="text-zinc-500 dark:text-zinc-400">
                            Click on the icon to select it.
                        </span>

                    @endif

                </div>


                <flux:modal.close>

                    <flux:button variant="ghost">
                        Close
                    </flux:button>

                </flux:modal.close>

            </div>

        </div>

    </flux:modal>

</div>

