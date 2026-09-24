<?php

use App\Models\Project;
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
#[Title('Edit Project')]
class extends Component
{
    use WithFileUploads;

    public Project $project;

    public string $title = '';

    public string $slug = '';

    /*
    |--------------------------------------------------------------------------
    | Slug Editing
    |--------------------------------------------------------------------------
    |
    | Slug is locked by default because changing an existing project's
    | slug changes its public URL if one is used later.
    |
    */
    public bool $editingSlug = false;

    public string $summary = '';

    public string $description = '';

    public ?string $live_url = null;

    public ?string $github_url = null;

    public bool $is_featured = false;

    public bool $is_published = false;

    public ?string $existingCoverImage = null;

    public bool $removeExistingCoverImage = false;

    #[Validate('nullable')]
    #[Validate(
        'image',
        message: 'The project cover image must be a valid image.'
    )]
    #[Validate(
        'mimes:jpg,jpeg,png,webp',
        message: 'Only JPG, JPEG, PNG, or WebP images are allowed.'
    )]
    #[Validate(
        'max:10240',
        message: 'The project cover image must not exceed 10MB.'
    )]
    #[Validate(
        'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
        message: 'The project cover image must be between 1200×630 and 4000×4000 pixels.'
    )]
    public $coverImage = null;

    public function mount(Project $project): void
    {
        $this->project = $project;

        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->summary = $project->summary;
        $this->description = $project->description;
        $this->live_url = $project->live_url;
        $this->github_url = $project->github_url;
        $this->is_featured = $project->is_featured;
        $this->is_published = $project->is_published;
        $this->existingCoverImage = $project->cover_image;
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
                text: 'This URL may be used publicly. Changing it may affect SEO and existing links to this project.',
                variant: 'warning',
            );

            return;
        }

        /*
         * Restore the original persisted slug when editing
         * is cancelled before saving.
         */
        $this->slug = $this->project->slug;

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
    | Cover Image
    |--------------------------------------------------------------------------
    */
    public function updatedCoverImage(): void
    {
        if ($this->coverImage) {
            $this->removeExistingCoverImage = false;
        }
    }

    public function removeCoverImage(): void
    {
        /*
         * Remove a newly uploaded temporary image immediately.
         */
        if ($this->coverImage) {
            $this->coverImage->delete();

            $this->coverImage = null;

            $this->resetValidation('coverImage');

            return;
        }

        /*
         * Existing image is only marked for deletion.
         *
         * The real file will be deleted when Save succeeds.
         */
        if ($this->existingCoverImage) {
            $this->removeExistingCoverImage = true;
        }
    }

    public function restoreExistingCoverImage(): void
    {
        $this->removeExistingCoverImage = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */
    public function save(): void
    {
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
                Rule::unique('projects', 'slug')
                    ->ignore($this->project->id),
            ],

            'summary' => [
                'required',
                'string',
                'max:1000',
            ],

            'description' => [
                'required',
                'string',
            ],

            'coverImage' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
                'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
            ],

            'live_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'github_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'is_featured' => [
                'boolean',
            ],

            'is_published' => [
                'boolean',
            ],
        ]);

        $oldCoverImage = $this->project->cover_image;

        $newCoverImagePath = null;

        /*
         * Store the new image first.
         */
        if ($this->coverImage) {
            $newCoverImagePath = $this->coverImage->store(
                'projects',
                'public'
            );
        }

        $updatedCoverImage = $oldCoverImage;

        /*
         * New image has priority over removing the old image.
         */
        if ($newCoverImagePath) {
            $updatedCoverImage = $newCoverImagePath;
        } elseif ($this->removeExistingCoverImage) {
            $updatedCoverImage = null;
        }

        try {
            $this->project->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'summary' => $validated['summary'],
                'description' => $validated['description'],
                'cover_image' => $updatedCoverImage,
                'live_url' => $validated['live_url'] ?: null,
                'github_url' => $validated['github_url'] ?: null,
                'is_featured' => $validated['is_featured'],
                'is_published' => $validated['is_published'],
            ]);
        } catch (\Throwable $e) {
            /*
             * Database update failed.
             * Remove the newly stored image so it does not become orphaned.
             */
            if ($newCoverImagePath) {
                Storage::disk('public')->delete($newCoverImagePath);
            }

            throw $e;
        }

        /*
         * Delete the old image only after the database update succeeds.
         */
        if (
            $oldCoverImage &&
            $oldCoverImage !== $updatedCoverImage
        ) {
            Storage::disk('public')->delete($oldCoverImage);
        }


        $this->redirectRoute('dashboard.projects.index', navigate: true);
        Flux::toast(
            heading: 'Project updated',
            text: 'The project has been updated successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>

        <flux:button
            href="{{ route('dashboard.projects.index') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Projects
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Edit Project
        </flux:heading>

        <flux:text class="mt-1">
            Update the project details and settings.
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

                    <flux:input
                        wire:model.live.debounce.300ms="title"
                        label="Title"
                        placeholder="Project title"
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
                        placeholder="project-url-slug"
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
                            The slug is locked to protect the existing URL. Changing the title will not change it.
                        </flux:description>

                    @endif

                    <flux:error name="slug" />

                </flux:field>


                {{-- Summary --}}
                <flux:field>

                    <flux:textarea
                        wire:model="summary"
                        label="Summary"
                        placeholder="Short project summary"
                        rows="3"
                        :invalid="$errors->has('summary')"
                    />

                    <flux:error name="summary" />

                </flux:field>


                {{-- Cover Image --}}
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
                            wire:model="coverImage"
                            label="Cover Image"
                            accept="image/jpeg,image/png,image/webp"
                            :error="$errors->first('coverImage')"
                        >
                            <flux:file-upload.dropzone
                                heading="Drop image here or click to browse"
                                text="JPG, JPEG, PNG, WebP • Max 10MB • 1200×630–4000×4000 px"
                                with-progress
                            />
                        </flux:file-upload>


                        {{-- New Image Preview --}}
                        @if ($coverImage && ! $errors->has('coverImage'))

                            <div class="mt-4">

                                <flux:file-item
                                    :heading="$coverImage->getClientOriginalName()"
                                    :image="$coverImage->temporaryUrl()"
                                    :size="$coverImage->getSize()"
                                >
                                    <x-slot name="actions">

                                        <flux:file-item.remove
                                            wire:click="removeCoverImage"
                                            aria-label="Remove new cover image"
                                        />

                                    </x-slot>
                                </flux:file-item>

                            </div>

                        @elseif ($existingCoverImage && ! $removeExistingCoverImage)

                            {{-- Existing Image --}}
                            <div class="mt-4">

                                <flux:file-item
                                    heading="Current cover image"
                                    :image="Storage::url($existingCoverImage)"
                                >
                                    <x-slot name="actions">

                                        <flux:file-item.remove
                                            wire:click="removeCoverImage"
                                            aria-label="Remove current cover image"
                                        />

                                    </x-slot>
                                </flux:file-item>

                            </div>

                        @elseif ($removeExistingCoverImage)

                            {{-- Removed Existing Image --}}
                            <div class="mt-4">

                                <div class="flex items-center justify-between rounded-lg border border-dashed border-zinc-300 px-4 py-3 dark:border-zinc-700">

                                    <flux:text size="sm">
                                        Current cover image will be removed when you save.
                                    </flux:text>

                                    <flux:button
                                        type="button"
                                        variant="subtle"
                                        size="sm"
                                        wire:click="restoreExistingCoverImage"
                                    >
                                        Keep Image
                                    </flux:button>

                                </div>

                            </div>

                        @endif

                    </div>

                </flux:field>


                {{-- Description --}}
                <flux:field>

                    <flux:editor
                        wire:model="description"
                        label="Description"
                        placeholder="Describe the project..."
                        toolbar="heading | bold italic underline strike | bullet ordered blockquote | subscript superscript highlight code | link | align ~ undo redo | copy"
                        :invalid="$errors->has('description')"
                    />

                </flux:field>


                {{-- Live Demo URL --}}
                <flux:field>

                    <flux:input
                        wire:model="live_url"
                        label="Live Demo URL"
                        type="text"
                        placeholder="https://example.com"
                        :invalid="$errors->has('live_url')"
                    />


                </flux:field>


                {{-- GitHub URL --}}
                <flux:field>

                    <flux:input
                        wire:model="github_url"
                        label="GitHub URL"
                        type="text"
                        placeholder="https://github.com/..."
                        :invalid="$errors->has('github_url')"
                    />

                </flux:field>


                {{-- Featured --}}
                <flux:field>

                    <flux:checkbox
                        wire:model="is_featured"
                        label="Featured project"
                        description="Show this project as a featured project where applicable."
                    />

                    <flux:error name="is_featured" />

                </flux:field>


                {{-- Published --}}
                <flux:field>

                    <flux:switch
                        wire:model.live="is_published"
                        label="Published project"
                        description="Published projects are visible on the public Projects page."
                    />

                    <flux:error name="is_published" />

                </flux:field>


                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 border-t border-zinc-200 pt-6 dark:border-zinc-800">

                    <flux:button
                        href="{{ route('dashboard.projects.index') }}"
                        variant="ghost"
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
                            Updating...
                        </span>

                    </flux:button>

                </div>

            </form>

        </flux:card>

    </div>

</div>

