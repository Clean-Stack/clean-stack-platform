<?php

use App\Models\Lead;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::app')]
#[Title('Lead Details')]
class extends Component {
    public Lead $lead;

    public string $status = '';

    public function mount(Lead $lead): void
    {
        $this->lead = $lead;
        $this->status = $lead->status;
    }

    public function updateStatus(): void
    {
        $validated = $this->validate([
            'status' => [
                'required',
                'in:new,contacted,qualified,proposal,won,lost',
            ],
        ]);

        $this->lead->update([
            'status' => $validated['status'],
        ]);

        $this->redirectRoute('dashboard.leads', navigate: true);
        Flux::toast(
            heading: 'Lead updated',
            text: 'The lead has been updated successfully.',
            variant: 'success',
        );
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>
        <flux:button
            href="{{ route('dashboard.leads') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Leads
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Lead Details
        </flux:heading>

        <flux:text class="mt-1">
            Review the project inquiry and manage its current status.
        </flux:text>
    </div>

    {{-- LEAD INFORMATION --}}
    <div class="grid gap-6 lg:grid-cols-3 mt-6">

        {{-- MAIN INFORMATION --}}
        <div class="space-y-6 lg:col-span-2">

            <flux:card>

                <flux:heading size="sm">
                    Lead Information
                </flux:heading>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">

                    <div>

                        <flux:text
                            size="sm"
                            class="text-zinc-400"
                        >
                            Name
                        </flux:text>

                        <flux:text class="mt-1">
                            {{ $lead->name }}
                        </flux:text>

                    </div>


                    <div>

                        <flux:text
                            size="sm"
                            class="text-zinc-400"
                        >
                            Email
                        </flux:text>

                        <flux:text class="mt-1">
                            {{ $lead->email }}
                        </flux:text>

                    </div>


                    @if ($lead->company)

                        <div>

                            <flux:text
                                size="sm"
                                class="text-zinc-400"
                            >
                                Company
                            </flux:text>

                            <flux:text class="mt-1">
                                {{ $lead->company }}
                            </flux:text>

                        </div>

                    @endif


                    @if ($lead->website)

                        <div>

                            <flux:text
                                size="sm"
                                class="text-zinc-400"
                            >
                                Website
                            </flux:text>

                            <flux:link
                                href="{{ $lead->website }}"
                                target="_blank"
                                class="mt-1 block"
                            >
                                {{ $lead->website }}
                            </flux:link>

                        </div>

                    @endif


                    @if ($lead->service)

                        <div>

                            <flux:text
                                size="sm"
                                class="text-zinc-400"
                            >
                                Service
                            </flux:text>

                            <flux:text class="mt-1">
                                {{ $lead->service }}
                            </flux:text>

                        </div>

                    @endif


                    <div>

                        <flux:text
                            size="sm"
                            class="text-zinc-400"
                        >
                            Received
                        </flux:text>

                        <flux:text class="mt-1">
                            {{ $lead->created_at->format('M d, Y H:i') }}
                        </flux:text>

                    </div>

                </div>

            </flux:card>


            {{-- MESSAGE --}}
            <flux:card>

                <flux:heading size="sm">
                    Project Inquiry
                </flux:heading>

                <flux:text class="mt-4 whitespace-pre-line">
                    {{ $lead->message }}
                </flux:text>

            </flux:card>

        </div>


        {{-- STATUS --}}
        <div>

            <flux:card>

                <flux:heading size="sm">
                    Lead Status
                </flux:heading>

                <flux:text
                    size="sm"
                    class="mt-2"
                >
                    Update the current stage of this lead.
                </flux:text>


                <form
                    wire:submit="updateStatus"
                    class="mt-6 space-y-5"
                >

                    <flux:select wire:model="status" variant="listbox" prefix="Status : ">
                        <flux:select.option value="new">New</flux:select.option>
                        <flux:select.option value="contacted">Contacted</flux:select.option>
                        <flux:select.option value="qualified">Qualified</flux:select.option>
                        <flux:select.option value="proposal">Proposal</flux:select.option>
                        <flux:select.option value="won">Won</flux:select.option>
                        <flux:select.option value="lost">Lost</flux:select.option>
                    </flux:select>
                    <flux:button
                        type="submit"
                        variant="primary"
                    >
                        Save Changes
                    </flux:button>

                </form>

            </flux:card>

        </div>

    </div>

</div>
