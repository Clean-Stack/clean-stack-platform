<?php

use App\Models\Service;
use App\Support\ServiceIcons;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new
#[Layout('layouts::app')]
#[Title('Create Service')]
class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:255|unique:services,slug')]
    public string $slug = '';

    #[Validate('required|string|max:500')]
    public string $short_description = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('nullable|string|max:50')]
    public ?string $icon = null;

    public bool $is_featured = false;

    public bool $is_active = true;

    public bool $showIconModal = false;

    public bool $iconCopied = false;

    public function updatedTitle(): void
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedIcon(?string $value): void
    {
        $this->icon = $value !== null
            ? trim($value)
            : null;

        $this->iconCopied = false;

        $this->validateOnly('icon', [
            'icon' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],
        ]);
    }

    public function openIconModal(): void
    {
        $this->iconCopied = false;

        $this->showIconModal = true;
    }

    public function closeIconModal(): void
    {
        $this->showIconModal = false;

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

    public function selectIcon(string $icon): void
    {
        if (!in_array($icon, ServiceIcons::ALL, true)) {
            return;
        }

        $this->icon = $icon;

        $this->resetValidation('icon');

        $this->iconCopied = true;

        $this->showIconModal = false;
    }

    public function save(): void
    {
        $this->icon = $this->icon !== null
            ? trim($this->icon)
            : null;

        $validated = $this->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:services,slug',
            ],

            'short_description' => [
                'required',
                'string',
                'max:500',
            ],

            'description' => [
                'required',
                'string',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(ServiceIcons::ALL),
            ],
        ]);

        Service::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'icon' => $validated['icon'] ?: null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
        ]);

        $this->redirectRoute('dashboard.services.index', navigate: true);
        Flux::toast(
            heading: 'Service created',
            text: 'The service has been created successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>

        <flux:button
            href="{{ route('dashboard.services.index') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Services
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Create Service
        </flux:heading>

        <flux:text class="mt-1">
            Create a new Clean Stack service.
        </flux:text>

    </div>


    {{-- Form --}}
    <div class="mt-6">

        <flux:card>

            <form
                wire:submit="save"
                class="space-y-6"
            >

                {{-- Title --}}
                <flux:field>

                    <flux:label>
                        Title
                    </flux:label>

                    <flux:input
                        wire:model.live.debounce.300ms="title"
                        placeholder="Enter service title..."
                        :invalid="$errors->has('title')"
                    />

                    <flux:error name="title"/>

                </flux:field>


                {{-- Slug --}}
                <flux:field>

                    <flux:label>
                        Slug
                    </flux:label>

                    <flux:input
                        wire:model="slug"
                        placeholder="service-url-slug"
                        readonly
                        :invalid="$errors->has('slug')"
                    />

                    <flux:description>
                        Automatically generated from the title.
                    </flux:description>

                    <flux:error name="slug"/>

                </flux:field>


                {{-- Short Description --}}
                <flux:field>

                    <flux:label>
                        Short Description
                    </flux:label>

                    <flux:textarea
                        wire:model="short_description"
                        placeholder="Write a short summary of this service..."
                        rows="4"
                        :invalid="$errors->has('short_description')"
                    />

                    <flux:description>
                        A concise summary used in service listings and previews.
                    </flux:description>

                    <flux:error name="short_description"/>

                </flux:field>


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
                            wire:click="openIconModal"
                        >
                            Choose Icon
                        </flux:button>

                    </div>

                    <div class="flex items-start gap-3">

                        <div class="flex-1">

                            <flux:input
                                wire:model.live.debounce.300ms="icon"
                                placeholder="code-bracket"
                                :invalid="$errors->has('icon')"
                            />

                        </div>

                        @if ($this->isValidIcon($icon))

                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-700">

                                <flux:icon
                                    :name="$icon"
                                    variant="outline"
                                    class="size-5"
                                />

                            </div>

                        @endif

                    </div>

                    <flux:description>
                        Enter a valid Flux icon name or choose one from the icon library.
                    </flux:description>

                    <flux:error name="icon"/>

                </flux:field>


                {{-- Description --}}
                <flux:field>

                    <flux:editor
                        wire:model="description"
                        label="Description"
                        description="Write and format the full service description."
                        placeholder="Start writing your service description..."
                        :invalid="$errors->has('description')"
                        toolbar="heading | bold italic underline strike | bullet ordered blockquote | subscript superscript highlight code | link | align ~ undo redo | copy"
                        class="**:data-[slot=content]:min-h-[400px]"
                    />

                </flux:field>


                {{-- Featured --}}
                <flux:field>

                    <flux:checkbox
                        wire:model="is_featured"
                        label="Featured service"
                        description="Show this service as a featured service where applicable."
                    />

                    <flux:error name="is_featured"/>

                </flux:field>


                {{-- Active --}}
                <flux:field>

                    <flux:switch
                        wire:model.live="is_active"
                        label="Active service"
                        description="Active services are visible on the public Services page."
                    />

                    <flux:error name="is_active"/>

                </flux:field>


                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-6 sm:flex-row sm:justify-end">

                    <flux:button
                        href="{{ route('dashboard.services.index') }}"
                        variant="subtle"
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
                            Create Service
                        </span>

                        <span
                            wire:loading
                            wire:target="save"
                        >
                            Creating...
                        </span>

                    </flux:button>

                </div>

            </form>

        </flux:card>

    </div>


    {{-- Icon Picker Modal --}}
    <flux:modal
        wire:model.self="showIconModal"
        class="w-full max-w-4xl"
    >

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Choose a Service Icon
                </flux:heading>

                <flux:text class="mt-1">
                    Select an icon that represents this service.
                </flux:text>

            </div>


            <div
                class="grid max-h-[60vh] grid-cols-3 gap-2 overflow-y-auto sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">

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

