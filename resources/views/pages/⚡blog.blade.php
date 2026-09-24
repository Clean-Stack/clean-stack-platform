<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Layout('layouts::public')]
#[Title('Blog — Clean Stack')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public int $perPage = 6;

    #[Url(history: true)]
    public string $category = 'all';

    public function updatedCategory(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function with(): array
    {
        return [
            'categories' => Category::query()
                ->orderBy('name')
                ->get(),

            'posts' => Post::query()
                ->with('category')
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->when(
                    $this->search,
                    fn($query) => $query->where(function ($query) {
                        $query
                            ->where(
                                'title',
                                'like',
                                '%' . $this->search . '%'
                            )
                            ->orWhere(
                                'excerpt',
                                'like',
                                '%' . $this->search . '%'
                            );
                    })
                )
                ->when(
                    $this->category !== 'all',
                    fn($query) => $query->where(
                        'category_id',
                        $this->category
                    )
                )
                ->latest('published_at')
                ->paginate($this->perPage),
        ];
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

    {{-- Header --}}
    <div class="max-w-2xl">

        <flux:heading size="xl">
            Blog
        </flux:heading>

        <flux:text class="mt-2">
            Ideas, lessons, and practical insights from building modern web experiences.
        </flux:text>

    </div>


    {{-- Filters --}}
    <div class="mt-6 flex flex-col gap-4 sm:flex-row">

        {{-- Search --}}
        <div class="w-full sm:max-w-md">

            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search articles..."
                icon="magnifying-glass"
            />

        </div>


        {{-- Category --}}
        <div class="w-full sm:max-w-xs">

            <flux:select
                wire:model.live="category"
                variant="listbox"
                searchable
                placeholder="Select a category..."
                prefix="Category : "
            >

                <flux:select.option value="all">
                    All
                </flux:select.option>

                @foreach ($categories as $categoryOption)

                    <flux:select.option value="{{ $categoryOption->id }}">
                        {{ $categoryOption->name }}
                    </flux:select.option>

                @endforeach

            </flux:select>

        </div>

    </div>


    {{-- Posts --}}
    <div class="mt-10">

        @if ($posts->isNotEmpty())

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($posts as $post)

                    <flux:card
                        wire:key="post-{{ $post->id }}"
                        class="flex h-full flex-col overflow-hidden"
                    >

                        {{-- Featured Image --}}
                        @if (
                            $post->featured_image_medium ||
                            $post->featured_image_thumbnail ||
                            $post->featured_image
                        )

                            <a
                                href="{{ route('blog.show', $post) }}"
                                wire:navigate
                                class="block overflow-hidden"
                                aria-label="Read {{ $post->title }}"
                            >

                                <img
                                    src="{{ Storage::url(
                                        $post->featured_image_medium
                                            ?? $post->featured_image
                                            ?? $post->featured_image_thumbnail
                                    ) }}"
                                    @if (
                                        $post->featured_image_medium &&
                                        $post->featured_image &&
                                        $post->featured_image_medium !== $post->featured_image
                                    )
                                        srcset="
                                            {{ Storage::url($post->featured_image_medium) }} 800w,
                                            {{ Storage::url($post->featured_image) }} 1200w
                                        "
                                    @endif
                                    sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                                    alt="{{ $post->title }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="aspect-video w-full object-cover transition-transform duration-300 hover:scale-105"
                                >

                            </a>

                        @else

                            {{-- Image Fallback --}}
                            <div
                                class="flex aspect-video w-full items-center justify-center bg-zinc-100 dark:bg-zinc-800"
                                aria-hidden="true"
                            >

                                <flux:icon
                                    name="photo"
                                    class="size-8 text-zinc-400 dark:text-zinc-500"
                                />

                            </div>

                        @endif


                        {{-- Content --}}
                        <div class="flex flex-1 flex-col p-6">

                            {{-- Category --}}
                            <div>

                                <flux:badge size="sm">
                                    {{ $post->category->name }}
                                </flux:badge>

                            </div>


                            {{-- Article Content --}}
                            <div class="mt-4 flex-1">

                                <flux:heading size="lg">
                                    {{ $post->title }}
                                </flux:heading>

                                <flux:text class="mt-2">
                                    {{ $post->excerpt }}
                                </flux:text>

                            </div>


                            {{-- Footer --}}
                            <div class="mt-6 flex items-center justify-between">

                                <flux:text size="sm">
                                    {{ $post->published_at->format('M d, Y') }}
                                </flux:text>

                                <flux:button
                                    href="{{ route('blog.show', $post) }}"
                                    variant="subtle"
                                    size="sm"
                                    wire:navigate
                                >
                                    Read
                                </flux:button>

                            </div>

                        </div>

                    </flux:card>

                @endforeach

            </div>


            {{-- Infinite Load Trigger --}}
            @if ($posts->hasMorePages())

                <div
                    class="mt-10 flex justify-center"
                    wire:intersect="loadMore"
                >

                    <flux:button
                        variant="subtle"
                        wire:loading
                        wire:target="loadMore"
                        disabled
                    >
                        Loading...
                    </flux:button>

                </div>

            @endif


            {{-- Loading Indicator --}}
            <div
                wire:loading
                wire:target="loadMore"
                class="mt-6 flex justify-center"
            >

                <flux:text size="sm">
                    Loading more posts...
                </flux:text>

            </div>

        @else

            <div class="py-16 text-center">

                <flux:heading size="lg">
                    No posts found
                </flux:heading>

                <flux:text class="mt-2">
                    Try a different search or category.
                </flux:text>

            </div>

        @endif

    </div>

</div>
