<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('admin.portfolio.index', [
            'items' => PortfolioItem::orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.portfolio.form', ['item' => new PortfolioItem(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        PortfolioItem::create($this->validated($request));

        return redirect()->route('admin.portfolio.index')->with('status', 'Réalisation ajoutée.');
    }

    public function edit(PortfolioItem $item): View
    {
        return view('admin.portfolio.form', compact('item'));
    }

    public function update(Request $request, PortfolioItem $item): RedirectResponse
    {
        $item->update($this->validated($request, $item));

        return redirect()->route('admin.portfolio.index')->with('status', 'Réalisation mise à jour.');
    }

    public function destroy(PortfolioItem $item): RedirectResponse
    {
        Media::delete($item->image);
        $item->delete();

        return redirect()->route('admin.portfolio.index')->with('status', 'Réalisation supprimée.');
    }

    private function validated(Request $request, ?PortfolioItem $item = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(array_keys(PortfolioItem::CATEGORIES))],
            'link' => ['nullable', 'url', 'max:500'],
            'image_file' => [$item ? 'nullable' : 'required', 'image', 'max:8192'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image_file')) {
            Media::delete($item?->image);
            $data['image'] = Media::store($request->file('image_file'), 'portfolio');
        }

        $data['position'] = $data['position'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        unset($data['image_file']);

        return $data;
    }
}
