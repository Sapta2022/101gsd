@extends('layouts.admin')

@section('content')
    @include('admin.settings._nav', ['active' => 'general'])

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.general.update') }}">
        @csrf
        @method('PUT')

        <div class="grid gap-6 rounded-xl border border-black/5 bg-white p-6 shadow-sm md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Site name</label>
                <input type="text" name="site_name" required value="{{ old('site_name', $general['site_name'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $general['tagline'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Logo URL</label>
                <input type="text" name="logo" value="{{ old('logo', $general['logo'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Dark logo URL</label>
                <input type="text" name="logo_dark" value="{{ old('logo_dark', $general['logo_dark'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Favicon URL</label>
                <input type="text" name="favicon" value="{{ old('favicon', $general['favicon'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Currency symbol</label>
                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $general['currency_symbol'] ?? '₹') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Timezone</label>
                <input type="text" name="timezone" value="{{ old('timezone', $general['timezone'] ?? 'Asia/Kolkata') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Support email</label>
                <input type="email" name="support_email" value="{{ old('support_email', $general['support_email'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Support phone</label>
                <input type="text" name="support_phone" value="{{ old('support_phone', $general['support_phone'] ?? '') }}"
                       class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Footer "about" text</label>
                <textarea name="footer_about" rows="3"
                          class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('footer_about', $general['footer_about'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="rounded-[var(--button-radius,9999px)] bg-[var(--color-primary)] px-6 py-2.5 text-sm font-semibold text-[var(--color-primary-foreground)] hover:opacity-90">
                Save General Settings
            </button>
        </div>
    </form>
@endsection
