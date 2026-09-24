<?php

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\PostImageManager;

new
#[Layout('layouts::app')]
#[Title('Create Post')]
class extends Component {
    use WithFileUploads;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public string $category_id = '';

//    #[Validate('nullable|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000')]
    #[Validate('nullable')]
    #[Validate('image', message: 'The featured image must be a valid image.')]
    #[Validate('mimes:jpg,jpeg,png,webp', message: 'Only JPG, JPEG, PNG, or WebP images are allowed.')]
    #[Validate('max:10240', message: 'The featured image must not exceed 10MB.')]
    #[Validate(
        'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
        message: 'The featured image must be between 1200×630 and 4000×4000 pixels.'
    )]
    public $featuredImage = null;

    public bool $published = false;

    public ?string $published_at = null;

    public function updatedTitle(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    private function publicationDateIsFuture(): bool
    {
        if (blank($this->published_at)) {
            return false;
        }

        $publicationDate = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $this->published_at,
            config('app.timezone'),
        );

        return $publicationDate->isAfter(
            now(config('app.timezone'))->endOfMinute()
        );
    }

    private function validatePublicationState(): void
    {
        $this->resetValidation('published_at');

        if (!$this->published) {
            return;
        }

        if (blank($this->published_at)) {
            return;
        }

        if ($this->publicationDateIsFuture()) {
            $this->published = false;

            $this->addError(
                'published_at',
                'A published post cannot have a future publication date.'
            );
        }
    }

    public function updatedPublishedAt(?string $value): void
    {
        $this->validatePublicationState();
    }

    public function updatedPublished(bool $value): void
    {
        $this->validatePublicationState();
    }

    public function save(): void
    {
        $this->resetValidation('published_at');

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
                Rule::unique('posts', 'slug'),
            ],

            'excerpt' => [
                'required',
                'string',
                'max:1000',
            ],

            'content' => [
                'required',
                'string',
                'min:20',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'featuredImage' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
                'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
            ],

            'published' => [
                'boolean',
            ],

            'published_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],
        ]);

        $publishedAt = $validated['published_at'] ?? null;

        if (
            $validated['published'] &&
            filled($publishedAt)
        ) {
            $publicationDate = Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $publishedAt,
                config('app.timezone'),
            );

            if (
                $publicationDate->isAfter(
                    now(config('app.timezone'))->endOfMinute()
                )
            ) {
                $this->addError(
                    'published_at',
                    'A published post cannot have a future publication date.'
                );

                return;
            }
        }

        $featuredImagePaths = [];

        if ($this->featuredImage) {
            $featuredImagePaths = app(PostImageManager::class)
                ->storeVariants($this->featuredImage);
        }

        try {
            Post::create([
                'user_id' => auth()->id(),
                'category_id' => (int)$validated['category_id'],
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'excerpt' => $validated['excerpt'],
                'content' => $validated['content'],
                'featured_image' => $featuredImagePaths['large'] ?? null,
                'featured_image_medium' => $featuredImagePaths['medium'] ?? null,
                'featured_image_thumbnail' => $featuredImagePaths['thumbnail'] ?? null,
                'status' => $validated['published']
                    ? 'published'
                    : 'draft',
                'published_at' => $publishedAt,
            ]);
        } catch (\Throwable $e) {
            if ($featuredImagePaths !== []) {
                app(PostImageManager::class)
                    ->deleteStoredVariants($featuredImagePaths);
            }

            throw $e;
        }

        $this->redirectRoute('dashboard.posts.index', navigate: true);
        Flux::toast(
            heading: 'Post created',
            text: 'The post has been created successfully.',
            variant: 'success',
        );
    }

    public function removeFeaturedImage(): void
    {
        if ($this->featuredImage) {
            $this->featuredImage->delete();
        }

        $this->featuredImage = null;

        $this->resetValidation('featuredImage');
    }

    public function with(): array
    {
        return [
            'categories' => Category::query()
                ->orderBy('name')
                ->get(),
        ];
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>
        <flux:button
            href="{{ route('dashboard.posts.index') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Posts
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Create Post
        </flux:heading>

        <flux:text class="mt-1">
            Create a new Clean Stack article.
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
                        placeholder="Enter post title..."
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
                        placeholder="post-url-slug"
                        readonly
                        :invalid="$errors->has('slug')"
                    />

                    <flux:description>
                        Automatically generated from the title.
                    </flux:description>

                    <flux:error name="slug"/>
                </flux:field>

                {{-- Category --}}
                <flux:field>
                    <flux:label>
                        Category
                    </flux:label>

                    <flux:select
                        variant="listbox"
                        searchable
                        wire:model.live="category_id"
                        placeholder="Select a category..."
                        :invalid="$errors->has('category_id')"
                    >
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="category_id"/>
                </flux:field>

                {{-- Excerpt --}}
                <flux:field>
                    <flux:label>
                        Excerpt
                    </flux:label>

                    <flux:textarea
                        wire:model="excerpt"
                        placeholder="Write a short summary of the post..."
                        rows="4"
                        :invalid="$errors->has('excerpt')"
                    />

                    <flux:error name="excerpt"/>
                </flux:field>

                {{-- Featured Image --}}
                <flux:field>
                    <div
                        x-data
                        x-on:drop.capture="
                            const files = Array.from($event.dataTransfer?.files ?? []);
                            const allowed = ['image/jpeg', 'image/png', 'image/webp'];

                            if (files.some(file => !allowed.includes(file.type))) {
                                $event.preventDefault();
                                $event.stopImmediatePropagation();
                            }
                        "
                        x-on:change.capture="
                            const input = $event.target.closest('input[type=file]');

                            if (!input || !input.files?.length) {
                                return;
                            }

                            const allowed = ['image/jpeg', 'image/png', 'image/webp'];

                            if (Array.from(input.files).some(file => !allowed.includes(file.type))) {
                                $event.preventDefault();
                                $event.stopImmediatePropagation();
                                input.value = '';
                            }
                        "
                    >
                        <flux:file-upload
                            wire:model="featuredImage"
                            label="Featured Image"
                            accept="image/jpeg,image/png,image/webp"
                            :error="$errors->first('featuredImage')"
                        >
                            <flux:file-upload.dropzone
                                heading="Drop image here or click to browse"
                                text="JPG, JPEG, PNG, WebP • Max 10MB • 1200×630–4000×4000 px"
                                with-progress
                            />
                        </flux:file-upload>

                        @if ($featuredImage  && ! $errors->has('featuredImage'))
                            <div class="mt-4">
                                <flux:file-item
                                    :heading="$featuredImage->getClientOriginalName()"
                                    :image="$featuredImage->temporaryUrl()"
                                    :size="$featuredImage->getSize()"
                                >
                                    <x-slot name="actions">
                                        <flux:file-item.remove
                                            wire:click="removeFeaturedImage"
                                            aria-label="Remove featured image"
                                        />
                                    </x-slot>
                                </flux:file-item>
                            </div>
                        @endif
                    </div>
                </flux:field>

                {{-- Content --}}
                <flux:field>

                    <flux:editor
                        wire:model="content"
                        label="Content"
                        description="Write and format your article content."
                        placeholder="Start writing your article..."
                        :invalid="$errors->has('content')"
                        toolbar="heading | bold italic underline strike | bullet ordered blockquote | subscript superscript highlight code | link | align ~ undo redo | copy"
                        class="**:data-[slot=content]:min-h-[400px]"
                    />

                </flux:field>

                {{-- Status --}}
                <flux:field>
                    <flux:label>
                        Status
                    </flux:label>

                    <flux:switch
                        wire:model.live="published"
                        label="Published"
                        description="Turn this on to publish the post."
                    />
                </flux:field>

                {{-- Published At --}}
                <flux:field>
                    <flux:label>
                        Published At
                    </flux:label>

                    <flux:input
                        wire:model.live="published_at"
                        type="datetime-local"
                        :invalid="$errors->has('published_at')"
                    />

                    <flux:description>
                        Set the publication date and time for this post.
                    </flux:description>

                    <flux:error name="published_at"/>
                </flux:field>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-6 sm:flex-row sm:justify-end">
                    <flux:button
                        href="{{ route('dashboard.posts.index') }}"
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
                            Create Post
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

</div>
