<?php

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::app')]
#[Title('Edit Category')]
class extends Component
{
    public Category $category;

    public string $name = '';

    public string $slug = '';

    /*
    |--------------------------------------------------------------------------
    | Slug Editing
    |--------------------------------------------------------------------------
    |
    | Slug is locked by default because changing an existing category's
    | slug changes its public URL.
    |
    */
    public bool $editingSlug = false;

    public function mount(Category $category): void
    {
        $this->category = $category;

        $this->name = $category->name;

        $this->slug = $category->slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Name Changed
    |--------------------------------------------------------------------------
    |
    | Category names are stored in lowercase.
    |
    | Changing the name must NOT automatically change the existing slug.
    |
    */
    public function updatedName(): void
    {
        $this->name = Str::lower($this->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Editing Toggle
    |--------------------------------------------------------------------------
    |
    | When slug editing is enabled:
    |
    | - The slug input becomes editable.
    | - A warning toast informs the user about SEO and existing links.
    |
    | When slug editing is disabled:
    |
    | - The persisted slug is restored.
    | - Any temporary slug changes are discarded.
    |
    */
    public function updatedEditingSlug(bool $value): void
    {
        if ($value) {
            Flux::toast(
                heading: 'Editing the URL',
                text: 'This URL may already be used publicly. Changing it may affect SEO and existing links to this category.',
                variant: 'warning',
            );

            return;
        }

        /*
        | Restore the original slug from the database.
        */
        $this->slug = $this->category->slug;

        /*
        | Remove any slug validation error caused by a temporary value.
        */
        $this->resetValidation('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Slug From Name
    |--------------------------------------------------------------------------
    |
    | This is intentionally NOT connected to updatedName().
    |
    | Changing the category name must never automatically change
    | the existing slug.
    |
    */
    public function generateSlugFromName(): void
    {
        $this->slug = Str::slug($this->name);

        /*
        | The generated slug may replace a previously invalid value,
        | so clear any previous slug validation error.
        */
        $this->resetValidation('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */
    public function save(): void
    {
        /*
        | Normalize the category name at the application layer.
        */
        $this->name = Str::lower(trim($this->name));

        /*
        | Only normalize the slug when the user explicitly enabled
        | slug editing.
        |
        | Otherwise, preserve the persisted slug exactly.
        */
        if ($this->editingSlug) {
            $this->slug = Str::slug($this->slug);
        } else {
            $this->slug = $this->category->slug;
        }

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($this->category->id),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->ignore($this->category->id),
            ],
        ]);

        $this->category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        $this->redirectRoute('dashboard.categories.index', navigate: true);
        Flux::toast(
            heading: 'Category updated',
            text: 'The category has been updated successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>

        <flux:button
            href="{{ route('dashboard.categories.index') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Categories
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Edit Category
        </flux:heading>

        <flux:text class="mt-1">
            Update this Clean Stack category.
        </flux:text>

    </div>


    {{-- Form --}}
    <div class="mt-6">

        <flux:card>

            <form
                wire:submit="save"
                class="space-y-6"
            >

                {{-- Name --}}
                <flux:field>

                    <flux:label>
                        Name
                    </flux:label>

                    <flux:input
                        wire:model.live.debounce.300ms="name"
                        placeholder="Enter category name..."
                        :invalid="$errors->has('name')"
                    />

                    <flux:error name="name" />

                </flux:field>


                {{-- Slug --}}
                <flux:field>

                    <div class="flex items-center justify-between gap-4">

                        <flux:label>
                            Slug
                        </flux:label>

                        <flux:switch
                            wire:model.live="editingSlug"
                            label="Edit Slug"
                        />

                    </div>

                    <flux:input
                        wire:model="slug"
                        placeholder="category-url-slug"
                        :disabled="! $editingSlug"
                        :invalid="$errors->has('slug')"
                    />

                    @if ($editingSlug)

                        <div class="mt-2">

                            <flux:button
                                type="button"
                                variant="subtle"
                                size="sm"
                                icon="arrow-path"
                                wire:click="generateSlugFromName"
                            >
                                Generate from Name
                            </flux:button>

                        </div>

                    @else

                        <flux:description>
                            The slug is locked to protect the existing URL. Changing the name will not change it.
                        </flux:description>

                    @endif

                    <flux:error name="slug" />

                </flux:field>


                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-6 sm:flex-row sm:justify-end">

                    <flux:button
                        href="{{ route('dashboard.categories.index') }}"
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

</div>
