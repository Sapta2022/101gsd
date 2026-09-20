@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <p class="text-sm text-[var(--color-ink-muted)]">
            Welcome back, {{ auth()->user()->name }}. Here's a quick snapshot of the site.
        </p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Pages</p>
            <p class="mt-2 font-[var(--font-display)] text-3xl font-semibold text-[var(--color-ink)]">{{ $stats['pages'] }}</p>
            <p class="mt-1 text-xs text-[var(--color-ink-muted)]">{{ $stats['published_pages'] }} published</p>
        </div>
        <div class="rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Posts</p>
            <p class="mt-2 font-[var(--font-display)] text-3xl font-semibold text-[var(--color-ink)]">{{ $stats['posts'] }}</p>
            <p class="mt-1 text-xs text-[var(--color-ink-muted)]">{{ $stats['published_posts'] }} published</p>
        </div>
        <div class="rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Role</p>
            <p class="mt-2 font-[var(--font-display)] text-xl font-semibold text-[var(--color-ink)]">
                {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Club Admin' }}
            </p>
        </div>
        <div class="rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Status</p>
            <p class="mt-2 font-[var(--font-display)] text-xl font-semibold text-emerald-600">Live</p>
        </div>
    </div>

    <div class="mt-10 rounded-xl border border-dashed border-black/10 bg-white/60 p-6 text-sm text-[var(--color-ink-muted)]">
        Pages, Blog &amp; News, Menus and Settings screens are next up in this build — they'll appear
        in the sidebar as each one lands.
    </div>
@endsection
