<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} &middot; {{ \App\Facades\Settings::get('general', 'site_name', '101GSD') }}</title>
    @include('partials.appearance-vars')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[color-mix(in_srgb,var(--color-surface)_100%,black_3%)] font-sans text-[var(--color-ink)] antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="flex w-64 flex-shrink-0 flex-col bg-[var(--color-surface-dark)] text-white">
            <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <img src="{{ asset('images/brand/logo.png') }}" alt="Logo" class="h-8 w-auto">
                <span class="font-[var(--font-display)] text-lg font-semibold">
                    {{ \App\Facades\Settings::get('general', 'site_name', '101GSD') }}
                </span>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-6 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-primary)] text-[var(--color-primary-foreground)]' : 'text-white/80 hover:bg-white/10' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.pages.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.pages.*') ? 'bg-[var(--color-primary)] text-[var(--color-primary-foreground)]' : 'text-white/80 hover:bg-white/10' }}">
                    Pages
                </a>
                <a href="#"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium text-white/50">
                    Blog &amp; News <span class="ml-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px]">soon</span>
                </a>
                <a href="#"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium text-white/50">
                    Menus <span class="ml-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px]">soon</span>
                </a>
                <a href="{{ route('admin.settings.general') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-[var(--color-primary)] text-[var(--color-primary-foreground)]' : 'text-white/80 hover:bg-white/10' }}">
                    Settings
                </a>
            </nav>

            <div class="border-t border-white/10 px-4 py-4">
                <p class="truncate text-xs text-white/50">{{ auth()->user()?->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-[var(--color-primary)] hover:underline">
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1">
            <header class="flex items-center justify-between border-b border-black/5 bg-white px-8 py-4">
                <h1 class="font-[var(--font-display)] text-xl font-semibold text-[var(--color-ink)]">
                    {{ $title ?? 'Dashboard' }}
                </h1>
                <a href="/" target="_blank" class="text-sm font-medium text-[var(--color-secondary)] hover:underline">
                    View site &rarr;
                </a>
            </header>

            <main class="px-8 py-8">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
