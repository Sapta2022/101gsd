@php
    $siteName = \App\Facades\Settings::get('general', 'site_name', '101GSD');
    $logo = \App\Facades\Settings::get('general', 'logo', '/images/brand/logo.png');
    $headerItems = \App\Models\Menu::where('slug', 'header')->first()?->items ?? collect();
@endphp
<header class="bg-white border-b border-black/5">
    <div class="mx-auto flex max-w-(--container-width) items-center justify-between px-6 py-4">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset($logo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            @forelse($headerItems as $item)
                <a href="{{ $item->resolvedUrl() }}" target="{{ $item->target }}"
                   class="text-sm font-medium text-[var(--color-ink)] hover:text-[var(--color-primary)] transition-colors">
                    {{ $item->label }}
                </a>
            @empty
                <a href="{{ url('/') }}" class="text-sm font-medium hover:text-[var(--color-primary)]">Home</a>
                <a href="{{ url('/shows') }}" class="text-sm font-medium hover:text-[var(--color-primary)]">Shows</a>
                <a href="{{ url('/clubs') }}" class="text-sm font-medium hover:text-[var(--color-primary)]">Clubs</a>
                <a href="{{ url('/blog') }}" class="text-sm font-medium hover:text-[var(--color-primary)]">Blog</a>
                <a href="{{ url('/contact') }}" class="text-sm font-medium hover:text-[var(--color-primary)]">Contact</a>
            @endforelse
        </nav>

        <a href="{{ url('/login') }}"
           class="rounded-[var(--radius-button)] bg-[var(--color-primary)] px-5 py-2.5 text-sm font-semibold text-[var(--color-primary-foreground)] hover:opacity-90 transition-opacity">
            Log In
        </a>
    </div>
</header>
