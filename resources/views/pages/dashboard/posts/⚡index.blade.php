<?php

use App\Models\Post;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new
#[Layout('layouts::app')]
#[Title('Posts')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = 'all';

    public int $perPage = 6;

    public ?int $postToDelete = null;

    public bool $showDeleteModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function confirmDelete(int $postId): void
    {
        $this->postToDelete = $postId;

        $this->showDeleteModal = true;
    }

    public function deletePost(): void
    {
        if ($this->postToDelete === null) {
            return;
        }

        Post::query()
            ->findOrFail($this->postToDelete)
            ->delete();

        $this->postToDelete = null;

        $this->showDeleteModal = false;

        Flux::toast(
            heading: 'Post moved to trash',
            text: 'The post has been moved to the trash successfully.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'posts' => Post::query()
                ->with('category')
                ->when(
                    $this->search,
                    fn($query) => $query->where(function ($query) {
                        $query
                            ->where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%');
                    })
                )
                ->when(
                    $this->status !== 'all',
                    fn($query) => $query->where('status', $this->status)
                )
                ->latest()
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
                Posts
            </flux:heading>

            <flux:text class="mt-1">
                Create and manage Clean Stack articles.
            </flux:text>
        </div>

        <div class="flex items-center gap-2">

            <flux:button
                href="{{ route('dashboard.posts.trash') }}"
                variant="outline"
                wire:navigate
            >
                Trash
            </flux:button>

            <flux:button
                variant="primary"
                href="{{ route('dashboard.posts.create') }}"
                wire:navigate
            >
                Create Post
            </flux:button>

        </div>

    </div>


    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">

        <div class="flex-1">

            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search posts..."
                icon="magnifying-glass"
            />

        </div>


        <div class="w-full sm:w-48">

            <flux:select wire:model.live="status" variant="listbox" prefix="Status : ">
                <flux:select.option value="all">All</flux:select.option>
                <flux:select.option value="draft">Draft</flux:select.option>
                <flux:select.option value="published">Published</flux:select.option>
            </flux:select>

        </div>

    </div>


    {{-- Posts Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Title
            </flux:table.column>

            <flux:table.column>
                Category
            </flux:table.column>

            <flux:table.column>
                Status
            </flux:table.column>

            <flux:table.column>
                Published
            </flux:table.column>

            <flux:table.column>
                Actions
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($posts as $post)

                <flux:table.row wire:key="post-{{ $post->id }}">

                    <flux:table.cell>
                        {{ Str::limit($post->title, 90) }}
                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $post->category->name }}
                    </flux:table.cell>


                    <flux:table.cell>

                        <flux:badge size="sm">
                            {{ ucfirst($post->status) }}
                        </flux:badge>

                    </flux:table.cell>


                    <flux:table.cell>

                        @if ($post->published_at)
                            {{ $post->published_at->format('M d, Y') }}
                        @else
                            —
                        @endif

                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            <flux:button
                                href="{{ route('dashboard.posts.edit', $post) }}"
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
                                wire:click="confirmDelete({{ $post->id }})"
                            >
                                Delete
                            </flux:button>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @empty

                <flux:table.row>

                    <flux:table.cell colspan="5">

                        <div class="py-8 text-center">

                            <flux:text>
                                No posts found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>


    {{-- Infinite Loading --}}
    @if ($posts->hasMorePages())

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
                    Delete Post?
                </flux:heading>

                <flux:text class="mt-2">
                    You're about to delete this post. The post will be moved to the trash.
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
                    wire:click="deletePost"
                    wire:loading.attr="disabled"
                    wire:target="deletePost"
                >

                    <span
                        wire:loading.remove
                        wire:target="deletePost"
                    >
                        Delete Post
                    </span>

                    <span
                        wire:loading
                        wire:target="deletePost"
                    >
                        Deleting...
                    </span>

                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>

