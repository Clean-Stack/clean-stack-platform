<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription ?? 'Clean Stack builds clean, modern websites and web applications focused on performance, usability, and clean code.' }}">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="robots" content="{{ $robots ?? 'index, follow' }}">

    @if (filled($canonicalUrl ?? null))
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if (filled($articleSchema ?? null))
        <script type="application/ld+json">
            {!! json_encode(
                $articleSchema,
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_PRETTY_PRINT
            ) !!}
        </script>
    @endif
    {{-- =========================================================
         OPEN GRAPH
         ========================================================= --}}
    @if (isset($ogTitle))
        <meta property="og:title" content="{{ $ogTitle }}" >
        <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? '' }}">
        <meta property="og:type" content="{{ $ogType ?? 'website' }}" >
        <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}" >
        <meta property="og:site_name" content="Clean Stack">
        @if (filled($ogImage ?? null))
            <meta property="og:image" content="{{ $ogImage }}" >
        @endif
    @endif

    {{-- =========================================================
         TWITTER / X
         ========================================================= --}}
    @if (isset($twitterTitle))

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $twitterTitle }}">
        <meta name="twitter:description" content="{{ $twitterDescription ?? $metaDescription ?? '' }}">
        @if (filled($twitterImage ?? null))
            <meta name="twitter:image" content="{{ $twitterImage }}">
        @endif
    @endif

    <title>{{ $title ?? 'Clean Stack' }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

</head>

<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">


<flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">

    <div class="mx-auto flex w-full max-w-7xl items-center px-6 lg:px-8">

        {{-- Mobile Menu --}}
        <flux:sidebar.toggle
            class="lg:hidden mr-2"
            icon="bars-2"
            inset="left"
            aria-label="Open navigation menu"
        />
        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            wire:navigate
            class="shrink-0"
        >
            <x-app-logo />
        </a>

        <flux:spacer />

        {{-- Desktop Navigation --}}
        <flux:navbar class="hidden lg:flex">
            <flux:navbar.item
                href="{{ route('home') }}"
                :current="request()->routeIs('home')"
                wire:navigate
            >
                Home
            </flux:navbar.item>

            <flux:navbar.item
                href="{{ route('blog') }}"
                :current="request()->routeIs('blog*')"
                wire:navigate
            >
                Blog
            </flux:navbar.item>
            <flux:navbar.item
                href="{{ route('services.index') }}"
                :current="request()->routeIs('services.*')"
                wire:navigate
            >
                Services
            </flux:navbar.item>

            <flux:navbar.item
                href="{{ route('projects.index') }}"
                :current="request()->routeIs('projects.*')"
                wire:navigate
            >
                Projects
            </flux:navbar.item>

            <flux:navbar.item
                href="{{ route('about') }}"
                :current="request()->routeIs('about')"
                wire:navigate
            >
                About
            </flux:navbar.item>

            <flux:navbar.item
                href="{{ route('contact') }}"
                :current="request()->routeIs('contact')"
                wire:navigate
            >
                Contact
            </flux:navbar.item>

        </flux:navbar>

        <flux:spacer />

        {{-- Header Actions --}}
        <div class="flex items-center gap-2">

            {{-- Theme --}}
            <flux:button
                x-data
                variant="subtle"
                square
                aria-label="Toggle color scheme"
                x-on:click="
                    $flux.appearance =
                    $flux.appearance === 'light' ? 'dark': $flux.appearance === 'dark'? 'system': 'light'">
                <flux:icon.sun
                    x-cloak
                    x-show="$flux.appearance === 'light'"
                    variant="mini"
                    class="text-zinc-500 dark:text-white"
                />

                <flux:icon.moon
                    x-cloak
                    x-show="$flux.appearance === 'dark'"
                    variant="mini"
                    class="text-zinc-500 dark:text-white"
                />

                <flux:icon.computer-desktop
                    x-cloak
                    x-show="$flux.appearance === 'system'"
                    variant="mini"
                    class="text-zinc-500 dark:text-white"
                />
            </flux:button>

            {{-- Login & Header CTA --}}
            <div class="hidden lg:inline-flex gap-2">
                @guest
                    <flux:button
                        variant="subtle"
                        href="{{ route('login') }}"
                        wire:navigate
                    >
                        Log in
                    </flux:button>
                @else
                    <flux:button
                        variant="subtle"
                        href="{{ route('dashboard') }}"
                        wire:navigate
                    >
                        Dashboard
                    </flux:button>
                @endguest
                <flux:button
                    variant="primary"
                    href="{{ route('lets-talk') }}"
                    wire:navigate
                >
                    Let's Talk
                </flux:button>
            </div>

        </div>

    </div>

</flux:header>
{{-- =========================================================
     MOBILE NAVIGATION
     ========================================================= --}}

<flux:sidebar
    collapsible="mobile"
    class="lg:hidden border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">

    <flux:sidebar.header>

        <a
            href="{{ route('home') }}"
            wire:navigate
            class="px-2"
        >
            <x-app-logo />
        </a>

        <flux:sidebar.collapse
            class="ml-auto"
            aria-label="Close navigation menu"
        />

    </flux:sidebar.header>


    <flux:sidebar.nav>

        <flux:sidebar.item
            icon="home"
            href="{{ route('home') }}"
            :current="request()->routeIs('home')"
            wire:navigate
        >
            Home
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="code-bracket"
            href="{{ route('services.index') }}"
            :current="request()->routeIs('services.*')"
            wire:navigate
        >
            Services
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="folder-git-2"
            href="{{ route('projects.index') }}"
            :current="request()->routeIs('projects.*')"
            wire:navigate
        >
            Projects
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="information-circle"
            href="{{ route('about') }}"
            :current="request()->routeIs('about')"
            wire:navigate
        >
            About
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="chat-bubble-left-right"
            href="{{ route('contact') }}"
            :current="request()->routeIs('contact')"
            wire:navigate
        >
            Contact
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="newspaper"
            href="{{ route('blog') }}"
            :current="request()->routeIs('blog*')"
            wire:navigate
        >
            Blog
        </flux:sidebar.item>

    </flux:sidebar.nav>


    <flux:sidebar.spacer />


    <flux:sidebar.nav>
        @guest
            <flux:sidebar.item
                icon="arrow-right-end-on-rectangle"
                href="{{ route('login') }}"
                :current="request()->routeIs('login')"
                wire:navigate
            >
                Log in
            </flux:sidebar.item>
        @else
            <flux:sidebar.item
                icon="rectangle-group"
                href="{{ route('dashboard') }}"
                :current="request()->routeIs('dashboard')"
                wire:navigate
            >
                Dashboard
            </flux:sidebar.item>
        @endguest
        <flux:sidebar.item
            icon="paper-airplane"
            href="{{ route('lets-talk') }}"
            :current="request()->routeIs('lets-talk')"
            wire:navigate
        >
            Let's Talk
        </flux:sidebar.item>

    </flux:sidebar.nav>

</flux:sidebar>

<main>
    {{ $slot }}
</main>


<footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">

    <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">

        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <flux:heading size="sm">
                    Clean Stack
                </flux:heading>

                <flux:text
                    size="sm"
                    class="mt-1"
                >
                    Building clean, modern web experiences.
                </flux:text>

            </div>

            <flux:text size="sm">
                © {{ date('Y') }} Clean Stack. All rights reserved.
            </flux:text>

        </div>

    </div>

</footer>


@fluxScripts


@livewireScripts

</body>

</html>
