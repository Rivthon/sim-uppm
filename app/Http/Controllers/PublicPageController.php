<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePublicPageRequest;
use App\Models\PublicPage;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        $pages = PublicPage::query()->with('parent:id,title')->withCount('children')
            ->orderByRaw('parent_id IS NOT NULL')->orderBy('parent_id')->orderBy('sort_order')->orderBy('title')->get();

        return view('public-pages.index', compact('pages'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        return view('public-pages.create', ['parents' => $this->parentOptions()]);
    }

    public function store(SavePublicPageRequest $request): RedirectResponse
    {
        $page = PublicPage::create([
            ...$request->safe()->except('is_published'),
            'is_published' => $request->boolean('is_published'),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('public-pages.edit', $page)->with('success', 'Halaman publik berhasil dibuat.');
    }

    public function show(PublicPage $publicPage): View
    {
        abort_unless($publicPage->is_published || auth()->user()?->hasRole('admin_uppm'), 404);
        $publicPage->load([
            'parent:id,title,slug',
            'parent.children' => fn ($query) => $query->where('is_published', true),
            'children' => fn ($query) => $query->where('is_published', true),
        ]);
        $settings = SiteSetting::query()->first() ?? new SiteSetting(SiteSetting::defaults());
        $menuPages = PublicPage::query()->whereNull('parent_id')->where('is_published', true)
            ->with(['children' => fn ($query) => $query->where('is_published', true)])
            ->orderBy('sort_order')->orderBy('title')->get();

        return view('public-pages.show', compact('publicPage', 'settings', 'menuPages'));
    }

    public function edit(PublicPage $publicPage): View
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);

        return view('public-pages.edit', ['publicPage' => $publicPage, 'parents' => $this->parentOptions($publicPage)]);
    }

    public function update(SavePublicPageRequest $request, PublicPage $publicPage): RedirectResponse
    {
        $publicPage->update([
            ...$request->safe()->except('is_published'),
            'is_published' => $request->boolean('is_published'),
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Halaman publik berhasil diperbarui.');
    }

    public function destroy(PublicPage $publicPage): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin_uppm'), 403);
        $publicPage->delete();

        return redirect()->route('public-pages.index')->with('success', 'Halaman publik berhasil dihapus.');
    }

    /** @return Collection<int, PublicPage> */
    private function parentOptions(?PublicPage $excludedPage = null): Collection
    {
        return PublicPage::query()->whereNull('parent_id')
            ->when($excludedPage, fn ($query) => $query->whereKeyNot($excludedPage->id))
            ->orderBy('sort_order')->orderBy('title')->get(['id', 'title']);
    }
}
