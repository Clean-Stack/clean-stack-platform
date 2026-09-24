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
#[Title('Edit Contact Page')]
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
    | Contact Options
    |--------------------------------------------------------------------------
    */

    public array $contactOptions = [];

    public ?int $iconSelectionIndex = null;

    public bool $showIconModal = false;

    public bool $iconCopied = false;

    /*
    |--------------------------------------------------------------------------
    | Final CTA
    |--------------------------------------------------------------------------
    */

    public string $finalCtaHeading = '';

    public string $finalCtaDescription = '';

    public function mount(): void
    {
        $this->page = SitePage::query()
            ->where('slug', 'contact')
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
        | Contact Options
        |--------------------------------------------------------------------------
        */

        $this->contactOptions =
            $content['contact_options'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | Final CTA
        |--------------------------------------------------------------------------
        */

        $this->finalCtaHeading =
            $content['final_cta']['heading'] ?? '';

        $this->finalCtaDescription =
            $content['final_cta']['description'] ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Icon Picker
    |--------------------------------------------------------------------------
    */

    public function openIconModal(int $index): void
    {
        if (! isset($this->contactOptions[$index])) {
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

    public function updatedContactOptions(
        mixed $value,
        ?string $key
    ): void {
        if ($key === null || ! str_ends_with($key, '.icon')) {
            return;
        }

        $index = (int) str_replace('.icon', '', $key);

        if (! isset($this->contactOptions[$index])) {
            return;
        }

        $icon = $this->contactOptions[$index]['icon'] ?? null;

        $this->contactOptions[$index]['icon'] = $icon !== null
            ? trim($icon)
            : null;

        $this->validateOnly(
            "contactOptions.{$index}.icon",
            [
                "contactOptions.{$index}.icon" => [
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

        if (! isset($this->contactOptions[$index])) {
            return;
        }

        $this->contactOptions[$index]['icon'] = $icon;

        $this->resetValidation(
            "contactOptions.{$index}.icon"
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
        foreach ($this->contactOptions as $index => $option) {
            $this->contactOptions[$index]['icon'] =
                isset($option['icon'])
                    ? trim($option['icon'])
                    : '';

            $this->contactOptions[$index]['title'] =
                isset($option['title'])
                    ? trim($option['title'])
                    : '';

            $this->contactOptions[$index]['description'] =
                isset($option['description'])
                    ? trim($option['description'])
                    : '';

            if ($index === 0 || $index === 1) {
                $this->contactOptions[$index]['button'] =
                    isset($option['button'])
                        ? trim($option['button'])
                        : '';

                $this->contactOptions[$index]['url'] =
                    isset($option['url'])
                        ? trim($option['url'])
                        : '';
            }
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
                'max:2000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Contact Options
            |--------------------------------------------------------------------------
            */

            'contactOptions' => [
                'required',
                'array',
                'size:3',
            ],

            'contactOptions.*.icon' => [
                'required',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],

            'contactOptions.*.title' => [
                'required',
                'string',
                'max:100',
            ],

            'contactOptions.*.description' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Telegram
            |--------------------------------------------------------------------------
            */

            'contactOptions.0.button' => [
                'required',
                'string',
                'max:100',
            ],

            'contactOptions.0.url' => [
                'required',
                'url',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | GitHub
            |--------------------------------------------------------------------------
            */

            'contactOptions.1.button' => [
                'required',
                'string',
                'max:100',
            ],

            'contactOptions.1.url' => [
                'required',
                'url',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Final CTA
            |--------------------------------------------------------------------------
            */

            'finalCtaHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'finalCtaDescription' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $contactOptions = [
            [
                'icon' => $validated['contactOptions'][0]['icon'],
                'title' => $validated['contactOptions'][0]['title'],
                'description' => $validated['contactOptions'][0]['description'],
                'button' => $validated['contactOptions'][0]['button'],
                'url' => $validated['contactOptions'][0]['url'],
            ],

            [
                'icon' => $validated['contactOptions'][1]['icon'],
                'title' => $validated['contactOptions'][1]['title'],
                'description' => $validated['contactOptions'][1]['description'],
                'button' => $validated['contactOptions'][1]['button'],
                'url' => $validated['contactOptions'][1]['url'],
            ],

            [
                'icon' => $validated['contactOptions'][2]['icon'],
                'title' => $validated['contactOptions'][2]['title'],
                'description' => $validated['contactOptions'][2]['description'],
            ],
        ];

        $this->page->update([
            'content' => [
                'hero' => [
                    'icon' => 'chat-bubble-left-right',
                    'badge' => $validated['heroBadge'],
                    'heading' => $validated['heroHeading'],
                    'description' => $validated['heroDescription'],
                ],

                'contact_options' => $contactOptions,

                'final_cta' => [
                    'heading' => $validated['finalCtaHeading'],
                    'description' => $validated['finalCtaDescription'],
                ],
            ],
        ]);
        $this->redirectRoute('dashboard.site-pages.index', navigate: true);
        Flux::toast(
            heading: 'Contact page updated',
            text: 'The Contact page content has been saved successfully.',
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
            Edit Contact Page
        </flux:heading>


        <flux:text class="mt-1">
            Manage the content displayed on the public Contact page.
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
                            Main content displayed at the top of the Contact page.
                        </flux:text>

                    </div>


                    <flux:field>

                        <flux:label>
                            Badge
                        </flux:label>

                        <flux:input
                            wire:model="heroBadge"
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
                     CONTACT OPTIONS
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Contact Options
                            </flux:heading>

                            <flux:text class="mt-1">
                                The three contact options displayed on the Contact page.
                            </flux:text>

                        </div>


                        @foreach ($contactOptions as $index => $option)

                            <div
                                wire:key="contact-option-{{ $index }}"
                                class="space-y-4"
                            >

                                <flux:heading size="sm">
                                    Contact Option {{ $index + 1 }}
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
                                                wire:model.live.debounce.300ms="contactOptions.{{ $index }}.icon"
                                                placeholder="paper-airplane"
                                                :invalid="$errors->has('contactOptions.' . $index . '.icon')"
                                            />

                                        </div>


                                        @if ($this->isValidIcon($option['icon'] ?? null))

                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-700">

                                                <flux:icon
                                                    :name="$option['icon']"
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
                                        name="contactOptions.{{ $index }}.icon"
                                    />

                                </flux:field>


                                {{-- Title --}}
                                <flux:field>

                                    <flux:label>
                                        Title
                                    </flux:label>

                                    <flux:input
                                        wire:model="contactOptions.{{ $index }}.title"
                                        :invalid="$errors->has('contactOptions.' . $index . '.title')"
                                    />

                                    <flux:error
                                        name="contactOptions.{{ $index }}.title"
                                    />

                                </flux:field>


                                {{-- Description --}}
                                <flux:field>

                                    <flux:label>
                                        Description
                                    </flux:label>

                                    <flux:textarea
                                        wire:model="contactOptions.{{ $index }}.description"
                                        rows="4"
                                        :invalid="$errors->has('contactOptions.' . $index . '.description')"
                                    />

                                    <flux:error
                                        name="contactOptions.{{ $index }}.description"
                                    />

                                </flux:field>


                                {{-- Button --}}
                                @if ($index === 0 || $index === 1)

                                    <flux:field>

                                        <flux:label>
                                            Button
                                        </flux:label>

                                        <flux:input
                                            wire:model="contactOptions.{{ $index }}.button"
                                            :invalid="$errors->has('contactOptions.' . $index . '.button')"
                                        />

                                        <flux:error
                                            name="contactOptions.{{ $index }}.button"
                                        />

                                    </flux:field>

                                @endif


                                {{-- URL --}}
                                @if ($index === 0 || $index === 1)

                                    <flux:field>

                                        <flux:label>
                                            URL
                                        </flux:label>

                                        <flux:input
                                            type="url"
                                            wire:model="contactOptions.{{ $index }}.url"
                                            :invalid="$errors->has('contactOptions.' . $index . '.url')"
                                        />

                                        <flux:error
                                            name="contactOptions.{{ $index }}.url"
                                        />

                                    </flux:field>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- =====================================================
                     FINAL CTA
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Final CTA
                            </flux:heading>

                            <flux:text class="mt-1">
                                Final call to action displayed at the bottom of the Contact page.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="finalCtaHeading"
                                :invalid="$errors->has('finalCtaHeading')"
                            />

                            <flux:error name="finalCtaHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="finalCtaDescription"
                                rows="4"
                                :invalid="$errors->has('finalCtaDescription')"
                            />

                            <flux:error name="finalCtaDescription" />

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
                    Select an icon for this contact option.
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
