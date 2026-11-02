<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->when($request->filled('q'), fn ($query) => $query->where('nama_kategori', 'like', '%'.$request->q.'%'))
            ->orderByDesc('is_active')
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Kategori tidak dihapus permanen agar laporan lama tetap terhubung, hanya diaktifkan atau dinonaktifkan.
     */
    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        $message = $category->is_active ? 'Kategori diaktifkan kembali.' : 'Kategori dinonaktifkan.';

        return redirect()->route('admin.categories.index')->with('success', $message);
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'nama_kategori')->ignore($category?->id),
            ],
        ]);
    }
}
