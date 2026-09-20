@extends('layouts.admin')

@php
    $isEdit = $page->exists;
    $seo = $page->seo ?? new \App\Models\PageSeo();
@endphp

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- Tabs --}}
        <div class="mb-6 flex gap-2 border-b border-black/5">
            <button type="button" data-tab="content" class="tab-btn border-b-2 border-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-ink)]">
                Content
            </button>
            <button type="button" data-tab="seo" class="tab-btn border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-[var(--color-ink-muted)]">
                SEO
            </button>
        </div>

        {{-- Content panel --}}
        <div data-panel="content" class="space-y-6">
            <div class="grid gap-6 rounded-xl border border-black/5 bg-white p-6 shadow-sm md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Title</label>
                    <input type="text" name="title" required value="{{ old('title', $page->title) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Slug</label>
                    <input type="text" name="slug" required value="{{ old('slug', $page->slug) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Template</label>
                    <input type="text" name="template" value="{{ old('template', $page->template) }}" placeholder="default"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Parent page</label>
                    <select name="parent_id" class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                        <option value="">None</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_id', $page->parent_id) == $parent->id)>{{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Status</label>
                    <select name="status" class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                        <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Banner image URL</label>
                    <input type="text" name="banner_image" value="{{ old('banner_image', $page->banner_image) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Short intro</label>
                    <textarea name="short_intro" rows="2"
                              class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('short_intro', $page->short_intro) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Content</label>
                    <textarea name="content" rows="14"
                              class="w-full rounded-lg border border-black/10 px-4 py-2.5 font-mono text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('content', $page->content) }}</textarea>
                    <p class="mt-1 text-xs text-[var(--color-ink-muted)]">HTML is supported. A rich editor lands in a later phase.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Sidebar content</label>
                    <textarea name="sidebar_content" rows="4"
                              class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('sidebar_content', $page->sidebar_content) }}</textarea>
                </div>
            </div>
        </div>

        {{-- SEO panel --}}
        <div data-panel="seo" class="hidden space-y-6">
            <div class="grid gap-6 rounded-xl border border-black/5 bg-white p-6 shadow-sm md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Meta title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Meta description</label>
                    <textarea name="meta_description" rows="3"
                              class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('meta_description', $seo->meta_description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Meta keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Canonical URL</label>
                    <input type="text" name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <label class="flex items-center gap-2 text-sm text-[var(--color-ink)]">
                    <input type="checkbox" name="robots_index" value="1" @checked(old('robots_index', $seo->robots_index ?? true))
                           class="rounded border-black/20 text-[var(--color-secondary)] focus:ring-[var(--color-secondary)]/30">
                    Allow indexing
                </label>

                <label class="flex items-center gap-2 text-sm text-[var(--color-ink)]">
                    <input type="checkbox" name="robots_follow" value="1" @checked(old('robots_follow', $seo->robots_follow ?? true))
                           class="rounded border-black/20 text-[var(--color-secondary)] focus:ring-[var(--color-secondary)]/30">
                    Allow following links
                </label>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Open Graph title</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Open Graph description</label>
                    <textarea name="og_description" rows="2"
                              class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">{{ old('og_description', $seo->og_description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[var(--color-ink)]">Open Graph image URL</label>
                    <input type="text" name="og_image" value="{{ old('og_image', $seo->og_image) }}"
                           class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:border-[var(--color-secondary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-secondary)]/30">
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit"
                    class="rounded-[var(--button-radius,9999px)] bg-[var(--color-primary)] px-6 py-2.5 text-sm font-semibold text-[var(--color-primary-foreground)] hover:opacity-90">
                {{ $isEdit ? 'Save changes' : 'Create page' }}
            </button>
            <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-[var(--color-ink-muted)] hover:underline">
                Cancel
            </a>
        </div>
    </form>

    <script>
        (function () {
            var buttons = document.querySelectorAll('.tab-btn');
            var panels = document.querySelectorAll('[data-panel]');
            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = btn.getAttribute('data-tab');
                    buttons.forEach(function (b) {
                        b.classList.toggle('border-[var(--color-primary)]', b === btn);
                        b.classList.toggle('border-transparent', b !== btn);
                        b.classList.toggle('text-[var(--color-ink)]', b === btn);
                        b.classList.toggle('text-[var(--color-ink-muted)]', b !== btn);
                    });
                    panels.forEach(function (p) {
                        p.classList.toggle('hidden', p.getAttribute('data-panel') !== target);
                    });
                });
            });
        })();
    </script>
@endsection
