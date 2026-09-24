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
#[Title('Create Category')]
class extends Component {
    public string $name = '';

    public string $slug = '';

    public function updatedName(): void
    {
        $this->name = Str::lower($this->name);
        $this->slug = Str::slug($this->name);
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name'),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug'),
            ],
        ];
    }

    public function save(): void
    {
        $this->name = Str::lower(trim($this->name));
        $this->slug = Str::slug($this->slug);

        $validated = $this->validate();

        Category::create($validated);

        $this->redirectRoute('dashboard.categories.index', navigate: true);
        Flux::toast(
            heading: 'Category created',
            text: 'The category has been created successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl">
            Create Category
        </flux:heading>

        <flux:text class="mt-1">
            Create a category to organize your posts.
        </flux:text>
    </div>

    {{-- Form --}}
    <flux:card>

        <form wire:submit="save" class="space-y-6">

            {{-- Name --}}
            <flux:field>
                <flux:label>
                    Name
                </flux:label>

                <flux:input
                    wire:model.live="name"
                    placeholder="e.g. Laravel"
                    :invalid="$errors->has('name')"
                />

                <flux:error name="name"/>
            </flux:field>

            {{-- Slug --}}
            <flux:field>
                <flux:label>
                    Slug
                </flux:label>

                <flux:input
                    wire:model="slug"
                    readonly
                    :invalid="$errors->has('slug')"
                />

                <flux:description>
                    The slug is generated automatically from the category name.
                </flux:description>

                <flux:error name="slug"/>
            </flux:field>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button
                    variant="ghost"
                    href="{{ route('dashboard.categories.index') }}"
                >
                    Cancel
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">
                        Create Category
                    </span>

                    <span wire:loading wire:target="save">
                        Creating...
                    </span>
                </flux:button>
            </div>

        </form>

    </flux:card>
</div>
