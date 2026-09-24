<?php

use App\Models\Project;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new
#[Layout('layouts::app')]
#[Title('Create Project')]
class extends Component {
    use WithFileUploads;

    public string $title = '';

    public string $slug = '';

    public string $summary = '';

    public string $description = '';

    #[Validate('nullable')]
    #[Validate('image', message: 'The project cover image must be a valid image.')]
    #[Validate('mimes:jpg,jpeg,png,webp', message: 'Only JPG, JPEG, PNG, or WebP images are allowed.')]
    #[Validate('max:10240', message: 'The project cover image must not exceed 10MB.')]
    #[Validate(
        'dimensions:min_width=1200,min_height=630,max_width=4000,max_height=4000',
        message: 'The project cover image must be between 1200×630 and 4000×4000 pixels.'
    )]
    public $coverImage = null;

    public ?string $live_url = null;

    public ?string $github_url = null;

    public bool $is_featured = false;

    public bool $is_published = false;

    public function updatedTitle(string $value): void
    {
        $this->slug = Str::slug($value);
    }

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
                'unique:projects,slug',
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

        $coverImagePath = null;

        if ($this->coverImage) {
            $coverImagePath = $this->coverImage->store(
                'projects',
                'public'
            );
        }

        try {
            Project::create([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'summary' => $validated['summary'],
                'description' => $validated['description'],
                'cover_image' => $coverImagePath,
                'live_url' => $validated['live_url'] ?: null,
                'github_url' => $validated['github_url'] ?: null,
                'is_featured' => $validated['is_featured'],
                'is_published' => $validated['is_published'],
            ]);
        } catch (\Throwable $e) {
            if ($coverImagePath) {
                Storage::disk('public')->delete($coverImagePath);
            }

            throw $e;
        }

        $this->redirectRoute('dashboard.projects.index', navigate: true);
        Flux::toast(
            heading: 'Project created',
            text: 'The project has been created successfully.',
            variant: 'success',
        );
    }

    public function removeCoverImage(): void
    {
        if ($this->coverImage) {
            $this->coverImage->delete();
        }

        $this->coverImage = null;

        $this->resetValidation('coverImage');
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
            Create Project
        </flux:heading>

        <flux:text class="mt-1">
            Create a new Clean Stack project.
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
                        placeholder="Enter project title..."
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
                        placeholder="project-url-slug"
                        readonly
                        :invalid="$errors->has('slug')"
                    />

                    <flux:description>
                        Automatically generated from the title.
                    </flux:description>

                    <flux:error name="slug"/>

                </flux:field>


                {{-- Summary --}}
                <flux:field>

                    <flux:label>
                        Summary
                    </flux:label>

                    <flux:textarea
                        wire:model="summary"
                        placeholder="Write a short summary of the project..."
                        rows="4"
                        :invalid="$errors->has('summary')"
                    />

                    <flux:description>
                        A concise summary used in the public Projects listing.
                    </flux:description>

                    <flux:error name="summary"/>

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
                                            aria-label="Remove project cover image"
                                        />

                                    </x-slot>

                                </flux:file-item>

                            </div>

                        @endif

                    </div>

                </flux:field>


                {{-- Description --}}
                <flux:field>

                    <flux:editor
                        wire:model="description"
                        label="Description"
                        description="Write and format the full project description."
                        placeholder="Start writing your project description..."
                        :invalid="$errors->has('description')"
                        toolbar="heading | bold italic underline strike | bullet ordered blockquote | subscript superscript highlight code | link | align ~ undo redo | copy"
                        class="**:data-[slot=content]:min-h-[400px]"
                    />

                </flux:field>


                {{-- Live URL --}}
                <flux:field>

                    <flux:label>
                        Live Demo URL
                    </flux:label>

                    <flux:input
                        wire:model="live_url"
                        type="text"
                        placeholder="https://example.com"
                        :invalid="$errors->has('live_url')"
                    />

                    <flux:description>
                        The public URL of the live project.
                    </flux:description>

                    <flux:error name="live_url"/>

                </flux:field>


                {{-- GitHub URL --}}
                <flux:field>

                    <flux:label>
                        GitHub URL
                    </flux:label>

                    <flux:input
                        wire:model="github_url"
                        type="text"
                        placeholder="https://github.com/..."
                        :invalid="$errors->has('github_url')"
                    />

                    <flux:description>
                        The GitHub repository URL for this project.
                    </flux:description>

                    <flux:error name="github_url"/>

                </flux:field>


                {{-- Featured --}}
                <flux:field>

                    <flux:checkbox
                        wire:model="is_featured"
                        label="Featured project"
                        description="Show this project as a featured project where applicable."
                    />

                    <flux:error name="is_featured"/>

                </flux:field>


                {{-- Published --}}
                <flux:field>

                    <flux:switch
                        wire:model.live="is_published"
                        label="Published project"
                        description="Published projects are visible on the public Projects page."
                    />

                    <flux:error name="is_published"/>

                </flux:field>


                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-6 sm:flex-row sm:justify-end">

                    <flux:button
                        href="{{ route('dashboard.projects.index') }}"
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
                            Create Project
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

