@extends('layouts.app')

@section('content')
    {{-- Hero: dark surface, gold accent headline, teaser "registration" card matching
         the reference hero/form mockup. The real registration flow is a later phase —
         this section is the Phase-1 visual/skeleton teaser only. --}}
    <section class="relative overflow-hidden bg-[var(--color-surface-dark)] text-white">
        <div class="absolute inset-0 opacity-20 [background:radial-gradient(circle_at_20%_20%,var(--color-primary),transparent_55%)]"></div>

        <div class="relative mx-auto grid max-w-[var(--container-width,1280px)] gap-12 px-6 py-20 md:grid-cols-2 md:items-center md:py-28">
            <div>
                <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-medium uppercase tracking-widest text-[var(--color-primary)]">
                    {{ \App\Facades\Settings::get('general', 'tagline', 'Dog Show Registration, Simplified') }}
                </p>

                <h1 class="font-[var(--font-display)] text-4xl font-semibold leading-tight md:text-5xl lg:text-6xl">
                    Register for dog shows
                    <span class="text-[var(--color-primary)]">online</span>,
                    in minutes.
                </h1>

                <p class="mt-6 max-w-lg text-base text-white/70 md:text-lg">
                    A single portal for clubs and exhibitors — entries, payments and
                    confirmations, without the paperwork.
                </p>

                <ul class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-sm font-medium text-white/90">
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--color-primary)]"></span>
                        Secure
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--color-primary)]"></span>
                        Automated
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--color-primary)]"></span>
                        Instant Confirmation
                    </li>
                </ul>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="/login"
                       class="rounded-[var(--button-radius,9999px)] bg-[var(--color-primary)] px-6 py-3 text-sm font-semibold text-[var(--color-primary-foreground)] shadow-lg shadow-black/20 transition hover:opacity-90">
                        Get Started
                    </a>
                    <a href="#shows"
                       class="rounded-[var(--button-radius,9999px)] border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Browse Upcoming Shows
                    </a>
                </div>
            </div>

            {{-- Teaser card echoing the mockup's white rounded input-style elements --}}
            <div class="relative">
                <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-6 backdrop-blur-sm shadow-2xl shadow-black/30 md:p-8">
                    <p class="mb-6 font-[var(--font-display)] text-lg font-semibold text-white">
                        Find a show
                    </p>

                    <div class="space-y-4">
                        <div class="rounded-xl bg-white px-4 py-3 text-sm text-[var(--color-ink-muted)] shadow-sm">
                            Select a club or region
                        </div>
                        <div class="rounded-xl bg-white px-4 py-3 text-sm text-[var(--color-ink-muted)] shadow-sm">
                            Select a date
                        </div>
                        <div class="rounded-xl bg-white px-4 py-3 text-sm text-[var(--color-ink-muted)] shadow-sm">
                            Select a breed group
                        </div>
                        <button type="button"
                                class="w-full rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-primary-foreground)] transition hover:opacity-90">
                            Search Shows
                        </button>
                    </div>

                    <p class="mt-5 text-center text-xs text-white/50">
                        Full online entry &amp; payment coming online this phase.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Intro / value strip --}}
    <section class="mx-auto max-w-[var(--container-width,1280px)] px-6 py-16 md:py-20">
        <div class="grid gap-10 md:grid-cols-3">
            <div>
                <h3 class="font-[var(--font-display)] text-xl font-semibold text-[var(--color-ink)]">
                    For Exhibitors
                </h3>
                <p class="mt-3 text-sm leading-relaxed text-[var(--color-ink-muted)]">
                    Browse upcoming shows, register your dogs and manage entries from
                    one place, with instant confirmation.
                </p>
            </div>
            <div>
                <h3 class="font-[var(--font-display)] text-xl font-semibold text-[var(--color-ink)]">
                    For Clubs
                </h3>
                <p class="mt-3 text-sm leading-relaxed text-[var(--color-ink-muted)]">
                    Publish shows, track entries and payments, and manage your club's
                    public presence — without spreadsheets.
                </p>
            </div>
            <div>
                <h3 class="font-[var(--font-display)] text-xl font-semibold text-[var(--color-ink)]">
                    Always Up To Date
                </h3>
                <p class="mt-3 text-sm leading-relaxed text-[var(--color-ink-muted)]">
                    News, announcements and show results, published straight from each
                    club's dashboard.
                </p>
            </div>
        </div>
    </section>

    {{-- Latest posts (blog / news & updates), if any exist yet --}}
    @php
        $latestPosts = \App\Models\Post::query()->published()->latest('published_at')->limit(3)->get();
    @endphp

    @if($latestPosts->isNotEmpty())
        <section class="bg-[color-mix(in_srgb,var(--color-surface)_100%,black_2%)] py-16 md:py-20">
            <div class="mx-auto max-w-[var(--container-width,1280px)] px-6">
                <div class="mb-10 flex items-end justify-between">
                    <h2 class="font-[var(--font-display)] text-2xl font-semibold text-[var(--color-ink)] md:text-3xl">
                        Latest News &amp; Updates
                    </h2>
                    <a href="/news" class="text-sm font-semibold text-[var(--color-secondary)] hover:underline">
                        View all &rarr;
                    </a>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    @foreach($latestPosts as $post)
                        <a href="/{{ $post->type === 'blog' ? 'blog' : 'news' }}/{{ $post->slug }}"
                           class="group block rounded-xl border border-black/5 p-6 shadow-sm transition hover:shadow-md">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-[var(--color-secondary)]">
                                {{ $post->type === 'blog' ? 'Blog' : 'News & Updates' }}
                            </p>
                            <h3 class="font-[var(--font-display)] text-lg font-semibold text-[var(--color-ink)] group-hover:text-[var(--color-secondary)]">
                                {{ $post->title }}
                            </h3>
                            @if($post->excerpt)
                                <p class="mt-2 text-sm text-[var(--color-ink-muted)]">
                                    {{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
