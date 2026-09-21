<div class="mb-6 flex gap-2 border-b border-black/5">
    <a href="{{ route('admin.settings.general') }}"
       class="border-b-2 px-4 py-3 text-sm font-semibold {{ $active === 'general' ? 'border-[var(--color-primary)] text-[var(--color-ink)]' : 'border-transparent text-[var(--color-ink-muted)]' }}">
        General
    </a>
    <a href="{{ route('admin.settings.appearance') }}"
       class="border-b-2 px-4 py-3 text-sm font-semibold {{ $active === 'appearance' ? 'border-[var(--color-primary)] text-[var(--color-ink)]' : 'border-transparent text-[var(--color-ink-muted)]' }}">
        Appearance
    </a>
</div>
