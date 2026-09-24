<?php

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Layout('layouts::app')]
#[Title('Categories')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public int $perPage = 6;

    public ?int $categoryToDelete = null;

    public bool $showDeleteModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function confirmDelete(int $categoryId): void
    {
        $this->categoryToDelete = $categoryId;

        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        if ($this->categoryToDelete === null) {
            return;
        }

        $category = Category::query()
            ->findOrFail($this->categoryToDelete);

        $postsCount = $category->posts()->count();

        if ($postsCount > 0) {
            $this->categoryToDelete = null;

            $this->showDeleteModal = false;

            Flux::toast(
                heading: 'Category cannot be deleted',
                text: "This category contains {$postsCount} " .
                ($postsCount === 1 ? 'post' : 'posts') .
                '. First move the posts to another category.',
                variant: 'warning',
            );

            return;
        }

        $category->delete();

        $this->categoryToDelete = null;

        $this->showDeleteModal = false;

        Flux::toast(
            heading: 'Category deleted',
            text: 'The category has been deleted successfully.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'categories' => Category::query()
                ->when(
                    $this->search,
                    fn($query) => $query->where(function ($query) {
                        $query
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%');
                    })
                )
                ->withCount('posts')
                ->orderBy('name')
                ->paginate($this->perPage),
        ];
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <flux:heading size="xl">
                Categories
            </flux:heading>

            <flux:text class="mt-1">
                Create and manage Clean Stack categories.
            </flux:text>

        </div>


        <div class="flex items-center gap-2">

            <flux:button
                variant="primary"
                href="{{ route('dashboard.categories.create') }}"
                wire:navigate
            >
                Create Category
            </flux:button>

        </div>

    </div>


    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">

        <div class="flex-1">

            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search categories..."
                icon="magnifying-glass"
            />

        </div>

    </div>


    {{-- Categories Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Name
            </flux:table.column>

            <flux:table.column>
                Slug
            </flux:table.column>

            <flux:table.column>
                Posts
            </flux:table.column>

            <flux:table.column>
                Actions
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($categories as $category)

                <flux:table.row wire:key="category-{{ $category->id }}">

                    <flux:table.cell>
                        {{ $category->name }}
                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $category->slug }}
                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $category->posts_count }}
                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            <flux:button
                                href="{{ route('dashboard.categories.edit', $category) }}"
                                variant="outline"
                                size="sm"
                                wire:navigate
                            >
                                Edit
                            </flux:button>


                            <flux:button
                                type="button"
                                variant="outline"
                                size="sm"
                                wire:click="confirmDelete({{ $category->id }})"
                            >
                                Delete
                            </flux:button>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @empty

                <flux:table.row>

                    <flux:table.cell colspan="4">

                        <div class="py-8 text-center">

                            <flux:text>
                                No categories found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>


    {{-- Infinite Loading --}}
    @if ($categories->hasMorePages())

        <div
            wire:intersect="loadMore"
            class="flex justify-center py-4"
        >

            <flux:icon
                name="arrow-path"
                class="size-5 animate-spin"
                wire:loading
                wire:target="loadMore"
            />

        </div>

    @endif


    {{-- Delete Modal --}}
    <flux:modal
        wire:model.self="showDeleteModal"
        class="min-w-[22rem]"
    >

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Category?
                </flux:heading>

                <flux:text class="mt-2">
                    You're about to delete this category. This action cannot be undone.
                </flux:text>

            </div>


            <div class="flex justify-end gap-2">

                <flux:modal.close>

                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>

                </flux:modal.close>


                <flux:button
                    type="button"
                    variant="outline"
                    wire:click="deleteCategory"
                    wire:loading.attr="disabled"
                    wire:target="deleteCategory"
                >

                    <span
                        wire:loading.remove
                        wire:target="deleteCategory"
                    >
                        Delete Category
                    </span>

                    <span
                        wire:loading
                        wire:target="deleteCategory"
                    >
                        Deleting...
                    </span>

                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>

