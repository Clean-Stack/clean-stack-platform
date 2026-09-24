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
#[Title('Edit Home Page')]
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
    | Services Section
    |--------------------------------------------------------------------------
    */

    public string $servicesBadge = '';

    public string $servicesHeading = '';

    public string $servicesDescription = '';

    /*
    |--------------------------------------------------------------------------
    | Projects Section
    |--------------------------------------------------------------------------
    */

    public string $projectsBadge = '';

    public string $projectsHeading = '';

    public string $projectsDescription = '';

    /*
    |--------------------------------------------------------------------------
    | Why Clean Stack
    |--------------------------------------------------------------------------
    */

    public string $whyCleanStackBadge = '';

    public string $whyCleanStackHeading = '';

    public string $whyCleanStackDescription = '';

    public array $whyCleanStackItems = [];

    public ?int $iconSelectionIndex = null;

    public bool $showIconModal = false;

    public bool $iconCopied = false;

    /*
    |--------------------------------------------------------------------------
    | Process
    |--------------------------------------------------------------------------
    */

    public string $processBadge = '';

    public string $processHeading = '';

    public string $processDescription = '';

    public array $processItems = [];

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
            ->where('slug', 'home')
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
        | Services
        |--------------------------------------------------------------------------
        */

        $this->servicesBadge =
            $content['services']['badge'] ?? '';

        $this->servicesHeading =
            $content['services']['heading'] ?? '';

        $this->servicesDescription =
            $content['services']['description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Projects
        |--------------------------------------------------------------------------
        */

        $this->projectsBadge =
            $content['projects']['badge'] ?? '';

        $this->projectsHeading =
            $content['projects']['heading'] ?? '';

        $this->projectsDescription =
            $content['projects']['description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Why Clean Stack
        |--------------------------------------------------------------------------
        */

        $this->whyCleanStackBadge =
            $content['why_clean_stack']['badge'] ?? '';

        $this->whyCleanStackHeading =
            $content['why_clean_stack']['heading'] ?? '';

        $this->whyCleanStackDescription =
            $content['why_clean_stack']['description'] ?? '';

        $this->whyCleanStackItems =
            $content['why_clean_stack']['items'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | Process
        |--------------------------------------------------------------------------
        */

        $this->processBadge =
            $content['process']['badge'] ?? '';

        $this->processHeading =
            $content['process']['heading'] ?? '';

        $this->processDescription =
            $content['process']['description'] ?? '';

        $this->processItems =
            $content['process']['items'] ?? [];

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
        if (! isset($this->whyCleanStackItems[$index])) {
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

    public function updatedWhyCleanStackItems(
        mixed $value,
        ?string $key
    ): void {
        if ($key === null || ! str_ends_with($key, '.icon')) {
            return;
        }

        $index = (int) str_replace('.icon', '', $key);

        if (! isset($this->whyCleanStackItems[$index])) {
            return;
        }

        $icon = $this->whyCleanStackItems[$index]['icon'] ?? null;

        $this->whyCleanStackItems[$index]['icon'] = $icon !== null
            ? trim($icon)
            : null;

        $this->validateOnly(
            "whyCleanStackItems.{$index}.icon",
            [
                "whyCleanStackItems.{$index}.icon" => [
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

        if (! isset($this->whyCleanStackItems[$index])) {
            return;
        }

        $this->whyCleanStackItems[$index]['icon'] = $icon;

        $this->resetValidation(
            "whyCleanStackItems.{$index}.icon"
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
        foreach ($this->whyCleanStackItems as $index => $item) {
            $this->whyCleanStackItems[$index]['icon'] =
                isset($item['icon'])
                    ? trim($item['icon'])
                    : '';
        }

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
            | Services
            |--------------------------------------------------------------------------
            */

            'servicesBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'servicesHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'servicesDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Projects
            |--------------------------------------------------------------------------
            */

            'projectsBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'projectsHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'projectsDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Why Clean Stack
            |--------------------------------------------------------------------------
            */

            'whyCleanStackBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'whyCleanStackHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'whyCleanStackDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            'whyCleanStackItems' => [
                'required',
                'array',
                'size:3',
            ],

            'whyCleanStackItems.*.icon' => [
                'required',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],

            'whyCleanStackItems.*.title' => [
                'required',
                'string',
                'max:100',
            ],

            'whyCleanStackItems.*.description' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Process
            |--------------------------------------------------------------------------
            */

            'processBadge' => [
                'required',
                'string',
                'max:100',
            ],

            'processHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'processDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            'processItems' => [
                'required',
                'array',
                'size:3',
            ],

            'processItems.*.title' => [
                'required',
                'string',
                'max:100',
            ],

            'processItems.*.description' => [
                'required',
                'string',
                'max:1000',
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

        $this->page->update([
            'content' => [
                'hero' => [
                    'badge' => $validated['heroBadge'],
                    'heading' => $validated['heroHeading'],
                    'description' => $validated['heroDescription'],
                ],

                'services' => [
                    'badge' => $validated['servicesBadge'],
                    'heading' => $validated['servicesHeading'],
                    'description' => $validated['servicesDescription'],
                ],

                'projects' => [
                    'badge' => $validated['projectsBadge'],
                    'heading' => $validated['projectsHeading'],
                    'description' => $validated['projectsDescription'],
                ],

                'why_clean_stack' => [
                    'badge' => $validated['whyCleanStackBadge'],
                    'heading' => $validated['whyCleanStackHeading'],
                    'description' => $validated['whyCleanStackDescription'],
                    'items' => $validated['whyCleanStackItems'],
                ],

                'process' => [
                    'badge' => $validated['processBadge'],
                    'heading' => $validated['processHeading'],
                    'description' => $validated['processDescription'],
                    'items' => $validated['processItems'],
                ],

                'cta' => [
                    'badge' => $validated['ctaBadge'],
                    'heading' => $validated['ctaHeading'],
                    'description' => $validated['ctaDescription'],
                ],
            ],
        ]);
        $this->redirectRoute('dashboard.site-pages.index', navigate: true);
        Flux::toast(
            heading: 'Home page updated',
            text: 'The Home page content has been saved successfully.',
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
            Edit Home Page
        </flux:heading>


        <flux:text class="mt-1">
            Manage the content displayed on the public Home page.
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
                            Main content displayed at the top of the Home page.
                        </flux:text>

                    </div>


                    <flux:field>

                        <flux:label>
                            Badge
                        </flux:label>

                        <flux:input
                            wire:model="heroBadge"
                            placeholder="Clean Stack"
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
                            placeholder="Building clean, modern web experiences."
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
                     SERVICES
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Services Section
                            </flux:heading>

                            <flux:text class="mt-1">
                                Introductory content above the featured services.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="servicesBadge"
                                :invalid="$errors->has('servicesBadge')"
                            />

                            <flux:error name="servicesBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="servicesHeading"
                                :invalid="$errors->has('servicesHeading')"
                            />

                            <flux:error name="servicesHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="servicesDescription"
                                rows="4"
                                :invalid="$errors->has('servicesDescription')"
                            />

                            <flux:error name="servicesDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     PROJECTS
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Projects Section
                            </flux:heading>

                            <flux:text class="mt-1">
                                Introductory content above the featured projects.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="projectsBadge"
                                :invalid="$errors->has('projectsBadge')"
                            />

                            <flux:error name="projectsBadge" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="projectsHeading"
                                :invalid="$errors->has('projectsHeading')"
                            />

                            <flux:error name="projectsHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="projectsDescription"
                                rows="4"
                                :invalid="$errors->has('projectsDescription')"
                            />

                            <flux:error name="projectsDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     WHY CLEAN STACK
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Why Clean Stack
                            </flux:heading>

                            <flux:text class="mt-1">
                                Content displayed in the Why Clean Stack section.
                            </flux:text>

                        </div>


                        {{-- Section Badge --}}
                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="whyCleanStackBadge"
                                placeholder="Why Clean Stack"
                                :invalid="$errors->has('whyCleanStackBadge')"
                            />

                            <flux:error name="whyCleanStackBadge" />

                        </flux:field>


                        {{-- Section Heading --}}
                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="whyCleanStackHeading"
                                placeholder="Clean code. Clear thinking. Better products."
                                :invalid="$errors->has('whyCleanStackHeading')"
                            />

                            <flux:error name="whyCleanStackHeading" />

                        </flux:field>


                        {{-- Section Description --}}
                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="whyCleanStackDescription"
                                rows="4"
                                :invalid="$errors->has('whyCleanStackDescription')"
                            />

                            <flux:error name="whyCleanStackDescription" />

                        </flux:field>


                        {{-- Principles --}}
                        <div class="space-y-6">

                            <div>

                                <flux:heading size="sm">
                                    Principles
                                </flux:heading>

                                <flux:text class="mt-1">
                                    The three principles displayed in this section.
                                </flux:text>

                            </div>


                            @foreach ($whyCleanStackItems as $index => $item)

                                <div
                                    wire:key="why-clean-stack-item-{{ $index }}"
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
                                                    wire:model.live.debounce.300ms="whyCleanStackItems.{{ $index }}.icon"
                                                    placeholder="code-bracket"
                                                    :invalid="$errors->has('whyCleanStackItems.' . $index . '.icon')"
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
                                            name="whyCleanStackItems.{{ $index }}.icon"
                                        />

                                    </flux:field>


                                    {{-- Title --}}
                                    <flux:field>

                                        <flux:label>
                                            Title
                                        </flux:label>

                                        <flux:input
                                            wire:model="whyCleanStackItems.{{ $index }}.title"
                                            :invalid="$errors->has('whyCleanStackItems.' . $index . '.title')"
                                        />

                                        <flux:error
                                            name="whyCleanStackItems.{{ $index }}.title"
                                        />

                                    </flux:field>


                                    {{-- Description --}}
                                    <flux:field>

                                        <flux:label>
                                            Description
                                        </flux:label>

                                        <flux:textarea
                                            wire:model="whyCleanStackItems.{{ $index }}.description"
                                            rows="4"
                                            :invalid="$errors->has('whyCleanStackItems.' . $index . '.description')"
                                        />

                                        <flux:error
                                            name="whyCleanStackItems.{{ $index }}.description"
                                        />

                                    </flux:field>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     PROCESS
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Process
                            </flux:heading>

                            <flux:text class="mt-1">
                                The three steps shown in the project process section.
                            </flux:text>

                        </div>


                        {{-- Section Badge --}}
                        <flux:field>

                            <flux:label>
                                Badge
                            </flux:label>

                            <flux:input
                                wire:model="processBadge"
                                placeholder="Process"
                                :invalid="$errors->has('processBadge')"
                            />

                            <flux:error name="processBadge" />

                        </flux:field>


                        {{-- Section Heading --}}
                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="processHeading"
                                placeholder="From idea to launch."
                                :invalid="$errors->has('processHeading')"
                            />

                            <flux:error name="processHeading" />

                        </flux:field>


                        {{-- Section Description --}}
                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="processDescription"
                                rows="4"
                                :invalid="$errors->has('processDescription')"
                            />

                            <flux:error name="processDescription" />

                        </flux:field>


                        @foreach ($processItems as $index => $item)

                            <div
                                wire:key="process-item-{{ $index }}"
                                class="space-y-4"
                            >

                                <flux:heading size="sm">
                                    Step {{ $index + 1 }}
                                </flux:heading>


                                <flux:field>

                                    <flux:label>
                                        Title
                                    </flux:label>

                                    <flux:input
                                        wire:model="processItems.{{ $index }}.title"
                                        :invalid="$errors->has('processItems.' . $index . '.title')"
                                    />

                                    <flux:error
                                        name="processItems.{{ $index }}.title"
                                    />

                                </flux:field>


                                <flux:field>

                                    <flux:label>
                                        Description
                                    </flux:label>

                                    <flux:textarea
                                        wire:model="processItems.{{ $index }}.description"
                                        rows="3"
                                        :invalid="$errors->has('processItems.' . $index . '.description')"
                                    />

                                    <flux:error
                                        name="processItems.{{ $index }}.description"
                                    />

                                </flux:field>

                            </div>

                        @endforeach

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
                                Final call to action displayed at the bottom of the Home page.
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
