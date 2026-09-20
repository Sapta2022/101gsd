@extends('layouts.admin')

@section('content')
    @include('admin.settings._nav', ['active' => 'appearance'])

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.appearance.update') }}">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Colors</h3>
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ([
                    'color_primary' => 'Primary (CTA)',
                    'color_primary_foreground' => 'Primary text',
                    'color_secondary' => 'Secondary (links / accents)',
                    'color_secondary_foreground' => 'Secondary text',
                    'color_ink' => 'Body text',
                    'color_ink_muted' => 'Muted text',
                    'color_surface' => 'Surface (light)',
                    'color_surface_dark' => 'Surface (dark)',
                ] as $key => $label)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">{{ $label }}</label>
                        <div class="flex items-center gap-3">
                            <input type="color" value="{{ old($key, $appearance[$key] ?? '#000000') }}"
                                   oninput="this.nextElementSibling.value = this.value"
                                   class="h-10 w-14 flex-shrink-0 cursor-pointer rounded border border-black/10 p-1">
                            <input type="text" name="{{ $key }}" value="{{ old($key, $appearance[$key] ?? '') }}"
                                   oninput="this.previousElementSibling.value = this.value"
                                   class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-black/5 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[var(--color-ink-muted)]">Typography &amp; layout</h3>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Heading font</label>
                    <input type="text" name="font_heading" value="{{ old('font_heading', $appearance['font_heading'] ?? 'Playfair Display') }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Body font</label>
                    <input type="text" name="font_body" value="{{ old('font_body', $appearance['font_body'] ?? 'Instrument Sans') }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Button radius</label>
                    <input type="text" name="button_radius" value="{{ old('button_radius', $appearance['button_radius'] ?? '9999px') }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Container width</label>
                    <input type="text" name="container_width" value="{{ old('container_width', $appearance['container_width'] ?? '1280px') }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="rounded-[var(--button-radius,9999px)] bg-[var(--color-primary)] px-6 py-2.5 text-sm font-semibold text-[var(--color-primary-foreground)] hover:opacity-90">
                Save Appearance
            </button>
        </div>
    </form>
@endsection
