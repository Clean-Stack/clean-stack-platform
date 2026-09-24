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
#[Title("Edit Let's Talk Page")]
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
    | Project Inquiry
    |--------------------------------------------------------------------------
    */

    public string $projectInquiryHeading = '';

    public string $projectInquiryDescription = '';

    /*
    |--------------------------------------------------------------------------
    | Checklist
    |--------------------------------------------------------------------------
    */

    public array $checklist = [];

    public ?int $iconSelectionIndex = null;

    public bool $showIconModal = false;

    public bool $iconCopied = false;

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    public string $successHeading = '';

    public string $successDescription = '';

    public string $successButton = '';

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->page = SitePage::query()
            ->where('slug', 'lets-talk')
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
        | Project Inquiry
        |--------------------------------------------------------------------------
        */

        $this->projectInquiryHeading =
            $content['project_inquiry']['heading'] ?? '';

        $this->projectInquiryDescription =
            $content['project_inquiry']['description'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Checklist
        |--------------------------------------------------------------------------
        */

        $this->checklist =
            $content['checklist'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $this->successHeading =
            $content['success']['heading'] ?? '';

        $this->successDescription =
            $content['success']['description'] ?? '';

        $this->successButton =
            $content['success']['button'] ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Icon Picker
    |--------------------------------------------------------------------------
    */

    public function openIconModal(int $index): void
    {
        if (! isset($this->checklist[$index])) {
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

    public function updatedChecklist(
        mixed $value,
        ?string $key
    ): void {
        if ($key === null || ! str_ends_with($key, '.icon')) {
            return;
        }

        $index = (int) str_replace('.icon', '', $key);

        if (! isset($this->checklist[$index])) {
            return;
        }

        $icon = $this->checklist[$index]['icon'] ?? null;

        $this->checklist[$index]['icon'] = $icon !== null
            ? trim($icon)
            : null;

        $this->validateOnly(
            "checklist.{$index}.icon",
            [
                "checklist.{$index}.icon" => [
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

        if (! isset($this->checklist[$index])) {
            return;
        }

        $this->checklist[$index]['icon'] = $icon;

        $this->resetValidation(
            "checklist.{$index}.icon"
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
        foreach ($this->checklist as $index => $item) {
            $this->checklist[$index]['icon'] =
                isset($item['icon'])
                    ? trim($item['icon'])
                    : '';

            $this->checklist[$index]['text'] =
                isset($item['text'])
                    ? trim($item['text'])
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
            | Project Inquiry
            |--------------------------------------------------------------------------
            */

            'projectInquiryHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'projectInquiryDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Checklist
            |--------------------------------------------------------------------------
            */

            'checklist' => [
                'required',
                'array',
                'size:3',
            ],

            'checklist.*.icon' => [
                'required',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],

            'checklist.*.text' => [
                'required',
                'string',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            'successHeading' => [
                'required',
                'string',
                'max:255',
            ],

            'successDescription' => [
                'required',
                'string',
                'max:1000',
            ],

            'successButton' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $this->page->update([
            'content' => [
                'hero' => [
                    'badge' => $validated['heroBadge'],
                    'heading' => $validated['heroHeading'],
                    'description' => $validated['heroDescription'],
                ],

                'project_inquiry' => [
                    'heading' => $validated['projectInquiryHeading'],
                    'description' => $validated['projectInquiryDescription'],
                ],

                'checklist' => $validated['checklist'],

                'success' => [
                    'heading' => $validated['successHeading'],
                    'description' => $validated['successDescription'],
                    'button' => $validated['successButton'],
                ],
            ],
        ]);
        $this->redirectRoute('dashboard.site-pages.index', navigate: true);
        Flux::toast(
            heading: "Let's Talk page updated",
            text: "The Let's Talk page content has been saved successfully.",
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
            Edit Let's Talk Page
        </flux:heading>


        <flux:text class="mt-1">
            Manage the content displayed on the public Let's Talk page.
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
                            Main content displayed at the top of the Let's Talk page.
                        </flux:text>

                    </div>


                    <flux:field>

                        <flux:label>
                            Badge
                        </flux:label>

                        <flux:input
                            wire:model="heroBadge"
                            placeholder="Start a Project"
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
                     PROJECT INQUIRY
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Project Inquiry
                            </flux:heading>

                            <flux:text class="mt-1">
                                Introductory content displayed above the project inquiry form.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="projectInquiryHeading"
                                :invalid="$errors->has('projectInquiryHeading')"
                            />

                            <flux:error name="projectInquiryHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="projectInquiryDescription"
                                rows="5"
                                :invalid="$errors->has('projectInquiryDescription')"
                            />

                            <flux:error name="projectInquiryDescription" />

                        </flux:field>

                    </div>

                </div>


                {{-- =====================================================
                     CHECKLIST
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Checklist
                            </flux:heading>

                            <flux:text class="mt-1">
                                The three points displayed beside the project inquiry form.
                            </flux:text>

                        </div>


                        @foreach ($checklist as $index => $item)

                            <div
                                wire:key="checklist-{{ $index }}"
                                class="space-y-4"
                            >

                                <flux:heading size="sm">
                                    Item {{ $index + 1 }}
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
                                                wire:model.live.debounce.300ms="checklist.{{ $index }}.icon"
                                                placeholder="check-circle"
                                                :invalid="$errors->has('checklist.' . $index . '.icon')"
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
                                        name="checklist.{{ $index }}.icon"
                                    />

                                </flux:field>


                                {{-- Text --}}
                                <flux:field>

                                    <flux:label>
                                        Text
                                    </flux:label>

                                    <flux:textarea
                                        wire:model="checklist.{{ $index }}.text"
                                        rows="3"
                                        :invalid="$errors->has('checklist.' . $index . '.text')"
                                    />

                                    <flux:error
                                        name="checklist.{{ $index }}.text"
                                    />

                                </flux:field>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- =====================================================
                     SUCCESS
                     ===================================================== --}}

                <div class="pt-8">

                    <div class="space-y-6">

                        <div>

                            <flux:heading size="lg">
                                Success
                            </flux:heading>

                            <flux:text class="mt-1">
                                Content displayed after a project inquiry is successfully submitted.
                            </flux:text>

                        </div>


                        <flux:field>

                            <flux:label>
                                Heading
                            </flux:label>

                            <flux:input
                                wire:model="successHeading"
                                :invalid="$errors->has('successHeading')"
                            />

                            <flux:error name="successHeading" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Description
                            </flux:label>

                            <flux:textarea
                                wire:model="successDescription"
                                rows="4"
                                :invalid="$errors->has('successDescription')"
                            />

                            <flux:error name="successDescription" />

                        </flux:field>


                        <flux:field>

                            <flux:label>
                                Button
                            </flux:label>

                            <flux:input
                                wire:model="successButton"
                                :invalid="$errors->has('successButton')"
                            />

                            <flux:error name="successButton" />

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
                    Select an icon for this checklist item.
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

