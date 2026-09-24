<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    {{-- Dashboard is available to every authenticated + verified user --}}
                    <flux:sidebar.item
                        icon="rectangle-group"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    {{-- Management navigation is available to admins only --}}
                    @if (auth()->user()->isAdmin())
                        <flux:sidebar.item
                            icon="document-text"
                            :href="route('dashboard.posts.index')"
                            :current="request()->routeIs('dashboard.posts.*')"
                            wire:navigate
                        >
                            {{ __('Posts') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="squares-2x2"
                            :href="route('dashboard.categories.index')"
                            :current="request()->routeIs('dashboard.categories.*')"
                            wire:navigate
                        >
                            {{ __('Categories') }}
                        </flux:sidebar.item>

                        <flux:navlist.item
                            icon="inbox"
                            :href="route('dashboard.leads')"
                            :current="request()->routeIs('dashboard.leads*')"
                            wire:navigate
                        >
                            {{ __('Leads') }}
                        </flux:navlist.item>

                        <flux:navlist.item
                            icon="code-bracket"
                            :href="route('dashboard.services.index')"
                            :current="request()->routeIs('dashboard.services.*')"
                            wire:navigate
                        >
                            {{ __('Services') }}
                        </flux:navlist.item>

                        <flux:navlist.item
                            icon="folder-git-2"
                            :href="route('dashboard.projects.index')"
                            :current="request()->routeIs('dashboard.projects.*')"
                            wire:navigate
                        >
                            {{ __('Projects') }}
                        </flux:navlist.item>

                        <flux:navlist.item
                            icon="window"
                            :href="route('dashboard.site-pages.index')"
                            :current="request()->routeIs('dashboard.site-pages.*')"
                            wire:navigate
                        >
                            {{ __('Site Pages') }}
                        </flux:navlist.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="globe-alt"
                    :href="route('home')"
                    wire:navigate
                >
                    {{ __('View Website') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
