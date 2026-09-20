@extends('layouts.app')

@section('content')
    {{-- Login styled after the "Talks on Dogs" prototype: blue secondary accent,
         clean white card, minimal chrome. --}}
    <section class="flex min-h-[70vh] items-center justify-center bg-[color-mix(in_srgb,var(--color-surface)_100%,black_3%)] px-6 py-16">
        <div class="w-full max-w-md rounded-2xl border border-black/5 bg-white p-8 shadow-lg shadow-black/5 md:p-10">
            <div class="mb-8 text-center">
                <img src="{{ asset('images/brand/logo.png') }}" alt="{{ \App\Facades\Settings::get('general', 'site_name', '101GSD') }}" class="mx-auto mb-4 h-12 w-auto">
                <h1 class="font-[var(--font-display)] text-2xl font-semibold text-[var(--color-ink)]">
                    Welcome back
                </h1>
                <p class="mt-1 text-sm text-[var(--color-ink-muted)]">
                    Sign in to manage your club and content.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Email address</label>
                    <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm text-[var(--color-ink)] focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm text-[var(--color-ink)] focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <label class="flex items-center gap-2 text-sm text-[var(--color-ink-muted)]">
                    <input type="checkbox" name="remember" class="rounded border-black/20 text-[var(--color-secondary)] focus:ring-[var(--color-secondary)]/30">
                    Remember me
                </label>

                <button type="submit"
                        class="w-full rounded-[var(--button-radius,9999px)] bg-[var(--color-secondary)] px-4 py-3 text-sm font-semibold text-[var(--color-secondary-foreground)] transition hover:opacity-90">
                    Sign In
                </button>
            </form>
        </div>
    </section>
@endsection
