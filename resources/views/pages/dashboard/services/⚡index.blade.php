<?php

use App\Models\Service;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new
#[Layout('layouts::app')]
#[Title('Services')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = 'all';

    public int $perPage = 6;

    public ?int $serviceToDelete = null;

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

    public function confirmDelete(int $serviceId): void
    {
        $this->serviceToDelete = $serviceId;

        $this->showDeleteModal = true;
    }

    public function deleteService(): void
    {
        if ($this->serviceToDelete === null) {
            return;
        }

        Service::query()
            ->findOrFail($this->serviceToDelete)
            ->delete();

        $this->serviceToDelete = null;

        $this->showDeleteModal = false;

        Flux::toast(
            heading: 'Service deleted',
            text: 'The service has been deleted successfully.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'services' => Service::query()
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
                        'is_active',
                        $this->status === 'active'
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
                Services
            </flux:heading>

            <flux:text class="mt-1">
                Create and manage Clean Stack services.
            </flux:text>

        </div>


        <div class="flex items-center gap-2">

            <flux:button
                variant="primary"
                href="{{ route('dashboard.services.create') }}"
                wire:navigate
            >
                Create Service
            </flux:button>

        </div>

    </div>


    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">

        <div class="flex-1">

            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search services..."
                icon="magnifying-glass"
            />

        </div>


        <div class="w-full sm:w-48">

            <flux:select
                wire:model.live="status"
                variant="listbox"
                prefix="Status : "
            >
                <flux:select.option value="all">
                    All
                </flux:select.option>

                <flux:select.option value="active">
                    Active
                </flux:select.option>

                <flux:select.option value="inactive">
                    Inactive
                </flux:select.option>
            </flux:select>

        </div>

    </div>


    {{-- Services Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Service
            </flux:table.column>

            <flux:table.column>
                Featured
            </flux:table.column>

            <flux:table.column>
                Status
            </flux:table.column>

            <flux:table.column>
                Actions
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($services as $service)

                <flux:table.row wire:key="service-{{ $service->id }}">

                    <flux:table.cell>

                        <div class="flex items-center gap-3">

                            @if ($service->icon)

                                <flux:icon
                                    :name="$service->icon"
                                    variant="outline"
                                    class="size-5"
                                />

                            @else

                                <flux:icon
                                    name="code-bracket"
                                    variant="outline"
                                    class="size-5"
                                />

                            @endif

                            <div>

                                <div class="font-medium">
                                    {{ Str::limit($service->title, 90) }}
                                </div>

                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ Str::limit($service->short_description, 90) }}
                                </div>

                            </div>

                        </div>

                    </flux:table.cell>


                    <flux:table.cell>

                        @if ($service->is_featured)

                            <flux:badge size="sm">
                                Featured
                            </flux:badge>

                        @else

                            <span class="text-zinc-500">
                                —
                            </span>

                        @endif

                    </flux:table.cell>


                    <flux:table.cell>

                        <flux:badge size="sm">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </flux:badge>

                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            <flux:button
                                href="{{ route('dashboard.services.edit', $service) }}"
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
                                wire:click="confirmDelete({{ $service->id }})"
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
                                No services found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>


    {{-- Infinite Loading --}}
    @if ($services->hasMorePages())

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
                    Delete Service?
                </flux:heading>

                <flux:text class="mt-2">
                    You're about to delete this service. This action cannot be undone.
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
                    wire:click="deleteService"
                    wire:loading.attr="disabled"
                    wire:target="deleteService"
                >

                    <span
                        wire:loading.remove
                        wire:target="deleteService"
                    >
                        Delete Service
                    </span>

                    <span
                        wire:loading
                        wire:target="deleteService"
                    >
                        Deleting...
                    </span>

                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>

