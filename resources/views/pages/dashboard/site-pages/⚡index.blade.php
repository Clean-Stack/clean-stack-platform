<?php

use App\Models\SitePage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::app')]
#[Title('Site Pages')]
class extends Component
{
    public function with(): array
    {
        return [
            'pages' => SitePage::query()
                ->whereIn('slug', [
                    'home',
                    'about',
                    'contact',
                    'lets-talk',
                ])
                ->orderByRaw("
                    FIELD(
                        slug,
                        'home',
                        'about',
                        'contact',
                        'lets-talk'
                    )
                ")
                ->get(),
        ];
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <flux:heading size="xl">
                Site Pages
            </flux:heading>

            <flux:text class="mt-1">
                Manage the content of your Clean Stack website pages.
            </flux:text>

        </div>

    </div>


    {{-- Site Pages Table --}}
    <flux:table>

        <flux:table.columns>

            <flux:table.column>
                Page
            </flux:table.column>

            <flux:table.column>
                Slug
            </flux:table.column>

            <flux:table.column>
                Actions
            </flux:table.column>

        </flux:table.columns>


        <flux:table.rows>

            @forelse ($pages as $page)

                <flux:table.row wire:key="site-page-{{ $page->id }}">

                    <flux:table.cell>

                        <div class="flex items-center gap-3">

                            @if ($page->slug === 'home')

                                <flux:icon
                                    name="home"
                                    variant="outline"
                                    class="size-5"
                                />

                            @elseif ($page->slug === 'about')

                                <flux:icon
                                    name="information-circle"
                                    variant="outline"
                                    class="size-5"
                                />

                            @elseif ($page->slug === 'contact')

                                <flux:icon
                                    name="chat-bubble-left-right"
                                    variant="outline"
                                    class="size-5"
                                />

                            @elseif ($page->slug === 'lets-talk')

                                <flux:icon
                                    name="paper-airplane"
                                    variant="outline"
                                    class="size-5"
                                />

                            @endif


                            <div>

                                <div class="font-medium">
                                    {{ $page->title }}
                                </div>

                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ match ($page->slug) {
                                        'home' => 'Main landing page of Clean Stack.',
                                        'about' => 'Information about Clean Stack and its approach.',
                                        'contact' => 'Contact options and ways to get in touch.',
                                        'lets-talk' => 'Project inquiries and client conversations.',
                                        default => '',
                                    } }}
                                </div>

                            </div>

                        </div>

                    </flux:table.cell>


                    <flux:table.cell>

                        <span class="text-zinc-600 dark:text-zinc-400">
                            /{{ $page->slug }}
                        </span>

                    </flux:table.cell>


                    <flux:table.cell>

                        <div class="flex items-center gap-2">

                            @if ($page->slug === 'home')

                                <flux:button
                                    href="{{ route('dashboard.site-pages.home.edit') }}"
                                    variant="outline"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>

                            @elseif ($page->slug === 'about')

                                <flux:button
                                    href="{{ route('dashboard.site-pages.about.edit') }}"
                                    variant="outline"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>

                            @elseif ($page->slug === 'contact')

                                <flux:button
                                    href="{{ route('dashboard.site-pages.contact.edit') }}"
                                    variant="outline"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>

                            @elseif ($page->slug === 'lets-talk')

                                <flux:button
                                    href="{{ route('dashboard.site-pages.lets-talk.edit') }}"
                                    variant="outline"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>

                            @endif

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @empty

                <flux:table.row>

                    <flux:table.cell colspan="3">

                        <div class="py-8 text-center">

                            <flux:text>
                                No site pages found.
                            </flux:text>

                        </div>

                    </flux:table.cell>

                </flux:table.row>

            @endforelse

        </flux:table.rows>

    </flux:table>

</div>

