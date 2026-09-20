@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <form method="GET" class="flex items-center gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search pages..."
                   class="w-64 rounded-lg border border-black/10 px-4 py-2 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
            <button type="submit" class="rounded-lg border border-black/10 px-4 py-2 text-sm font-medium text-[var(--color-ink-muted)] hover:bg-black/5">
                Search
            </button>
        </form>

        <a href="{{ route('admin.pages.create') }}"
           class="rounded-[var(--button-radius,9999px)] bg-[var(--color-primary)] px-5 py-2.5 text-sm font-semibold text-[var(--color-primary-foreground)] hover:opacity-90">
            + New Page
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-black/5 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-black/[0.02] text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">
                <tr>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Slug</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Updated</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($pages as $page)
                    <tr>
                        <td class="px-6 py-4 font-medium text-[var(--color-ink)]">
                            {{ $page->title }}
                            @if ($page->is_system)
                                <span class="ml-2 rounded-full bg-black/5 px-2 py-0.5 text-[10px] uppercase text-[var(--color-ink-muted)]">system</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-[var(--color-ink-muted)]">/{{ $page->slug }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $page->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-black/5 text-[var(--color-ink-muted)]' }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[var(--color-ink-muted)]">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="font-medium text-[var(--color-secondary)] hover:underline">Edit</a>
                            @unless ($page->is_system)
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline" onsubmit="return confirm('Delete this page?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-4 font-medium text-red-600 hover:underline">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-[var(--color-ink-muted)]">
                            No pages yet — create the first one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pages->links() }}
    </div>
@endsection
