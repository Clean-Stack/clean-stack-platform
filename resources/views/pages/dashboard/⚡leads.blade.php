<?php

use App\Models\Lead;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Layout('layouts::app')]
#[Title('Leads')]
class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = 'all';

    public int $perPage = 6;

    public ?int $leadToDelete = null;

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

    public function confirmDelete(int $leadId): void
    {
        $this->leadToDelete = $leadId;

        $this->showDeleteModal = true;
    }

    public function deleteLead(): void
    {
        if ($this->leadToDelete === null) {
            return;
        }

        Lead::query()
            ->findOrFail($this->leadToDelete)
            ->delete();

        $this->leadToDelete = null;

        $this->showDeleteModal = false;

        Flux::toast(
            heading: 'Lead deleted',
            text: 'The lead has been deleted successfully.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        $query = Lead::query();

        $query
            ->when(
                $this->search,
                fn($query) => $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('company', 'like', '%' . $this->search . '%');
                })
            )
            ->when(
                $this->status !== 'all',
                fn($query) => $query->where('status', $this->status)
            );

        return [
            'leads' => $query
                ->latest()
                ->paginate($this->perPage),

            'totalLeads' => Lead::count(),

            'newLeads' => Lead::where('status', 'new')->count(),

            'qualifiedLeads' => Lead::where('status', 'qualified')->count(),

            'wonLeads' => Lead::where('status', 'won')->count(),
        ];
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            Leads
        </flux:heading>

        <flux:text class="mt-1">
            Manage project inquiries and follow up with potential clients.
        </flux:text>
    </div>


    {{-- Lead Statistics --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <flux:card>
            <flux:text size="sm">
                Total Leads
            </flux:text>

            <flux:heading size="2xl" class="mt-2">
                {{ $totalLeads }}
            </flux:heading>
        </flux:card>


        <flux:card>
            <flux:text size="sm">
                New
            </flux:text>

            <flux:heading size="2xl" class="mt-2">
                {{ $newLeads }}
            </flux:heading>
        </flux:card>


        <flux:card>
            <flux:text size="sm">
                Qualified
            </flux:text>

            <flux:heading size="2xl" class="mt-2">
                {{ $qualifiedLeads }}
            </flux:heading>
        </flux:card>


        <flux:card>
            <flux:text size="sm">
                Won
            </flux:text>

            <flux:heading size="2xl" class="mt-2">
                {{ $wonLeads }}
            </flux:heading>
        </flux:card>

    </div>


    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">

        <div class="flex-1">

            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name, email, or company..."
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

                <flux:select.option value="new">
                    New
                </flux:select.option>

                <flux:select.option value="contacted">
                    Contacted
                </flux:select.option>

                <flux:select.option value="qualified">
                    Qualified
                </flux:select.option>

                <flux:select.option value="proposal">
                    Proposal
                </flux:select.option>

                <flux:select.option value="won">
                    Won
                </flux:select.option>

                <flux:select.option value="lost">
                    Lost
                </flux:select.option>
            </flux:select>

        </div>

    </div>


    {{-- Leads Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Name
            </flux:table.column>

            <flux:table.column>
                Email
            </flux:table.column>

            <flux:table.column>
                Service
            </flux:table.column>

            <flux:table.column>
                Status
            </flux:table.column>

            <flux:table.column>
                Received
            </flux:table.column>

            <flux:table.column>
                Action
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($leads as $lead)

                <flux:table.row wire:key="lead-{{ $lead->id }}">

                    <flux:table.cell>
                        {{ $lead->name }}
                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $lead->email }}
                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $lead->service ?? '—' }}
                    </flux:table.cell>


                    <flux:table.cell>

                        <flux:badge size="sm">
                            {{ ucfirst($lead->status) }}
                        </flux:badge>

                    </flux:table.cell>


                    <flux:table.cell>
                        {{ $lead->created_at->format('M d, Y') }}
                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            <flux:button
                                href="{{ route('dashboard.leads.show', $lead) }}"
                                variant="outline"
                                size="sm"
                                wire:navigate
                            >
                                View
                            </flux:button>


                            <flux:button
                                type="button"
                                variant="outline"
                                size="sm"
                                wire:click="confirmDelete({{ $lead->id }})"
                            >
                                Delete
                            </flux:button>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @empty

                <flux:table.row>

                    <flux:table.cell colspan="6">

                        <div class="py-8 text-center">

                            <flux:text>
                                No leads found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>


    {{-- Infinite Loading --}}
    @if ($leads->hasMorePages())

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
                    Delete Lead?
                </flux:heading>

                <flux:text class="mt-2">
                    You're about to delete this lead. This action cannot be undone.
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
                    wire:click="deleteLead"
                    wire:loading.attr="disabled"
                    wire:target="deleteLead"
                >

                    <span
                        wire:loading.remove
                        wire:target="deleteLead"
                    >
                        Delete Lead
                    </span>

                    <span
                        wire:loading
                        wire:target="deleteLead"
                    >
                        Deleting...
                    </span>

                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>

