<?php

use App\Models\Category;
use App\Models\Post;
use App\Services\PostImageManager;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new
#[Layout('layouts::app')]
#[Title('Edit Post')]
class extends Component
{
    use WithFileUploads;

    public Post $post;

    public string $title = '';

    public string $slug = '';

    public bool $editingSlug = false;

    public string $excerpt = '';

    public string $content = '';

    public string $category_id = '';

    /*
    |--------------------------------------------------------------------------
    | Existing Featured Image
    |--------------------------------------------------------------------------
    */
    public ?string $existingFeaturedImage = null;

    public bool $removeExistingFeaturedImage = false;

    #[Validate('nullable')]
    #[Validate(
        'image',
        message: 'The featured image must be a valid image.'
    )]
    #[Validate(
        'mimes:jpg,jpeg,png,webp',
        message: 'Only JPG, JPEG, PNG, or WebP images are allowed.'
    )]
    #[Validate(
        'max:10240',
        message: 'The featured image must not exceed 10MB.'
    )]
    #[Validate(
        'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
        message: 'The featured image must be between 1200×630 and 4000×4000 pixels.'
    )]
    public $featuredImage = null;

    /*
    |--------------------------------------------------------------------------
    | Post Status
    |--------------------------------------------------------------------------
    |
    | false → draft
    | true  → published
    |
    */
    public bool $published = false;

    /*
    |--------------------------------------------------------------------------
    | Published At
    |--------------------------------------------------------------------------
    */
    public ?string $published_at = null;

    public function mount(Post $post): void
    {
        $this->post = $post;

        $this->title = $post->title;

        $this->slug = $post->slug;

        $this->excerpt = $post->excerpt;

        $this->content = $post->content;

        $this->category_id = (string) $post->category_id;

        $this->published = $post->status === 'published';

        $this->published_at = $post->published_at?->format('Y-m-d\TH:i');

        $this->existingFeaturedImage = $post->featured_image;
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Editing Toggle
    |--------------------------------------------------------------------------
    */
    public function updatedEditingSlug(bool $value): void
    {
        if ($value) {
            Flux::toast(
                heading: 'Editing the URL',
                text: 'This URL is already published on the internet. Changing it may affect SEO and existing links to this post.',
                variant: 'warning',
            );

            return;
        }

        $this->slug = $this->post->slug;

        $this->resetValidation('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Slug From Title
    |--------------------------------------------------------------------------
    */
    public function generateSlugFromTitle(): void
    {
        $this->slug = Str::slug($this->title);

        $this->resetValidation('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Publication Date
    |--------------------------------------------------------------------------
    */
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

        if (! $this->published) {
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

    /*
    |--------------------------------------------------------------------------
    | Featured Image
    |--------------------------------------------------------------------------
    */
    public function updatedFeaturedImage(): void
    {
        if ($this->featuredImage) {
            $this->removeExistingFeaturedImage = false;
        }
    }

    public function removeFeaturedImage(): void
    {
        /*
         * Remove a newly uploaded temporary image immediately.
         */
        if ($this->featuredImage) {
            $this->featuredImage->delete();

            $this->featuredImage = null;

            $this->resetValidation('featuredImage');

            return;
        }

        /*
         * Existing image is only marked for deletion.
         *
         * The actual image variants will be deleted when Save succeeds.
         */
        if ($this->existingFeaturedImage) {
            $this->removeExistingFeaturedImage = true;
        }
    }

    public function restoreExistingFeaturedImage(): void
    {
        $this->removeExistingFeaturedImage = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */
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
                Rule::unique('posts', 'slug')
                    ->ignore($this->post->id),
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

        $publishedAt = filled($validated['published_at'] ?? null)
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $validated['published_at'],
                config('app.timezone'),
            )
            : null;

        /*
        |--------------------------------------------------------------------------
        | Final Publishing Rule
        |--------------------------------------------------------------------------
        */
        if (
            $validated['published'] &&
            $publishedAt &&
            $publishedAt->isAfter(
                now(config('app.timezone'))->endOfMinute()
            )
        ) {
            $this->addError(
                'published_at',
                'A published post cannot have a future publication date.'
            );

            return;
        }

        $oldFeaturedImage = $this->post->featured_image;

        $this->post->update([
            'category_id' => (int) $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $validated['published']
                ? 'published'
                : 'draft',
            'published_at' => $publishedAt,
        ]);

        /*
        |--------------------------------------------------------------------------
        | New Image
        |--------------------------------------------------------------------------
        |
        | Existing behavior remains unchanged:
        | PostImageManager handles all image variants.
        |
        */
        if ($this->featuredImage) {
            app(PostImageManager::class)->replaceVariants(
                $this->post,
                $this->featuredImage,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Existing Image
        |--------------------------------------------------------------------------
        |
        | Only remove the existing variants when:
        |
        | - the user explicitly removed the image
        | - and did not upload a replacement
        |
        */
        elseif (
            $this->removeExistingFeaturedImage &&
            $oldFeaturedImage
        ) {
            app(PostImageManager::class)->deleteVariants($this->post);

            $this->post->update([
                'featured_image' => null,
                'featured_image_medium' => null,
                'featured_image_thumbnail' => null,
            ]);
        }

        $this->redirectRoute('dashboard.posts.index', navigate: true);
        Flux::toast(
            heading: 'Post updated',
            text: 'The post has been updated successfully.',
            variant: 'success',
        );
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
            Edit Post
        </flux:heading>

        <flux:text class="mt-1">
            Update this Clean Stack article.
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

                    <flux:error name="title" />

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
                        placeholder="post-url-slug"
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
                                wire:click="generateSlugFromTitle"
                            >
                                Generate from Title
                            </flux:button>

                        </div>

                    @else

                        <flux:description>
                            The slug is locked to protect your existing URL. Changing the title will not change it.
                        </flux:description>

                    @endif

                    <flux:error name="slug" />

                </flux:field>


                {{-- Category --}}
                <flux:field>

                    <flux:label>
                        Category
                    </flux:label>

                    <flux:select
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

                    <flux:error name="category_id" />

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

                    <flux:error name="excerpt" />

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


                        {{-- New Image Preview --}}
                        @if ($featuredImage && ! $errors->has('featuredImage'))

                            <div class="mt-4">

                                <flux:file-item
                                    :heading="$featuredImage->getClientOriginalName()"
                                    :image="$featuredImage->temporaryUrl()"
                                    :size="$featuredImage->getSize()"
                                >

                                    <x-slot name="actions">

                                        <flux:file-item.remove
                                            wire:click="removeFeaturedImage"
                                            aria-label="Remove new featured image"
                                        />

                                    </x-slot>

                                </flux:file-item>

                            </div>


                            {{-- Existing Image --}}
                        @elseif ($existingFeaturedImage && ! $removeExistingFeaturedImage)

                            <div class="mt-4">

                                <flux:file-item
                                    heading="Current featured image"
                                    :image="Storage::disk(config('posts.images.disk', 'public'))
                                        ->url($existingFeaturedImage)"
                                >

                                    <x-slot name="actions">

                                        <flux:file-item.remove
                                            wire:click="removeFeaturedImage"
                                            aria-label="Remove current featured image"
                                        />

                                    </x-slot>

                                </flux:file-item>

                            </div>


                            {{-- Removed Existing Image --}}
                        @elseif ($removeExistingFeaturedImage)

                            <div class="mt-4">

                                <div class="flex items-center justify-between rounded-lg border border-dashed border-zinc-300 px-4 py-3 dark:border-zinc-700">

                                    <flux:text size="sm">
                                        Current featured image will be removed when you save.
                                    </flux:text>

                                    <flux:button
                                        type="button"
                                        variant="subtle"
                                        size="sm"
                                        wire:click="restoreExistingFeaturedImage"
                                    >
                                        Keep Image
                                    </flux:button>

                                </div>

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

                    <flux:error name="published_at" />

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

