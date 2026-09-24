@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-md">
            <x-app-logo-icon class="block max-w-full max-h-full w-auto h-auto text-black dark:text-white" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-md">
            <x-app-logo-icon class="block max-w-full max-h-full w-auto h-auto text-black dark:text-white" />
        </x-slot>
    </flux:brand>
@endif
