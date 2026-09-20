<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $pages = Page::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function create(): View
    {
        return view('admin.pages.form', [
            'page' => new Page(['status' => 'draft']),
            'parents' => Page::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $page = Page::create($data);

        $this->syncSeo($page, $request);
        $this->recordRevision($page);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page created.');
    }

    public function edit(Page $page): View
    {
        $page->loadMissing('seo');

        return view('admin.pages.form', [
            'page' => $page,
            'parents' => Page::query()->where('id', '!=', $page->id)->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request, $page);

        $page->update($data);

        $this->syncSeo($page, $request);
        $this->recordRevision($page);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page saved.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->is_system) {
            return back()->with('status', 'System pages can\'t be deleted.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash',
                'unique:pages,slug,'.($page->id ?? 'NULL').',id'],
            'banner_image' => ['nullable', 'string', 'max:2048'],
            'short_intro' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sidebar_content' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    private function syncSeo(Page $page, Request $request): void
    {
        $seo = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'robots_index' => ['nullable', 'boolean'],
            'robots_follow' => ['nullable', 'boolean'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:2048'],
        ]);

        $seo['robots_index'] = $request->boolean('robots_index');
        $seo['robots_follow'] = $request->boolean('robots_follow');

        $page->seo()->updateOrCreate(['page_id' => $page->id], $seo);
    }

    private function recordRevision(Page $page): void
    {
        PageRevision::create([
            'page_id' => $page->id,
            'user_id' => auth()->id(),
            'snapshot' => $page->fresh('seo')->toArray(),
        ]);
    }
}
