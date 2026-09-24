<?php

use App\Models\Project;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new
#[Layout('layouts::app')]
#[Title('Projects')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = 'all';

    #[Url(history: true)]
    public string $featured = 'all';

    public int $perPage = 6;

    public ?int $projectToDelete = null;

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

    public function updatedFeatured(): void
    {
        $this->resetPage();

        $this->perPage = 6;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function confirmDelete(int $projectId): void
    {
        $this->projectToDelete = $projectId;

        $this->showDeleteModal = true;
    }

    public function deleteProject(): void
    {
        if ($this->projectToDelete === null) {
            return;
        }

        $project = Project::query()
            ->findOrFail($this->projectToDelete);

        /*
         * Delete the project's cover image from storage
         * before removing the database record.
         */
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }

        $project->delete();

        $this->projectToDelete = null;

        $this->showDeleteModal = false;

        Flux::toast(
            heading: 'Project deleted',
            text: 'The project has been deleted successfully.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'projects' => Project::query()
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
                    fn($query) => $query->where(
                        'is_published',
                        $this->status === 'published'
                    )
                )
                ->when(
                    $this->featured !== 'all',
                    fn($query) => $query->where(
                        'is_featured',
                        $this->featured === 'featured'
                    )
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
                Projects
            </flux:heading>

            <flux:text class="mt-1">
                Create and manage Clean Stack projects.
            </flux:text>

        </div>


        <div class="flex items-center gap-2">

            <flux:button
                variant="primary"
                href="{{ route('dashboard.projects.create') }}"
                wire:navigate
            >
                Create Project
            </flux:button>

        </div>

    </div>


    {{-- Filters --}}
    <div class="grid gap-4 sm:grid-cols-3">

        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search projects..."
            icon="magnifying-glass"
        />


        <flux:select
            wire:model.live="status"
            variant="listbox"
            prefix="Status : "
        >

            <flux:select.option value="all">
                All
            </flux:select.option>

            <flux:select.option value="published">
                Published
            </flux:select.option>

            <flux:select.option value="unpublished">
                Unpublished
            </flux:select.option>

        </flux:select>


        <flux:select
            wire:model.live="featured"
            variant="listbox"
            prefix="Featured : "
        >

            <flux:select.option value="all">
                All
            </flux:select.option>

            <flux:select.option value="featured">
                Featured
            </flux:select.option>

            <flux:select.option value="not_featured">
                Not Featured
            </flux:select.option>

        </flux:select>

    </div>


    {{-- Projects Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Project
            </flux:table.column>

            <flux:table.column>
                Slug
            </flux:table.column>

            <flux:table.column>
                Status
            </flux:table.column>

            <flux:table.column>
                Featured
            </flux:table.column>

            <flux:table.column>
                Actions
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($projects as $project)

                <flux:table.row wire:key="project-{{ $project->id }}">

                    <flux:table.cell>

                        <div class="flex items-center gap-3">

                            @if ($project->cover_image)

                                <img
                                    src="{{ Storage::url($project->cover_image) }}"
                                    alt="{{ $project->title }}"
                                    class="size-12 rounded-lg object-cover"
                                >

                            @else

                                <div
                                    class="flex size-12 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">

                                    <flux:icon
                                        name="folder-git-2"
                                        class="size-5 text-zinc-400"
                                    />

                                </div>

                            @endif


                            <div class="min-w-0">

                                <flux:text class="truncate font-medium">
                                    {{ Str::limit($project->title, 45) }}
                                </flux:text>

                            </div>

                        </div>

                    </flux:table.cell>


                    <flux:table.cell>
                        {{ Str::limit($project->slug, 45) }}
                    </flux:table.cell>


                    <flux:table.cell>

                        @if ($project->is_published)

                            <flux:badge
                                variant="success"
                                size="sm"
                            >
                                Published
                            </flux:badge>

                        @else

                            <flux:badge
                                variant="subtle"
                                size="sm"
                            >
                                Unpublished
                            </flux:badge>

                        @endif

                    </flux:table.cell>


                    <flux:table.cell>

                        @if ($project->is_featured)

                            <flux:badge
                                variant="warning"
                                size="sm"
                            >
                                Featured
                            </flux:badge>

                        @else

                            <flux:text size="sm">
                                —
                            </flux:text>

                        @endif

                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            <flux:button
                                href="{{ route('dashboard.projects.edit', $project) }}"
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
                                wire:click="confirmDelete({{ $project->id }})"
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
                                No projects found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>


    {{-- Infinite Loading --}}
    @if ($projects->hasMorePages())

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


    <div
        wire:loading
        wire:target="loadMore"
        class="mt-6 flex justify-center"
    >

        <flux:text size="sm">
            Loading more projects...
        </flux:text>

    </div>


    {{-- Delete Modal --}}
    <flux:modal
        wire:model.self="showDeleteModal"
        class="min-w-[22rem]"
    >

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Project?
                </flux:heading>

                <flux:text class="mt-2">
                    You're about to delete this project. This action cannot be undone.
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
                    wire:click="deleteProject"
                    wire:loading.attr="disabled"
                    wire:target="deleteProject"
                >

                    <span
                        wire:loading.remove
                        wire:target="deleteProject"
                    >
                        Delete Project
                    </span>

                    <span
                        wire:loading
                        wire:target="deleteProject"
                    >
                        Deleting...
                    </span>

                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>

